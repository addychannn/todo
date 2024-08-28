# Web Template API

## Project setup

```
1. composer update
```

```
2. php artisan key:generate
```

```
3. php artisan migrate --seed
```

```
4. php artisan serve
```

## Packages Used

| Package Name                                                                   | Description                                                                                                                                     |
| ------------------------------------------------------------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------- |
| [Intervention Image](https://github.com/Intervention/image)                    | Intervention Image is a PHP image handling and manipulation library providing an easier and expressive way to create, edit, and compose images. |
| [Laravel Sanctum ](https://github.com/laravel/sanctum)                         | Laravel Sanctum provides a featherweight authentication system for SPAs and simple APIs.                                                        |
| [Spatie Permission](https://spatie.be/docs/laravel-permission/v5/introduction) | This package allows you to manage user permissions and roles in a database.                                                                     |
| [Laravel HashId](https://github.com/veelasky/laravel-hashid)                   | Automatic HashId generator for your eloquent model.                                                                                             |

## Miscallenous

| Documentations         |
| ---------------------- |
| [Logs](./Docs/Logs.md) |

### Custom artisan commands

```php
// Scaffold a new trait
php artisan make:trait TraitName
```
