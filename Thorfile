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
          ENV[key] = slug(name).to_s
          msg_env key

        else
          puts "\n"
          msg "Enter your #{key}:"
          msg " - lowercase"
          msg " - alphanumeric"
          msg " - underscore"
          msg " - maxlength 50 characters"
          ENV[key] = slug(input(key))
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

    def env_REMOTES
      key = 'REMOTES'
      remotes = [ENV['APP_HOST']]
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter a host for REMOTES (optional)"
        msg "Example: prod1.example.com"
        remotes << input(key)
      else
        remotes << ENV[key]
      end
      ENV[key] = remotes.uniq.join(',').to_s
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
        ENV[key] = ENV['APP_NAME']
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

    # def env_WP_USER
    #   key = 'WP_USER'
    #   if ENV[key].to_s.empty?
    #     ENV[key] = 'nonfiction'
    #   end
    #   msg_env key
    #   !ENV[key].to_s.empty?
    # end

    # def env_WP_EMAIL
    #   key = 'WP_EMAIL'
    #   if ENV[key].to_s.empty?
    #     ENV[key] = 'web@nonfiction.ca'
    #   end
    #   msg_env key
    #   !ENV[key].to_s.empty?
    # end

    # def env_WP_PASSWORD
    #   key = 'WP_PASSWORD'
    #   did_input = false
    #   if ENV[key].to_s.empty?
    #     msg ""
    #     msg "Enter the password for the WordPress admin user \"#{ENV['WP_USER']}\""
    #     ENV[key] = input key
    #     did_input = true
    #   end
    #   msg_env key unless did_input
    #   !ENV[key].to_s.empty?
    # end

    def env_S3_UPLOADS_KEY
      key = 'S3_UPLOADS_KEY'
      did_input = false
      if ENV[key].to_s.empty?
        msg ""
        msg "Enter your S3_UPLOADS_KEY (optional)"
        msg "https://cloud.digitalocean.com/account/api/tokens"
        ENV[key] = input key
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
        ENV[key] = input key
        did_input = true
      end
      msg_env key unless did_input
      !ENV[key].to_s.empty?
    end

  end
    
    

  desc "list", "List Commands"
  def list
    puts "all the commands"
  end


  desc "dotenv", "Generates .env"
  def dotenv
    say bold(".env GENERATOR")

    return unless env_DB_HOST
    return unless env_DB_ROOT_USER
    return unless env_DB_ROOT_PASSWORD

    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_REMOTES

    env_APP_NAME_HOST
    
    return unless env_DB_USER
    return unless env_DB_PASSWORD

    if env_S3_UPLOADS_KEY
      env_S3_UPLOADS_SECRET 
    end

    # Update name in package.json
    unless name_from_package
      if File.file? 'package.json'
        package = File.read('package.json').gsub(/#{default_package_name}/, ENV['APP_NAME'])
        msg "Writing package.json with new name: #{ENV['APP_NAME']}"
        create_file "package.json", package, :force => true
      end
    end

    # Build .env configuration
    dotenv = <<~DOTENV.strip
      # https://#{ENV['APP_NAME']}.#{ENV['APP_HOST']}/
      APP_NAME=#{ENV['APP_NAME']}
      APP_HOST=#{ENV['APP_HOST']}

      # Comma-separated remote docker hosts for deployment
      REMOTES=#{ENV['APP_HOST']}

      # https://cloud.digitalocean.com/databases
      DB_HOST=#{ENV['DB_HOST']}
      DB_USER=#{ENV['DB_USER']}
      DB_PASSWORD=#{ENV['DB_PASSWORD']}
      DB_PREFIX=wp
      
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
    DOTENV

    # Write .env file
    msg "Writing .env to disk"
    create_file ".env", dotenv, :force => true

    # Run db task
    dbcreate

    # Run wpinstall task
    # wpinstall
  end


  desc "dbcreate", "Creates databases and user"
  def dbcreate
    say bold("DB CREATE")

    return unless env_DB_HOST
    return unless env_DB_ROOT_USER
    return unless env_DB_ROOT_PASSWORD
    return unless env_DB_ADMIN_USER
    return unless env_APP_NAME
    return unless env_APP_HOST
    return unless env_REMOTES
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
    db_names = ([ENV['APP_HOST']] + ENV['REMOTES'].split(',')).uniq.map do |each_host|
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

  # desc "wpinstall", "Run WordPress installer"
  # def wpinstall
  #   say bold("WP INSTALL")
  #
  #   return unless env_APP_NAME
  #   return unless env_APP_HOST
  #   return unless env_WP_USER
  #   return unless env_WP_EMAIL
  #   return unless env_WP_PASSWORD
  #
  #   # Install WordPress on disk
  #   cli "composer update -d /srv" 
  #
  #   # Install WordPress on database
  #   cli([
  #     "wp core install",
  #     "--path=#{Dir.pwd}/wp",
  #     "--url=https://#{ENV['APP_NAME']}.#{ENV['APP_HOST']}/wp", 
  #     "--title=#{ENV['APP_NAME']}",
  #     "--admin_user=#{ENV['WP_USER']}",
  #     "--admin_password=#{ENV['WP_PASSWORD']}",
  #     "--admin_email=#{ENV['WP_EMAIL']}"
  #   ].join(' '))
  #
  # end

end

MyThorCommand.start