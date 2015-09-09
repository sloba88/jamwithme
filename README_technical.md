

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
