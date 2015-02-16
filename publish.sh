#!/bin/bash

export REPO_SLUG="Symbid/dev-blog"

#export TRAVIS_REPO_SLUG="Symbid/dev-blog"
#export TRAVIS_PULL_REQUEST="false"
#export TRAVIS_BRANCH="master"
#export TRAVIS_BUILD_DIR=`pwd`

echo -e "Running details: $TRAVIS_REPO_SLUG / $TRAVIS_PULL_REQUEST / $TRAVIS_BRANCH / $TRAVIS_BUILD_DIR \n"

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
