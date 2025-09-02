# Icecast status periodic skimmer

## Requirements

### Web reverse proxy

If you don't already have a docker web reverse proxy service (ex: traefik), you must start it
```sh
$ docker stack deploy -c .docker/docker-compose-reverse-proxy.yml traefik
```

## Installation

From Git

[IDCI-Consulting gitlab resource]
```sh
git clone git@gitlab.idci-consulting.fr:liodie/icecast-status-periodic-skimmer.git
```

Add the following DNS entries in your host file:
```
# Icecast status periodic skimmer
127.0.0.1       isps.docker
```

If you need to rebuild docker app images, run the following command :
```sh
$ make build-images
```

## Start

To run the project docker stack :
```sh
$ make start
```

## Stop

To stop the project docker stack :
```sh
$ make stop
```

## Build assets

To build the assets, run the following commands :
```sh
$ make yarn
$ make encore
```



