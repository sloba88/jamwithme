set :deploy_to, "/var/www/jamifind"

role :web, "dev.jamifind.com"                          # Your HTTP server, Apache/etc
role :app, "dev.jamifind.com"                          # This may be the same as your `Web` server
role :db,  "dev.jamifind.com", :primary => true

server 'dev.jamifind.com', user: 'app', roles: %w{web app}, primary: true

set :file_permissions_users, ['apache']
set :webserver_user,        "apache"