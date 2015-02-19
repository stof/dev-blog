---
title: New Package: Symbid Chainlink
categories:
    - oss
    - php
tags:
    - composer
    - packagist
    - chain of responsibility
    - open source
    - package
authors:
    - rdohms
draft: true
---

As a development team we have always believed in open source and sharing as much as we can with the community. Following in this belief we have decided to start sharing some of our independent pieces of code, starting with *Chainlink*.

Keep an eye on the *Symbid* namespace and at [packagist](http://packagist.com/package/symbid), for we hope to share even more in the future.


## Chainlink

Chainlink is a very simple package on its own, its a lightweight implementation of the chain of responsibility pattern, often referred to as "speed dating" or "delegation loop".

The Context allows you to attach any number of handlers to it and will then search over these until it finds one (or more) that can handle the given input.

~~~php
<?php
    class MyHandler implements HandlerInterface
    {
        // ... fulfill interface ...
    }

    $handler = new MyHandler();

    // Create a Context to chain responsibilities
    $context = new Symbid\Chainlink\Context();
    $context->addHandler($handler);

    // Pass in an item to be handled
    $context->handle($input);

    // You can also get the handler as a return value
    $handler = $context->getHandlerFor($input);

    // You may have need of returning multiple handlers
    $handler = $context->getAllHandlersFor($input);
~~~

Where *Chainlink* really shines is in providing integration to popular frameworks. Over the years developing our products at Symbid we found ourselves writing code like this over and over. A service that would look for other *tagged services* and attach them to it, adding handlers to a Context.

Our Chainlink Bundle makes this so much easier, you can simple declare in `config.yml` the name of the contexts and which tag their handlers use and it will auto wire them all for you, making using it a breeze.

~~~yaml
# In your config.yml
symbid_chainlink:
    contexts:
        my_new_context:
            tag: mycontext.handler

# In your services.yml
services:
    my_bundle.handler.raging_handler:
        class: MyBundle/Handler/RagingHandler
        tags:
            - { name: mycontext.handler }
~~~

and then just use it

~~~php
$this->container->get('my_new_context')->handle($input);
~~~

This allows you to quickly spin up new chains of responsibility and focus on the really important stuff, your handlers, not your wiring and boilerplate.

Here is how you can find them:

```
composer require symbid/chainlink
```

or for the symfony bundle

```
composer require symbid/chainlink-bundle
```

* Chainlink: [repository](http://github.com/Symbid/chainlink) | [packagist](https://packagist.org/packages/symbid/chainlink)
* Chainlink Bundle: [repository](http://github.com/Symbid/chainlink-bundle) | [packagist](https://packagist.org/packages/symbid/chainlink-bundle)

We are wide open to PRs and contributions, feel free to use github for that, also let us know if you are interested in writing a wrapper for you preferred framework.
