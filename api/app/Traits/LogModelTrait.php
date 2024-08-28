<?php

namespace App\Traits;
use Illuminate\Http\Request;

trait LogModelTrait
{
    use LoggerTrait;

    protected  $_creating = false;
    protected  $_created = true;
    protected  $_updating = false;
    protected  $_updated = true;
    protected  $_saving = false;
    protected  $_saved = false;
    protected  $_deleting = false;
    protected  $_deleted = true;
    protected  $_restoring = false;
    protected  $_restored = true;
    protected  $_retrieved = false;
    protected  $_replicating = true;

    private static $doNotLog = false;

    protected static function boot(){
        parent::boot();
        $self = new static;
        if(!self::$doNotLog){
            if($self->can_log('log_creating')){
                static::creating(function ($model) use ($self) {
                    $self->logInfo("Creating new record", $self->getModuleName(), $self->ACTION_CREATE, null, $model);
                });
            }

            if($self->can_log('log_created')){
                static::created(function ($model) use ($self) {
                    $self->logInfo("New record created", $self->getModuleName(), $self->ACTION_CREATE, null, $model);
                });
            }

            if($self->can_log('log_updating')){
                static::updating(function ($model) use ($self) {
                    $self->logInfo("Updating record", $self->getModuleName(), $self->ACTION_UPDATE, null, $model);
                });
            }

            if($self->can_log('log_updated')){
                static::updated(function ($model) use ($self) {
                    $self->logInfo("Record Updated", $self->getModuleName(), $self->ACTION_UPDATE, $model->getOriginal(), $model->getChanges());
                });
            }

            if($self->can_log('log_saving')){
                static::saving(function ($model) use ($self) {
                    $self->logInfo("Saving data", $self->getModuleName(), $self->ACTION_UPDATE, $model->getOriginal(), $model->getChanges());
                });
            }

            if($self->can_log('log_saved')){
                static::saved(function ($model) use ($self) {
                    $self->logInfo("Data Saved", $self->getModuleName(), $self->ACTION_UPDATE, $model->getOriginal(), $model->getChanges());
                });
            }

            if($self->can_log('log_deleting')){
                static::deleting(function ($model) use ($self) {
                    if($self->uses_soft_delete() && $model->isForceDeleting()){
                        $self->logWarning("Deleting record forcefully (Non-Recoverable)", $self->getModuleName(), $self->ACTION_DELETE, $model);
                    }else{
                        $self->logWarning("Deleting Record", $self->getModuleName(), $self->ACTION_DELETE, $model);
                    }
                });
            }

            if($self->can_log('log_deleted')){
                static::deleted(function ($model) use ($self) {
                    if($self->uses_soft_delete() && $model->isForceDeleting()){
                        $self->logAlert("Record deleted forcefully (Non-Recoverable)", $self->getModuleName(), $self->ACTION_DELETE, $model);
                    }else{
                        $self->logAlert("Record Deleted", $self->getModuleName(), $self->ACTION_DELETE, $model);
                    }
                });
            }

            if($self->can_log('log_restoring', true)){
                static::restoring(function ($model) use ($self) {
                    $self->logInfo("Restoring data", $self->getModuleName(), $self->ACTION_RESTORE, $model);
                });
            }

            if($self->can_log('log_restored', true)){
                static::restored(function ($model) use ($self) {
                    $self->logInfo("Data restored", $self->getModuleName(), $self->ACTION_RESTORE, $model);
                });
            }

            if($self->can_log('log_retrieved')){
                static::retrieved(function ($model) use ($self) {
                    $self->logInfo("Retrieved record", $self->getModuleName(), $self->ACTION_RETRIEVED, $model);
                });
            }

            if($self->can_log('log_replicating')){
                static::replicating(function ($model) use ($self) {
                    $self->logInfo("Replicating data", $self->getModuleName(), $self->ACTION_REPLICATE, $model);
                });
            }
        }
    }

    public static function LogMeNot(): self{
        self::$doNotLog = true;
        return new static;
    }

    private function getModuleName(){
        $classname = __CLASS__ . PHP_EOL;
        if(property_exists($this, 'log_module_name') && !empty($this->log_module_name)){
            return $this->log_module_name;
        }
        return $classname;
    }

    private function can_log($action, $check_for_softdelete = false){
        $defaultName = str_ireplace("log_", '_', $action);
        $result = property_exists($this, $action) ? $this->$action : $this->$defaultName;
        if($check_for_softdelete){
            return $this->uses_soft_delete() ? $result : false;
        }
        return $result;
    }

    private function uses_soft_delete(){
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
    }
}