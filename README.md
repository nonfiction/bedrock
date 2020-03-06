# bedrock

A foundation for setting up new Wordpress sites, highly influenced by
<https://github.com/roots/bedrock>

## Docker Hub

<https://hub.docker.com/repository/docker/nonfiction/bedrock/>

- **bedrock:web** Wordpress on Apache (composer, wpcli)
- **bedrock:dev** Webpack Dev Server (npm)
- **bedrock:env** MySQL Client and .env generator (ruby, thor)

## Development

```
docker-compose build
docker-compose push
```

## Installation

See <https://github.com/nonfiction/bedrock-site> for installation and usage.
