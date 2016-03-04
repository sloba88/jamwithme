

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

```
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
```
```
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX /dev/shm/appname/cache /dev/shm/appname/logs  
```
```
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX /dev/shm/appname/cache /dev/shm/appname/logs
```
