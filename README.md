# Eloquent Model Generator

Eloquent Model Generator is a tool based on [Code Generator](https://github.com/krlove/code-generator) for generating Eloquent models.

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
to generate a model class. Generator will look for table with name `users` and generate a model for it.

### table-name
Use `table-name` option to specify another table name:
```
php artisan krlove:generate:model User --table-name=user
```
In this case generated model will contain `protected $table = 'user'` property.

### output-path
Generated file will be saved into `app` directory of your application and have `App` namespace by default. If you want to change the destination and namespace, supply the `output-path` and `namespace` options respectively:
```
php artisan krlove:generate:model User --output-path=/full/path/to/output/directory --namespace=Some\\Other\\NSpace
```
`output-path` can be absolute path or relative to project's `app` directory. Absolute path must start with `/`:
- `/var/www/html/app/Models` - absolute path
- `Models` - relative path, will be transformed to `/var/www/html/app/Models` (assuming your project app directory is `/var/www/html/app`)


### base-class-name
By default generated class will be extended from `Illuminate\Database\Eloquent\Model`. To change the base class specify `base-class-name` option:
```
php artisan krlove:generate:model User --base-class-name=Some\\Other\\Base\\Model
```

### other options
There are several useful options for defining several model's properties:
- `no-timestamps` - adds `public $timestamps = false;` property to the model
- `date-format` - specifies `dateFormat` property of the model
- `connection` - specifies connection name property of the model

Instead of spcifying options each time when executing the command you can create a config file named `eloquent_model_generator.php` at project's `config` directory with your own default values. Generator already contains its own config file at `Resources/config.php` with following options:
```php
<?php

return [
    'namespace'       => 'App',
    'base_class_name' => \Illuminate\Database\Eloquent\Model::class,
    'output_path'     => null,
    'no_timestamps'   => null,
    'date_format'     => null,
    'connection'      => null,
];
```
You can override them by defining `model_defaults` array at `eloquent_model_generator.php`:
```php
<?php

return [
    'model_defaults' => [
        'namespace'       => 'Some\\Other\\Namespace',
        'base_class_name' => 'Some\\Other\\ClassName',
        'output_path'     => '/full/path/to/output/directory',
        'no_timestamps'   => true,
        'date_format'     => 'U',
        'connection'      => 'other-connection',
    ],
];
```
### Registring custom database types
If running a command leads to an error
```
[Doctrine\DBAL\DBALException]
Unknown database type <ANY_TYPE> requested, Doctrine\DBAL\Platforms\MySqlPlatform may not support it.
```
it means that you must register your type `<ANY_TYPE>` with Doctrine.

For instance, you are going to register `enum` type and want Doctrine to treat it as `string` (You can find all existing Doctrine's types [here](http://doctrine-orm.readthedocs.io/projects/doctrine-dbal/en/latest/reference/types.html#mapping-matrix)). Add next lines at your `config/eloquent_model_generator.php`:
```
return [
    // ...
    'db_types' => [
        'enum' => 'string',
    ],
];
```
### Usage example
Table `user`:
```mysql
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```
Command:
```
php artisan krlove:generate:model User  --table-name=user
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

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
