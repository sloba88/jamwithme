

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
