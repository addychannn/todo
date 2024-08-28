# LogModelTrait

-   This trait is an extension of the LoggerTrait trait. Use it in your model to automatically log activities based on events fired by your model.

### `Import`

In this example, We'll import the logger in the `User` model.

```php

...
use App\Traits\LogModelTrait as Logger;

class User extends Authenticatable
{
    use ..., Logger;

    // Set the module name to use in our logger
    protected $log_module_name = "Users"
}

```

Thats it, the logger will add event handlers to your model and will automatically log activies.

### `Events`

The logger will attach itself to the events fired by your models, you can specify which events the logger will reocord. Just set any of the following to true/false to start/stop recording model activities.

```php
    // Values provided are the default
    protected $creating = false;
    protected $created = true;
    protected $updating = false;
    protected $updated = true;
    protected $saving = false;
    protected $saved = false;
    protected $deleting = false;
    protected $deleted = true;
    protected $restoring = false;
    protected $restored = true;
    protected $retrieved = false;
    protected $replicating = true;
```

### `Misc`

In the event that you don't want to log any activities of your Model, you can use the `LogMeNot` function to stop any logging.

```php
ex.
    $user = User::LogMeNot()->where('id', 1)->get();
    $user->update([
        'username' => 'newusername'
    ]);

```
