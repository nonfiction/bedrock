#!/usr/bin/env ruby

require "thor"
require "dotenv/load"
require 'digest'
require 'json'
require 'open3'
require "yaml"

class MyThorCommand < Thor
  include Thor::Actions
  def self.exit_on_failure?
    true
  end

  no_commands do

    def bold(text) 
      set_color(" #{text} ", :black, :on_green, :bold)
    end

    def clean(*text)
      text.join(' ')
        .tr(' ','-')
        .strip
        .downcase
    end

    def slug(*text)
      text.join(' ')
        .strip
        .downcase
        .tr(' ','_')
        .tr('-','_')
        .tr('.','_')
    end

    def input(key, echo = true)
      label = set_color(" #{key} ", :black, :on_green, :bold)
      response = ask label, :echo => echo
      clean(response).to_s
    end

    def input_secure(key)
      input(key, false)
    end

    def input_as_is(key, echo = true)
      label = set_color(" #{key} ", :black, :on_green, :bold)
      response = ask label, :echo => echo
      response.to_s.strip
    end

    def msg(*text)
      say(text.join(' '), :green)
    end

    def msg_env(key)
      label = set_color(" #{key} ", :black, :on_blue, :bold)
      say "#{label} #{ENV[key]}"
    end

    def password(text, salt, max_length = 20)
      hash = Digest::SHA512.hexdigest(text + salt)
      hash[0..max_length]
    end

    def choose(prompt = "", choices = [])
      env_APP_NAME
      args = [
        "--clear",
        "--output-fd 1",
        "--title \"#{ENV['APP_NAME']}\"",
        "--menu \"#{prompt}\"",
        '15 40 4'
      ]
      opts = choices.map.with_index do |choice, index|
        "#{index + 1} \"#{choice}\""
      end
      chosen_index = cli("dialog #{args.join(' ')} #{opts.join(' ')}", false)
      return chosen_index.to_i - 1
    end

    def default_package_name
      'bedrock-site'
    end

    # Pull app_name from package.json (if available)
    def name_from_package
      return false unless File.file? 'package.json'
      name = JSON.parse(File.read('package.json'))['name'] or false
      return false if name == default_package_name # exclude default
      name
    end

    def cli(command, echo = true)
      if echo
        say "#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color(command, :yellow)}"
      end

      out, err, stat = Open3.capture3(command)

      if echo
        # say(err.chomp, :red) unless err.chomp.empty?
        out = out.chomp
        say(out, :white) unless out.empty?
      end

      return out
    end

    # MySQL base command
    def mysql(command, echo = true)
      say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color(command, :yellow)}") if echo

      user = ENV['DB_ROOT_USER']
      pass = ENV['DB_ROOT_PASSWORD']
      host = ENV['DB_HOST'].split(':')[0]
      port = ENV['DB_HOST'].split(':')[1]

      base = "mysql -h#{host} -P#{port} -u#{user} -p#{pass} -e"
      return cli("#{base} \"#{command}\"", false)
    end


    def mysql_migrate(source_db, dest_db, echo = true)

      # Ensure source_db and dest_db are not both .sql files
      if (source_db.include? ".sql" and dest_db.include? ".sql")
        say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("Source and destination cannot both be *.sql files", :yellow)}")
        return false
      end

      # Ensure source_db and dest_db do not match
      if (source_db == dest_db)
        say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("Databases match", :yellow)}")
        return false
      end

      say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("#{source_db.gsub('/srv/','')} -> #{dest_db.gsub('/srv/','')}", :yellow)}") if echo

      user = ENV['DB_USER']
      pass = ENV['DB_PASSWORD']
      host = ENV['DB_HOST'].split(':')[0]
      port = ENV['DB_HOST'].split(':')[1]

      # Commands with credentials ready
      mysql = "mysql -h#{host} -P#{port} -u#{user} -p#{pass}"
      mysqldump = "mysqldump -h#{host} -P#{port} -u#{user} -p#{pass} --set-gtid-purged=OFF"

      # Perform an SQL import from source_db file.sql to dest_db database
      if source_db.include? ".sql"
        say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("IMPORTING...", :yellow)}")
        return cli( "#{mysql} #{dest_db} < #{source_db}", false )
      end

      # Perform an SQL export from source_db database to dest_db file.sql
      if dest_db.include? ".sql"
        say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("EXPORTING...", :yellow)}")
        return cli( "#{mysqldump} #{source_db} > #{dest_db}", false )
      end

      # Perform an SQL export from source_db database to dest_db database
      say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("MIGRATING...", :yellow)}")
      return cli( "#{mysqldump} #{source_db} | #{mysql} #{dest_db}", false )

    end


    def env_DB_HOST
      key = 'DB_HOST'
      did_input = false
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter your database host"
        msg "Example: mysql.example.com:25060"
        ENV[key] = input key
        did_input = true
      end
      msg_env key unless did_input
      !ENV[key].to_s.empty?
    end

    def env_DB_ROOT_PASSWORD
      key = 'DB_ROOT_PASSWORD'
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter your database #{ENV['DB_ROOT_USER'].upcase} password"
        msg "https://cloud.digitalocean.com/databases"
        ENV[key] = input_secure(key)
        puts "\n"
      end
      !ENV[key].to_s.empty?
    end

    def env_APP_NAME
      key = 'APP_NAME'

      if ENV[key].to_s.empty?

        name = name_from_package
        if name
          ENV[key] = clean(name).to_s
          msg_env key

        else
          puts "\n"
          msg "Enter your #{key}:"
          msg " - lowercase"
          msg " - alphanumeric"
          msg " - underscore"
          msg " - maxlength 50 characters"
          ENV[key] = input(key)
        end

      else
        msg_env key
      end
      !ENV[key].to_s.empty?
    end

    def env_APP_HOST
      key = 'APP_HOST'
      did_input = false
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter your APP_HOST"
        msg "Example: dev1.example.com"
        ENV[key] = input key
        did_input = true
      end
      msg_env key unless did_input
      !ENV[key].to_s.empty?
    end

    def env_APP_NAME_HOST
      key = 'APP_NAME_HOST'
      if ENV[key].to_s.empty?
        if !ENV['APP_NAME'].to_s.empty? and !ENV['APP_HOST'].to_s.empty?
          ENV[key] = slug(ENV['APP_NAME'], ENV['APP_HOST'])
        end
      end
      !ENV[key].to_s.empty?
    end

    def env_DEPLOY_HOST
      key = 'DEPLOY_HOST'
      if ENV[key].to_s.empty?
        ENV[key] = ENV['APP_HOST']
      end
      !ENV[key].to_s.empty?
    end

    def env_DEPLOY_HOSTS
      key = 'DEPLOY_HOSTS'
      deploy_hosts = [ENV['DEPLOY_HOSTS']]
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter a DEPLOY_HOST (optional)"
        msg "Example: app1.example.com"
        deploy_hosts << input(key).strip
      else
        deploy_hosts << ENV[key].strip
      end
      ENV[key] = deploy_hosts.compact.reject(&:empty?).uniq.join(',').to_s  
      msg_env key
      !ENV[key].to_s.empty?
    end

    def env_DB_ROOT_USER
      key = 'DB_ROOT_USER'
      if ENV[key].to_s.empty?
        ENV[key] = 'doadmin'
      end
      !ENV[key].to_s.empty?
    end

    def env_DB_ADMIN_USER
      key = 'DB_ADMIN_USER'
      if ENV[key].to_s.empty?
        ENV[key] = 'nonfiction'
      end
      !ENV[key].to_s.empty?
    end

    def env_DB_USER
      key = 'DB_USER'
      if ENV[key].to_s.empty?
        ENV[key] = slug(ENV['APP_NAME'])
      end
      msg_env key
      !ENV[key].to_s.empty?
    end

    def env_DB_PASSWORD
      key = 'DB_PASSWORD'
      if ENV[key].to_s.empty?
        if !ENV['APP_NAME'].to_s.empty? and !ENV['DB_ROOT_PASSWORD'].to_s.empty?
          ENV[key] = password(ENV['APP_NAME'], ENV['DB_ROOT_PASSWORD'])
        end
      end
      msg_env key
      !ENV[key].to_s.empty?
    end

    def env_S3_UPLOADS_KEY
      key = 'S3_UPLOADS_KEY'
      did_input = false
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter your S3_UPLOADS_KEY (optional)"
        msg "https://cloud.digitalocean.com/account/api/tokens"
        ENV[key] = input_as_is key
        did_input = true
      end
      msg_env key unless did_input
      !ENV[key].to_s.empty?
    end

    def env_S3_UPLOADS_SECRET
      key = 'S3_UPLOADS_SECRET'
      did_input = false
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter your S3_UPLOADS_SECRET (optional)"
        msg "https://cloud.digitalocean.com/account/api/tokens"
        ENV[key] = input_as_is key
        did_input = true
      end
      msg_env key unless did_input
      !ENV[key].to_s.empty?
    end

  end


  desc "list", "List Commands"
  def list
    puts "dotenv deploy_host db_create db_export db_import db_push db_pull"
  end


  desc "dotenv", "Generates .env"
  def dotenv
    say bold(".env GENERATOR")

    return unless env_DB_HOST
    return unless env_DB_ROOT_USER
    return unless env_DB_ROOT_PASSWORD

    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_DEPLOY_HOSTS

    env_APP_NAME_HOST
    env_DB_ADMIN_USER

    return unless env_DB_USER
    return unless env_DB_PASSWORD

    if env_S3_UPLOADS_KEY
      env_S3_UPLOADS_SECRET 
    end	

    # Update name in package.json, docker-compose.yml
    ['package.json', 'package-lock.json', 'docker-compose.yml'].each do |filename|
      if File.file? filename
        text = File.read(filename).gsub(/#{default_package_name}/, ENV['APP_NAME'])
        msg "Writing #{filename} with new name: #{ENV['APP_NAME']}"
        create_file filename, text, :force => true
      end
    end

    # Build .env configuration
    dotenv = <<~DOTENV.strip
      # https://#{ENV['APP_NAME']}.#{ENV['APP_HOST']}/wp/wp-login.php
      # Username: nf-#{ENV['APP_NAME']}
      # Password: #{ENV['DB_PASSWORD']}
      APP_NAME=#{ENV['APP_NAME']}
      APP_HOST=#{ENV['APP_HOST']}

      # Comma-separated remote docker hosts for deployment
      DEPLOY_HOSTS=#{ENV['DEPLOY_HOSTS']}
      DEPLOY_HOST=#{ENV['DEPLOY_HOSTS']}

      # Publically accessible host name                                                                                                                                                             
      PUBLIC_HOST=

      # https://cloud.digitalocean.com/databases
      DB_HOST=#{ENV['DB_HOST']}
      DB_USER=#{ENV['DB_USER']}
      DB_PASSWORD=#{ENV['DB_PASSWORD']}

      # When DB_NAME is undefined, it's automatically set to "${APP_NAME}_${APP_HOST}"
      # DB_NAME forces the database name, regardless of WP_ENV or APP_HOST
      # DB_NAME=#{ENV['APP_NAME_HOST']}

      # DB_NAME_DEVELOPMENT forces the database name in WP_ENV=development, regardless of APP_HOST
      # DB_NAME_DEVELOPMENT=#{ENV['APP_NAME_HOST']}

      # DB_NAME_STAGING forces the database name in WP_ENV=staging, regardless of APP_HOST
      # DB_NAME_STAGING=#{ENV['APP_NAME_HOST']}

      # DB_NAME_PRODUCTION forces the database name in WP_ENV=production, regardless of APP_HOST
      # DB_NAME_PRODUCTION=#{ENV['APP_NAME_HOST']}

      # https://cloud.digitalocean.com/account/api/tokens
      S3_UPLOADS_SPACE=nonfiction
      S3_UPLOADS_REGION=sfo2
      S3_UPLOADS_KEY=#{ENV['S3_UPLOADS_KEY']}
      S3_UPLOADS_SECRET=#{ENV['S3_UPLOADS_SECRET']}
      S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL=false
      # S3_UPLOADS_CUSTOM_ENDPOINT=

      # When S3_UPLOADS_BUCKET is undefined, it's automatically set to "${APP_NAME}_${APP_HOST}"
      # S3_UPLOADS_BUCKET=#{ENV['APP_NAME_HOST']}

      # S3_UPLOADS_BUCKET_DEVELOPMENT forces the database name in WP_ENV=development, regardless of APP_HOST
      # S3_UPLOADS_BUCKET_DEVELOPMENT=#{ENV['APP_NAME_HOST']}

      # S3_UPLOADS_BUCKET_STAGING forces the database name in WP_ENV=staging, regardless of APP_HOST
      # S3_UPLOADS_BUCKET_STAGING=#{ENV['APP_NAME_HOST']}

      # S3_UPLOADS_BUCKET_PRODUCTION forces the database name in WP_ENV=production, regardless of APP_HOST
      # S3_UPLOADS_BUCKET_PRODUCTION=#{ENV['APP_NAME_HOST']}

      # WP_ENV is set in docker-compose.yml (development, staging, production)
      # WP_ENV=

      # Used by docker-compose
      COMPOSE_DOCKER_CLI_BUILD=1
      DOCKER_BUILDKIT=1 
    DOTENV

    # Write .env file
    msg "Writing .env to disk"
    create_file ".env", dotenv, :force => true

    # Run db task
    db_create
  end


  desc "deploy_host", "Choose deploy host"
  def deploy_host
    return unless env_APP_HOST
    return unless env_DEPLOY_HOSTS

    deploy_hosts = ENV['DEPLOY_HOSTS'].split(',').uniq - ['']
    index = 0
    index = choose("Choose deploy host:", deploy_hosts) if deploy_hosts.length > 1

    deploy_host = deploy_hosts[index]
    ENV['DEPLOY_HOST'] = deploy_host.split('@').last

    if File.file? '.env'
      env = File.read('.env')
      env.gsub!(/^DEPLOY_HOST=(.*)$/, "DEPLOY_HOST=#{ENV['DEPLOY_HOST']}")
      msg "Updating .env with new DEPLOY_HOST: #{ENV['DEPLOY_HOST']}"
      create_file '.env', env, :force => true
    end
  end


  desc "db_create", "Creates databases and user"
  def db_create
    say bold("DB CREATE")

    return unless env_DB_HOST
    return unless env_DB_ROOT_USER
    return unless env_DB_ROOT_PASSWORD
    return unless env_DB_ADMIN_USER
    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_DEPLOY_HOSTS
    return unless env_DB_USER
    return unless env_DB_PASSWORD
    env_APP_NAME_HOST

    # Check for existing user
    msg "Checking for existing user #{ENV['DB_USER']}..."
    command = "SELECT COUNT(*) FROM mysql.user WHERE user='#{ENV['DB_USER']}' AND host='%';"
    count = mysql(command, false).split("\n").last.to_i

    # Skip creating user
    if count > 0 then
      msg "User #{ENV['DB_USER']} already exists!"

      # Create user
    else
      msg "User #{ENV['DB_USER']} doesn't exist! Creating..."
      mysql "CREATE USER '#{ENV['DB_USER']}'@'%' IDENTIFIED WITH mysql_native_password BY '#{ENV['DB_PASSWORD']}';"
    end

    # Array of database names (based on app_name + app_host/remote_host)
    db_names = ([ENV['APP_HOST']] + ENV['DEPLOY_HOSTS'].split(',')).uniq.map do |each_host|
      slug( ENV['APP_NAME'], each_host)
    end

    # Foreach database name
    db_names.each do |db_name|
      return false if db_name.empty?

      # Create database
      mysql "CREATE DATABASE IF NOT EXISTS #{db_name} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;"

      # Grant database access permissions to user
      mysql "GRANT ALL ON #{db_name}.* TO '#{ENV['DB_USER']}'@'%';"

      # Grant database access permissions to admin user
      mysql "GRANT ALL ON #{db_name}.* TO '#{ENV['DB_ADMIN_USER']}'@'%';"
    end
  end
  


  desc "db_export", "Export from the APP_HOST database to the data directory"
  def db_export
    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_DEPLOY_HOST

    db = slug( ENV['APP_NAME'], ENV['APP_HOST'] )
    db_file = "data/#{db}.sql"

    mysql_migrate( db, db_file )
  end

  
  desc "db_import", "Import from the data directory to the APP_HOST database"
  def db_import
    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_DEPLOY_HOST

    db_files = Dir.glob("data/*.sql")
    if db_files.size < 1
      say("#{set_color(' > ', :black, :on_yellow, :bold)} #{set_color("No SQL files found!", :yellow)}")
      return false
    end

    index = choose("Choose SQL file to import:", db_files)
    if (index >= 0)

      db_file = "/srv/#{db_files[index]}"
      db = slug( ENV['APP_NAME'], ENV['APP_HOST'] )

      mysql_migrate( db_file, db )
      File.delete( db_file )

    end
  end


  desc "db_push", "Push the APP_HOST database to the DEPLOY_HOST database"
  def db_push
    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_DEPLOY_HOST

    source_db = slug( ENV['APP_NAME'], ENV['APP_HOST'] )
    dest_db = slug( ENV['APP_NAME'], ENV['DEPLOY_HOST'] )

    mysql_migrate(source_db, dest_db)
  end


  desc "db_pull", "Pull the DEPLOY_HOST database to the APP_HOST database"
  def db_pull
    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_DEPLOY_HOST

    source_db = slug( ENV['APP_NAME'], ENV['DEPLOY_HOST'] )
    dest_db = slug( ENV['APP_NAME'], ENV['APP_HOST'] )

    mysql_migrate(source_db, dest_db)
  end


  desc "test", "test"
  def test( x = false )
    index = choose("Choose one:", ['Howdy',"Y'all"])
    puts index
    puts x
    cli("touch /srv/data/#{x}.txt")
  end

end
MyThorCommand.start
