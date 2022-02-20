# Eloquent Model Generator

Eloquent Model Generator generates Eloquent models using database schema as a source.

## Version 2.0.0
Version 2.0.0 has been released. Checkout [this discussion](https://github.com/krlove/eloquent-model-generator/discussions/89) for more details and upgrade instructions.

## Installation
Step 1. Add Eloquent Model Generator to your project:
```
composer require krlove/eloquent-model-generator --dev
```
Step 2. Register `GeneratorServiceProvider`:
```php
'providers' => [
    // ...
    Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class,
];
```

Step 3. Configure your database connection.

## Usage
Use
```
php artisan krlove:generate:model User
```
to generate a model class. Generator will look for table named `users` and generate a model for it.

### table-name
Use `table-name` option to specify another table name:
```
php artisan krlove:generate:model User --table-name=user
```
In this case generated model will contain `protected $table = 'user'` property.

### output-path
Generated file will be saved into `app/Models` directory of your application and have `App\Models` namespace by default. If you want to change the destination and namespace, supply the `output-path` and `namespace` options respectively:
```
php artisan krlove:generate:model User --output-path=/full/path/to/output/directory --namespace=Your\\Custom\\Models\\Place
```
`output-path` can be absolute path or relative to project's `app` directory. Absolute path must start with `/`:
- `/var/www/html/app/Models` - absolute path
- `Custom/Models` - relative path, will be transformed to `/var/www/html/app/Custom/Models` (assuming your project app directory is `/var/www/html/app`)

### base-class-name
By default, generated class will be extended from `Illuminate\Database\Eloquent\Model`. To change the base class specify `base-class-name` option:
```
php artisan krlove:generate:model User --base-class-name=Custom\\Base\\Model
```

### no-backup
If `User.php` file already exist, it will be renamed into `User.php~` first and saved at the same directory. Unless `no-backup` option is specified:
```
php artisan krlove:generate:model User --no-backup
```

### Other options
There are several useful options for defining several model's properties:
- `no-timestamps` - adds `public $timestamps = false;` property to the model
- `date-format` - specifies `dateFormat` property of the model
- `connection` - specifies connection name property of the model

### Overriding default options

Instead of specifying options each time when executing the command you can create a config file named `eloquent_model_generator.php` at project's `config` directory with your own default values:
```php
<?php

return [
    'namespace' => 'App',
    'base_class_name' => \Illuminate\Database\Eloquent\Model::class,
    'output_path' => null,
    'no_timestamps' => null,
    'date_format' => null,
    'connection' => null,
    'no_backup' => null,
    'db_types' => null,
];
```

### Registering custom database types
If running a command leads to an error
```
[Doctrine\DBAL\DBALException]
Unknown database type <TYPE> requested, Doctrine\DBAL\Platforms\MySqlPlatform may not support it.
```
it means that you must register your type `<TYPE>` at your `config/eloquent_model_generator.php`:

```
return [
    // ...
    'db_types' => [
        '<TYPE>' => 'string',
    ],
];
```

### Usage example
Table `user`:
```mysql
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```
Command:
```
php artisan krlove:generate:model User
```
Result:
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $role_id
 * @property mixed $username
 * @property mixed $email
 * @property Role $role
 * @property Article[] $articles
 * @property Comment[] $comments
 */
class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['role_id', 'username', 'email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('Role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany('Article');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Comment');
    }
}
```

## Generating models for all tables
Command `krlove:generate:models` will generate models for all tables in the database. It accepts all options available for `krlove:generate:model` along with `skip-table` option.

### skip-table
Specify one or multiple table names to skip:
```php
php artisan krlove:generate:models --skip-table=users --skip-table=roles
```
Note that table names must be specified without prefix if you have one configured.

## Customization
You can hook into the process of model generation by adding your own instances of `Krlove\EloquentModelGenerator\Processor\ProcessorInterface` and tagging it with `GeneratorServiceProvider::PROCESSOR_TAG`.

Imagine you want to override Eloquent's `perPage` property value.
```php
class PerPageProcessor implements ProcessorInterface
{
    public function process(EloquentModel $model, Config $config): void
    {
        $propertyModel = new PropertyModel('perPage', 'protected', 20);
        $dockBlockModel = new DocBlockModel('The number of models to return for pagination.', '', '@var int');
        $propertyModel->setDocBlock($dockBlockModel);
        $model->addProperty($propertyModel);
    }

    public function getPriority(): int
    {
        return 8;
    }
}
```
`getPriority` determines the order of when the processor is called relative to other processors.

In your service provider:
```php
public function register()
{
    $this->app->tag([InflectorRulesProcessor::class], [GeneratorServiceProvider::PROCESSOR_TAG]);
}
```
After that, generated models will contain the following code:
```php
/**
 * The number of models to return for pagination.
 * 
 * @var int
 */
protected $perPage = 20;
```
