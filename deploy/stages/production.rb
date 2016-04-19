set :deploy_to, "/var/www"

role :web, "139.59.136.28"                          # Your HTTP server, Apache/etc
role :app, "139.59.136.28"                          # This may be the same as your `Web` server
role :db,  "139.59.136.28", :primary => true

server '139.59.136.28', user: 'app', roles: %w{web app}, primary: true

set :file_permissions_users, ['www-data']
set :webserver_user,        "www-data"