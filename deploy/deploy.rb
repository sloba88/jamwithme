set :application, "Jamifind"
set :deploy_to, "/var/www/jamifind"

set :stages, ["dev", "production"]
set :default_stage, "dev"
set :user, "app"

set :repo_url,  "https://37760454bc4d8e4121e82d286c040c301c77322f:@github.com/sloba88/jamwithme.git"
set :scm, :git
set :deploy_via, :remote_cache
set :use_sudo, false

role :web, "dev.jamifind.com"                          # Your HTTP server, Apache/etc
role :app, "dev.jamifind.com"                          # This may be the same as your `Web` server
role :db,  "dev.jamifind.com", :primary => true

server 'dev.jamifind.com', user: 'app', roles: %w{web app}, primary: true

set :ssh_options, {
  forward_agent: true,
  user: 'app',
}

set :linked_files,          ["app/config/parameters.yml"]
set :linked_dirs,           [fetch(:log_path), fetch(:web_path) + "/uploads", fetch(:web_path) + "/media"]

set :file_permissions_users, ['apache']
set :webserver_user,        "apache"
set :permission_method,     "acl"
set :use_set_permissions,   true

set :format, :pretty
set :log_level, :info

after 'deploy:updated',   'symfony:assets:install'
after 'deploy:updated',   'symfony:assetic:dump'

set :bower_flags, '--quiet --config.interactive=false'

namespace :redis do
    desc 'Clears redis cache'
    task :clear do
        invoke 'symfony:console', 'redis:flushdb', '--client=cache --no-interaction'
    end
end

namespace :elastica do
    desc 'Elastica re-populate database again'
    task :populate do
        invoke 'symfony:console', 'fos:elastica:populate', '--no-interaction'
    end
end

namespace :node do
    desc 'Node restart messaging service'
    task :restart do
        on roles(:web) do
            execute "forever stopall; cd '#{release_path}/node/'; npm install; forever start app.js"
        end
    end
end

after 'deploy:updated',   'redis:clear'

after 'deploy:updated',   'elastica:populate'

after 'deploy:updated',   'node:restart'