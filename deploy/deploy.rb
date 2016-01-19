set :application, "Jamifind"
set :deploy_to, "/vagrant/test"

set :stages, ["dev", "production"]
set :default_stage, "dev"

set :repo_url,  "https://37760454bc4d8e4121e82d286c040c301c77322f:@github.com/sloba88/jamwithme.git"
set :scm, :git
set :deploy_via, :remote_cache

role :web, "33.33.33.100"                          # Your HTTP server, Apache/etc
role :app, "33.33.33.100"                          # This may be the same as your `Web` server
role :db,  "33.33.33.100", :primary => true

set :linked_files,          ["app/config/parameters.yml"]
set :linked_dirs,           [fetch(:log_path), fetch(:web_path) + "/uploads", fetch(:web_path) + "/media"]

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



after 'deploy:updated',   'redis:clear'

after 'deploy:updated',   'elastica:populate'