CHANGELOG
=========
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