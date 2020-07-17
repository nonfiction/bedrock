# bedrock

## Development

Common commands are available via Makefile. For example:

#### Add a WordPress plugin or theme from wpackagist.org

```
make plugin add=wordpress-seo
make theme add=hueman
```

#### Add an NPM package from npmjs.com

```
make package add=normalize.css
```

#### Compile Assets with Webpack

```
make assets
```

#### Launch in development mode with HMR

```
make up
```

#### Launch in production mode with compiled assets

```
make upp
```

#### Watch the logs

```
make logs
```

#### Stop the container

```
make down
```

#### Choose remote host

```
make remote
```

#### Deploy to remote production

```
make up!
```

#### Watch the production logs

```
make logs!
```

#### Stop the production container

```
make down!
```

#### Pull the database and upload files from production to development

```
make pull
```

#### Push the database and upload files from development to production

```
make push
```

#### Export the database and upload files from development to a backup

```
make export
```

#### Import the database and upload file backups into development

```
make import
```

Note: composer installation happens during Docker build, so any
new WordPress plugins or themes require a fresh Docker build.

## WP-Cli

```
docker-compose run wp core version
docker-compose run wp search-replace <old> <new>
```

## Backend

<https://hub.docker.com/repository/docker/nonfiction/bedrock/>

- **bedrock:srv** Wordpress on Apache (composer, wpcli)
- **bedrock:dev** Webpack Dev Server (npm)
- **bedrock:env** MySQL Client and .env generator (ruby, thor)
