# Symbid Development Blog

## Setup

Get Sculpin and optionally put it in your bin folder

```sh
curl -O https://download.sculpin.io/sculpin.phar
sudo mv sculpin.phar /usr/bin/sculpin
```

Get dependencies and setup

```sh
sculpin install
```

To run it locally and test it (dev mode)

```sh
sculpin generate --watch --server
```

## Updating

Blog will automatically update after anything is merged into master.
Using Travis-CI.