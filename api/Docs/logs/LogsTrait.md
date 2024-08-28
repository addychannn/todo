# LogsTrait

-   This trait is from the old logging system. It is retained and modified to not break existing logging applied to controllers.
-   This trait uses the `recordActivity` function from [LoggerTrait](./LoggerTrait.md) with modification.
-   This trait has only one function ( `log(...)` ).
-   The `log` and `recordActivity` is actually the same, the only difference is the second parameter. instead of accepting string name of the module it is used on, it accepts a module code that you specify within the trait.
-   This trait will default to `info` log severity.
-   Please use the new logging system. This is only for the legacy logging system.

```php
ex.

    public function MyFunction(Request $request){

        $this->log('My message', '0x00', $this->ACTION_CREATE,
            $oldData, $newData, $this->LEVEL_INFO, $user, 'Name of the user');
    }
```
