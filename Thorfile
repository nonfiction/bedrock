#!/usr/bin/env ruby

require "thor"
require "dotenv/load"
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

    def clean(text)
      text.downcase.tr(' ','_').tr('-','_').tr('.','_')
    end

    # MySQL base command
    def mysql(args = {}, command)
      base = "mysql -h#{args[:host]} -P#{args[:port]} -u#{args[:root_user]} -p#{args[:root_pass]} -e"
      cmd = "#{base} \"#{command}\""
      out, err, stat = Open3.capture3(cmd)
      return out
    end

  end

  desc "list", "List Commands"
  def list
    puts "all the commands"
  end

  desc "foo", "Prints foo"
  def foo(bar="bar")
    puts "foo! #{bar}!!"
    puts ENV.to_h.to_yaml 
  end
  
  desc "env", "Generates .env"
  def env
    say bold(".env GENERATOR")

    say "What is your app_name", :green
    say " - lowercase", :green
    say " - alphanumeric", :green
    say " - underscore", :green
    say " - maxlength 50 characters", :green
    app = clean(ask bold("APP:"))

    say "Please enter your database host", :green
    say "Example: mysql.example.com:25060", :green
    db_host = ask bold("DB_HOST:")

    env = <<~ENV.strip
      APP=#{app}

      # https://cloud.digitalocean.com/databases
      DB_HOST=#{db_host}
      DB_USER=#{app}
      DB_PASSWORD=#{`pwgen -s 20`}
      DB_PREFIX=wp
      
      # Leave DB_NAME unset in this file. DB_NAME is automatically set to one of the below:"
      # DB_NAME=
      DB_NAME_PRODUCTION=#{app}
      DB_NAME_DEVELOPMENT=#{clean("#{app}_#{ENV['HOSTNAME']}")}
      
      # Leave WP_ENV unset in this file. WP_ENV is set in docker-compose.yml
      # WP_ENV=

      # https://cloud.digitalocean.com/account/api/tokens
      S3_UPLOADS_SPACE=nonfiction
      S3_UPLOADS_REGION=sfo2
      S3_UPLOADS_BUCKET=#{app}
      S3_UPLOADS_KEY=
      S3_UPLOADS_SECRET=
      S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL=false
    ENV

    create_file ".env", env, :force => true
  end


  desc "db", "Creates databases and user"
  def db
    say bold("DB CREATE")

    say "What your database ROOT password?", :green
    root_pass = clean(ask(bold("PASSWORD:"), :echo => false))

    root_user = 'doadmin'
    admin_user = 'nonfiction'

    user = ENV['DB_USER']
    pass = ENV['DB_PASSWORD']

    args = { 
      :host => ENV['DB_HOST'].split(':')[0],
      :port => ENV['DB_HOST'].split(':')[1],
      :root_user => root_user,
      :root_pass => root_pass
    }

    # Validate data
    return false if user.empty? or pass.empty? or root_pass.empty?

    # Check for existing user
    say "Checking for existing user #{user}...", :green
    command = "SELECT COUNT(*) FROM mysql.user WHERE user='#{user}' AND host='%';"
    count = mysql(args, command).split("\n").last.to_i

    # Skip creating user
    if count > 0 then
      say "User #{user} already exists!", :green

    # Create user
    else
      say "User #{user} doesn't exist! Creating...", :green
      command = "CREATE USER '#{user}'@'%' IDENTIFIED WITH mysql_native_password BY '#{pass}';"
      say command, :yellow
      say mysql(args, command), :white
    end
      
    # Foreach database name
    [ ENV['DB_NAME_PRODUCTION'], ENV['DB_NAME_DEVELOPMENT'] ].each do |db_name|
       return false if db_name.empty?

       # Create database
       command = "CREATE DATABASE IF NOT EXISTS #{db_name} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
       say command, :yellow
       say mysql(args, command), :white

      # Grant database access permissions to user
       command = "GRANT ALL ON #{db_name}.* TO '#{user}'@'%';"
       say command, :yellow
       say mysql(args, command), :white

       # Grant database access permissions to admin user
       command = "GRANT ALL ON #{db_name}.* TO '#{admin_user}'@'%';"
       say command, :yellow
       say mysql(args, command), :white
    end

  end

end

MyThorCommand.start
