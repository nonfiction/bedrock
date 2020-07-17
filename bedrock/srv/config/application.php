<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;

// Directory containing all of the site's files
$root_dir = dirname(__DIR__);

// Document Root
$webroot_dir = $root_dir . '/web';

// Expose global env() function from oscarotero/env
Env::init();

// Use Dotenv to set required environment variables and load .env file in root
$dotenv = Dotenv\Dotenv::create($root_dir);
if (file_exists($root_dir . '/.env')) {
  $dotenv->load();
  $dotenv->required(['APP_NAME', 'APP_HOST']);
  if (!env('DATABASE_URL')) {
    $dotenv->required(['DB_HOST', 'DB_PASSWORD']);
  }
}

// Salts are generated in a separate .env file
$dotenv_salts = Dotenv\Dotenv::create($root_dir, 'salts.env');
if (file_exists($root_dir . '/salts.env')) {
  $dotenv_salts->load();
}

// Combined app name and host is used as database name and s3 bucket
// my-app.DEV1.example.com => my_app_dev1_example_com
$APP_NAME_HOST = env('APP_NAME') . '_' . env('APP_HOST');
$APP_NAME_HOST = strtolower( $APP_NAME_HOST );
$APP_NAME_HOST = str_replace( [' ', '-', '.'], '_', $APP_NAME_HOST );
Config::define('APP_NAME_HOST', $APP_NAME_HOST);


// Set up our global environment constant and load its config first
// Default: production
define('WP_ENV', env('WP_ENV') ?: 'production');

// Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
// See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
  $_SERVER['HTTPS'] = 'on';
}

// URLs
if (env('WP_HOME')) {
  Config::define('WP_HOME', env('WP_HOME'));
  Config::define('WP_SITEURL', env('WP_SITEURL') ?: env('WP_HOME') . '/wp');
} else {
  $http_or_https = ( isset( $_SERVER['HTTPS'] ) ) ? 'https' : 'http';
  Config::define('WP_HOME', "${http_or_https}://" . env('HTTP_HOST'));
  Config::define('WP_SITEURL', "${http_or_https}://" . env('HTTP_HOST') . '/wp');
}

// Custom Content Directory
Config::define('WEBROOT_DIR', $webroot_dir);
Config::define('CONTENT_DIR', '/app');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

// DB settings
$DB_NAME_ENV = strtoupper('DB_NAME_'.WP_ENV);
Config::define('DB_NAME', env('DB_NAME') ?: (env($DB_NAME_ENV) ?: $APP_NAME_HOST));
$DB_USER = strtolower(str_replace( [' ', '-', '.'], '_', env('APP_NAME') ));
Config::define('DB_USER', env('DB_USER') ?: $DB_USER);
Config::define('DB_PASSWORD', env('DB_PASSWORD'));
Config::define('DB_HOST', env('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

if (env('DATABASE_URL')) {
  $dsn = (object) parse_url(env('DATABASE_URL'));
  Config::define('DB_NAME', substr($dsn->path, 1));
  Config::define('DB_USER', $dsn->user);
  Config::define('DB_PASSWORD', isset($dsn->pass) ? $dsn->pass : null);
  Config::define('DB_HOST', isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}

// S3 settings
if (env('S3_UPLOADS_SPACE')) {

  Config::define('S3_UPLOADS_SPACE', env('S3_UPLOADS_SPACE'));
  Config::define('S3_UPLOADS_REGION', env('S3_UPLOADS_REGION'));
  Config::define('S3_UPLOADS_ENDPOINT', 'https://'.env('S3_UPLOADS_REGION').'.digitaloceanspaces.com');

  $BUCKET_ENV = strtoupper('S3_UPLOADS_BUCKET_'.WP_ENV);
  $BUCKET = env('S3_UPLOADS_BUCKET') ?: (env($BUCKET_ENV) ?: $APP_NAME_HOST);

  Config::define('S3_UPLOADS_BUCKET', env('S3_UPLOADS_SPACE')."/${BUCKET}");
  $BUCKET_URL = env('S3_UPLOADS_CUSTOM_ENDPOINT') ?: 'https://'.env('S3_UPLOADS_SPACE').'.'.env('S3_UPLOADS_REGION').".digitaloceanspaces.com";
  Config::define('S3_UPLOADS_BUCKET_URL', "${BUCKET_URL}/${BUCKET}");

  Config::define('S3_UPLOADS_KEY', env('S3_UPLOADS_KEY'));
  Config::define('S3_UPLOADS_SECRET', env('S3_UPLOADS_SECRET'));

  Config::define('S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL', env('S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL'));
  Config::define('S3_UPLOADS_AUTOENABLE', env('S3_UPLOADS_AUTOENABLE'));

}

// Authentication Unique Keys and Salts
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

// Custom Settings
Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: true);
// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT', true);
// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

// Query Monitor Settings
Config::define('QM_DARK_MODE', env('QM_DARK_MODE') ?: true);
Config::define('QM_DISABLE_ERROR_HANDLER', env('QM_DISABLE_ERROR_HANDLER') ?: true);

// Debugging Settings
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');

// Environment configs
$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';
if (file_exists($env_config)) {
  require_once $env_config;
}

Config::apply();

// Bootstrap WordPress
if (!defined('ABSPATH')) {
  define('ABSPATH', $webroot_dir . '/wp/');
}
