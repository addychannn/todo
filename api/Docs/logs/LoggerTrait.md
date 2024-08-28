# LoggerTrait

-   The main trait that is also used in the LogModelTrait and LogsTrait. This trait contains the entire logic for logging.

### `Import`

Since this trait is already used by the LogsTrait, it's no longer necessary to use this in your controllers as the LogsTrait is imported in the main controller, effectively making the log functions available.

```php
...
use App\Traits\LoggerTrait;
class MyController extends Controller{
    use LoggerTrait;
    ...
}
```

The logging system now incorporates 7 levels of severity:

| Severity  | Description                                                              | Condition                                                                              |
| --------- | ------------------------------------------------------------------------ | -------------------------------------------------------------------------------------- |
| Emergency | System is unusable                                                       | A panic condition.                                                                     |
| Alert     | Action must be taken immediately (but system is still usable).           | A condition that should be corrected immediately, such as a corrupted system database. |
| Critical  | Critical conditions (but action need not be taken immediately)           | Hard device errors.                                                                    |
| Error     | Error conditions (but not critical)                                      |                                                                                        |
| Warning   | Warning conditions (close to error, but not error)                       |                                                                                        |
| Notice    | Normal but significant (notable) conditions                              | Conditions that are not error conditions, but that may require special handling.       |
| Info      | Informational messages                                                   | Confirmation that the program is working as expected.                                  |
| Debug     | Debug-level messages (, i.e. messages logged for the sake of de-bugging) | Messages that contain information normally of use only when debugging a program.       |

### `Usage`

To log messages in your controllers, you simply call any of the following fuctions and supply the parameters:

```php
   $this->logEmergency( $action, $module, $type, $oldData, $newData );
   $this->logAlert( ... );
   $this->logCritical( ... );
   $this->logError( ... );
   $this->logWarning( ... );
   $this->logNotice( ... );
   $this->logInfo( ... );
   $this->logDebug( ... );
```

These functions accepts the same parameters below:

| Parameter | Type               | Required | Default | Description                                                                     |
| --------- | ------------------ | -------- | ------- | ------------------------------------------------------------------------------- |
| $action   | `String`           | Yes      | N/A     | The message you want to log.                                                    |
| $module   | `String`           | Yes      | N/A     | The module name where the log takes place.                                      |
| $type     | `Integer`          | Yes      | N/A     | The type of action taken (Refer to the actions table below).                    |
| $oldData  | `array/collection` | No       | `null`  | Additional data (Mostly coming from your Model) that's associated with the log. |
| $newData  | `array/collection` | No       | `null`  | An updated model data or new data that's associated with the log.               |

### `Record Activity`

In most cases, the provided functions are enough to log information within your system. There is, however, another function utilized by the listed functions above. The `recordActivity` function accepts 3 additional parameters, these parameters are optional and is automatically added if no value is provided. You can use this function to manually set data to these parameters:

| Parameter | Type      | Required | Default | Description                                                                                                                                                                   |
| --------- | --------- | -------- | ------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| $level    | `Integer` | No       | `7`     | The level of severity of the log. (defaults to `Info`)                                                                                                                        |
| $user     | `User`    | No       | `null`  | The User model who performed the actions that leads to this log message. (If `null`, the system uses the currently authenticated user)                                        |
| $actor    | `String`  | No       | `null`  | A String version of the user parameter. This will record the current Full name/username/email of the current user. This will not change regardless if the User model changes. |

```php
 ex.
 ...
 public function myFunction(Request $request){

    $user = auth()->user();
    $name = auth()->user()->profile->first_name + " " + auth()->user()->profile->last_name;

    $someData = SomeModel::where('id', 100)->get();
    $someData->update([
        'column' => 'New Value'
    ]);

    $this->recordActivity(
        'Test message here!recordActivity',
        'SYSTEM',
        $this->ACTION_UPDATE,
        $someData->getOriginal(),
        $someData->getChanges(),
        $this->LEVEL_INFO,
        $user,
        $name
    );
 }
 ...
```

### `Log Types`

| Variable            | Value |
| ------------------- | ----- |
| $ACTION_CREATE      | `1`   |
| $ACTION_UPDATE      | `2`   |
| $ACTION_DELETE      | `3`   |
| $ACTION_LOGIN       | `4`   |
| $ACTION_LOGOUT      | `5`   |
| $ACTION_DISABLE     | `6`   |
| $ACTION_ENABLE      | `7`   |
| $ACTION_SEND_EMAIL  | `8`   |
| $ACTION_SAVING      | `9`   |
| $ACTION_SAVED       | `10`  |
| $ACTION_RETRIEVED   | `11`  |
| $ACTION_RESTORED    | `12`  |
| $ACTION_RESTORING   | `13`  |
| $ACTION_REPLICATING | `14`  |
| $ACTION_CREATING    | `15`  |
| $ACTION_UPDATING    | `16`  |
| $ACTION_DELETING    | `17`  |

### `Levels of Severity`

| Variable         | Value |
| ---------------- | ----- |
| $LEVEL_EMERGENCY | `1`   |
| $LEVEL_ALERT     | `2`   |
| $LEVEL_CRITICAL  | `3`   |
| $LEVEL_ERROR     | `4`   |
| $LEVEL_WARNING   | `5`   |
| $LEVEL_NOTICE    | `6`   |
| $LEVEL_INFO      | `7`   |
| $LEVEL_DEBUG     | `8`   |
