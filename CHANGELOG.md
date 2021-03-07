CHANGELOG
=========
2021-03-07, v1.3.7
-----------------
* Add Doctrine DBAL 3.0 support

2020-10-03, v1.3.6
-----------------
* Add Laravel 8 support

2020-09-04, v1.3.5
-----------------
* Merged PR [#69](https://github.com/krlove/eloquent-model-generator/pull/69) (Update composer.json)

2019-11-03, v1.3.4
-----------------
* Add quote for table prefix (issue [#59](https://github.com/krlove/eloquent-model-generator/issues/59))

2019-10-12, v1.3.3
-----------------
* Add Laravel 6 support

2019-03-02, v1.3.2
-----------------
* Merged PR [#49](https://github.com/krlove/eloquent-model-generator/pull/49) (Compatible with Laravel 5.8)

2018-07-26, v1.3.1
-----------------
* Add package autodiscovery feature (issue [#37](https://github.com/krlove/eloquent-model-generator/issues/37))
* Introduce backup option for saving existing model file (issue [#32](https://github.com/krlove/eloquent-model-generator/issues/32))

2018-07-22, v1.3.0
-----------------
* Add lumen Support (issue [#33](https://github.com/krlove/eloquent-model-generator/issues/33))
* Perform minor code style changes

2018-07-15, v1.2.7
-----------------
* Merged PR [#36](https://github.com/krlove/eloquent-model-generator/pull/36) (Change `incrementing` property to `public`)

2017-07-19, v1.2.6
-----------------
* Add Laravel 5.5 support

2017-07-19, v1.2.5
-----------------
* Add custom primary key, key type and incrementing properties support

2017-06-23, v1.2.4
-----------------
* Fixed issue [#25](https://github.com/krlove/eloquent-model-generator/issues/25)

2016-08-20, v1.2.0
-----------------
* Add table prefix support
* Fixed relation's related table generation
* Fixed type hinting for relation's methods php doc
* Fixed ManyToMany relation's arguments generation
* Performed internal refactoring

2016-07-16, v1.1.0
-----------------
* Added possibility to register custom database types (like `enum`)
* Added possibility to define configs at `config/eloquent_model_generator.php`
* Removed `--config` (`-c`) argument from `GenerateModelCommand`'s signature

2016-04-03, v1.0.0
-----------------
* Initial Version
