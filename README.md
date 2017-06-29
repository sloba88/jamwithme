

Generate tables

```
php app/console doctrine:schema:update --force
```

Insert fixtures

```
php app/console doctrine:fixtures:load
```

Populate elasticsearch indexes

```
php app/console fos:elastica:populate
```

Bower install

```
bower install
```

Install assets

```
php app/console assets:install 
```

Generate routes

```
php app/console fos:js-routing:dump
```

Dump assets

```
php app/console assetic:dump
```

Clear redis cache

```
redis-cli flushall
```

```
sudo npm install -g grunt-cli
```

Change templates and parse them
```
cd node ; grunt jst; cd ..; php app/console assetic:dump
```

```
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
```
```
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX /dev/shm/appname/cache /dev/shm/appname/logs  
```
```
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX /dev/shm/appname/cache /dev/shm/appname/logs
```

Setup deployment

```
bundle install
```

```
sudo gem install capistrano-symfony
```

Exctract translations
```
php app/console translation:extract en --config=app
php app/console translation:extract fi --config=app
```

Compile sass
```
cd web; compass compile; cd ..;
```