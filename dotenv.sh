#!/bin/bash

# APP input
read -p $'
Please type your app_name
 - lowercase
 - alphanumeric
 - underscores
 - maxlength 50 characters

APP: ' APP

# DB_HOST input
read -p $'
Please enter your database host
Example: mysql.example.com:25060
                               
DB_HOST: ' DB_HOST          

ENV=""
ENV="${ENV}APP=${APP}\n"
ENV="${ENV}\n"
ENV="${ENV}# https://cloud.digitalocean.com/databases\n"
ENV="${ENV}DB_HOST=${DB_HOST}\n"
ENV="${ENV}DB_USER=${APP}\n"
ENV="${ENV}DB_PASSWORD=$(pwgen -s 20)\n"
ENV="${ENV}DB_PREFIX=wp\n"
ENV="${ENV}\n"
ENV="${ENV}# Leave DB_NAME unset in this file. DB_NAME is automatically set to \"\${APP}_\${WP_ENV}\"\n"
ENV="${ENV}# DB_NAME=\n"
ENV="${ENV}\n"
ENV="${ENV}# Leave WP_ENV unset in this file. WP_ENV is set in docker-compose.yml\n"
ENV="${ENV}# WP_ENV=\n"
ENV="${ENV}\n"
ENV="${ENV}# https://cloud.digitalocean.com/account/api/tokens\n"
ENV="${ENV}S3_UPLOADS_SPACE=\n"
ENV="${ENV}S3_UPLOADS_REGION=\n"
ENV="${ENV}S3_UPLOADS_BUCKET=${APP}\n"
ENV="${ENV}S3_UPLOADS_KEY=\n"
ENV="${ENV}S3_UPLOADS_SECRET=\n"
ENV="${ENV}S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL=false\n"

echo -e $ENV > /.env
