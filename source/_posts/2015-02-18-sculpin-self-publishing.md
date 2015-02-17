---
title: Self Publishing Sculpin blog with Github and TravisCI
categories:
    - oss
    - php
tags:
    - sculpin
    - travisCI

draft: true
---

*"We need a Tech blog, what should we use?"*

This was the topic of our monday morning post-standup discussion. It was time to start sharing some of our content and we needed a place for that. It was up to our team to decide how we would get it done.

The parameters and requirements came out pretty quick: *"Not Wordpress"*, *"Markdown please"*, *"No server maintenance"*, *"Developers only do git"*, *"It has to be pretty"*, ok not really, no one said it should be pretty. I quickly took these requirements and started giving it some thought. The obvious choice was Jekyll and OctoPress, but I had wanted to try out [Sculpin](http://sculpin.io) for a while, it was php, the core dev is a friend, this was a good chance.

So we went for it, decided on Sculpin and decided to host it on Github Pages so we would have no work in keeping it up or scaling it. That was the challenge, since Jekyll is Github powered we would get that out of the box, but no PHP love from them, so we would have to find a way. I toyed with the idea of hosting something on Heroku to do it, or somewhere else, but that would be yet another moving part. [@Wouterjnl](https://twitter.com/wouterjnl) mentioned on twitter that he had done this using [TravisCI](http://travis-ci.org) builds and he would share the idea at some point.

I suffer from *"I can't wait"* disorder so I started hacking on some ideas on some free time. Reading some similar ideas of combining TravisCI and other static site generators I slowly pieced together the recipe below. 

The first piece was to setup github. That's pretty easy but just so you know how it works, basically you push static html to a `gh-pages` branch and github will host those files for you. So this was my final target.

This is what the workflow looked like:

1. Author writes a post.
1. Author opens a pull rewuest.
1. Merge into `gh-pages` will trigger a TravisCI build.
1. TravisCI build will generate static site.
1. Travis commits the new code into `gh-pages` and pushes.
1. Github hosts the files.

The two missing pieces were to tell TravisCI to build the static page and make it commit the new code. While this is all possible directly in the `.travis.yml` file I separated into two parts to make it easier to follow.

This is what my `.travis.yml` looks like:

```yml
language: php
php:
    - 5.5
env:
    global:
        - secure: <secure vars>

before_script:
    - composer config -g github-oauth.github.com $GH_TOKEN
    - curl -O https://download.sculpin.io/sculpin.phar
    - php sculpin.phar install

script:
    - php sculpin.phar generate --env=prod

after_script:
    - ./publish.sh
```

Two things to be noted there. One, notice that I download and run sculpin directly in Travis, less code in my repo. The second is the `secure` vars, this is simple, its encrypted code that contains my Github token to allow me to execute the next steps, and avoid API limits on `sculpin install`. To configure sculpin and composer to use this I run the `composer config` command.

To generate this bit you need to run something like `travis encrypt GH_TOKEN=<your token> -r <org>/<reponame> --add`.
    
Final step now was to setup a script that is able to take the new code and push it up to Github. This is where publish.sh comes in and what it looks like:

```sh
#!/bin/bash

# Configure your repository name
export REPO_SLUG="<org>/<reponame>"

echo -e "Running details: $TRAVIS_REPO_SLUG / $TRAVIS_PULL_REQUEST / $TRAVIS_BRANCH / $TRAVIS_BUILD_DIR \n"

# Only run if its master, avoids pushing unfinished posts
if [ "$TRAVIS_REPO_SLUG" == $REPO_SLUG ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_BRANCH" == "master" ]; then

  echo -e "Checks passed, getting ready to update blog ...\n"

  echo -e "Checking out gh-pages branch...\n"

  cd $TRAVIS_BUILD_DIR
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "travis-ci"
  git clone --quiet --branch=gh-pages https://${GH_TOKEN}@github.com/${REPO_SLUG} gh-pages > /dev/null
  if [ $? -ne 0 ]; then echo "Could not clone the repository"; exit 1; fi

  echo -e "Syncronizing content...\n"
  rsync -rtv --delete ./output_prod/* ./gh-pages
  if [ $? -ne 0 ]; then echo "Could not sync directories"; exit 1; fi

  echo -e "Commiting changes...\n"
  cd gh-pages
  git add --all .
  git commit -m "Publishing latest changes to blog from build $TRAVIS_COMMIT (Build #$TRAVIS_BUILD_NUMBER) to gh-pages"
  git push -fq origin gh-pages > /dev/null
  if [ $? -ne 0 ]; then echo "Failed to push changes"; exit 1; fi

  echo -e "Latest blog update pushed to gh-pages.\n"

fi
```

Notice I added a few safeguards, you don't really need to since you can configure Travis to only run on master, but this setup allows me to have "pre-builds" that can let me know if compilation was successful. It does not do much, mostly it will clone the `gh-pages` branch, `rsync` the generated content into the `gh-pages` folder and commit the changes.

That's all, except the testing and tweaking until we had it down to a science. I hope you can make use of this learning experience and get your sculpin blogs self publishing.

**Interested in taking this a step further?** I wold suggest adding a "sync with gh-pages" command to sculpin and then you can run it all in one command.
