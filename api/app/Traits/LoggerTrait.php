<?php

namespace App\Traits;
use Illuminate\Http\Request;

use App\Models\Logs;
use App\Models\User;

trait LoggerTrait
{
    protected $ACTION_CREATE = 1;
    protected $ACTION_UPDATE = 2;
    protected $ACTION_DELETE = 3;
    protected $ACTION_LOGIN = 4;
    protected $ACTION_LOGOUT = 5;
    protected $ACTION_DISABLE = 6;
    protected $ACTION_ENABLE = 7;
    protected $ACTION_SEND_EMAIL = 8;

    protected $ACTION_SAVING = 9;
    protected $ACTION_SAVED = 10;
    protected $ACTION_RETRIEVED = 11;
    protected $ACTION_RESTORED = 12;
    protected $ACTION_RESTORING = 13;
    protected $ACTION_REPLICATING = 14;
    protected $ACTION_CREATING = 15;
    protected $ACTION_UPDATING = 16;
    protected $ACTION_DELETING = 17;

    protected $LEVEL_EMERGENCY = 1;
    protected $LEVEL_ALERT = 2;
    protected $LEVEL_CRITICAL = 3;
    protected $LEVEL_ERROR = 4;
    protected $LEVEL_WARNING = 5;
    protected $LEVEL_NOTICE = 6;
    protected $LEVEL_INFO = 7;
    protected $LEVEL_DEBUG = 8;

    protected function getActions($action = null){
        $actions = [
            'create'        => $this->ACTION_CREATE,
            'update'        => $this->ACTION_UPDATE,
            'delete'        => $this->ACTION_DELETE,
            'login'         => $this->ACTION_LOGIN,
            'logout'        => $this->ACTION_LOGOUT,
            'disable'       => $this->ACTION_DISABLE,
            'enable'        => $this->ACTION_ENABLE,
            'sendemail'     => $this->ACTION_SEND_EMAIL,
            'saving'        => $this->ACTION_SAVING,
            'saved'         => $this->ACTION_SAVED,
            'retrieved'     => $this->ACTION_RETRIEVED,
            'restored'      => $this->ACTION_RESTORED,
            'restoring'     => $this->ACTION_RESTORING,
            'replicating'   => $this->ACTION_REPLICATING,
            'creating'      => $this->ACTION_CREATING,
            'updating'      => $this->ACTION_UPDATING,
            'deleting'      => $this->ACTION_DELETING,
        ];

        if($action != null){
            if(!property_exists($actions, strtolower($action))){
                throw new Exception("Invalid action name!");
            }
            return $actions[strtolower($action)];
        }
        return $actions;
    }

    protected function getLevels($level = null){
        $levels = [
            'emergency' => $this->LEVEL_EMERGENCY,
            'alert' => $this->LEVEL_ALERT,
            'critical' => $this->LEVEL_CRITICAL,
            'error' => $this->LEVEL_ERROR,
            'warning' => $this->LEVEL_WARNING,
            'notice' => $this->LEVEL_NOTICE,
            'info' => $this->LEVEL_INFO,
            'debug' => $this->LEVEL_DEBUG,
        ];

        if($level != null){
            if(!property_exists($levels, strtolower($level))){
                throw new Exception("Invalid level name!");
            }
            return $levels[strtolower($level)];
        }
        return $levels;
    }

    protected function map_action_types($action){
        switch($action){
            case $this->ACTION_CREATE:
                return "Created";
            case $this->ACTION_UPDATE:
                return "Updated";
            case $this->ACTION_DELETE:
                return "Deleted";
            case $this->ACTION_LOGIN:
                return "Logged In";
            case $this->ACTION_LOGOUT:
                return "Logged Out";
            case $this->ACTION_DISABLE:
                return "Toggled: Disable";
            case $this->ACTION_ENABLE:
                return "Toggled: Enabled";
            case $this->ACTION_SEND_EMAIL:
                return "Sent Email";
            case $this->ACTION_RETRIEVED:
                return 'Retrieved';
            case $this->ACTION_RESTORED: 
                return "Restored";
            case $this->ACTION_REPLICATING: 
                return "Replicating";

            case $this->ACTION_RESTORING: 
                return "Restoring";
            case $this->ACTION_SAVING: 
                return "Saving";
            case $this->ACTION_SAVED: 
                return "Saved";
            case $this->ACTION_CREATING: 
                return "Creating";
            case $this->ACTION_UPDATING: 
                return "Updating";
            case $this->ACTION_DELETING: 
                return "Deleting";
            default:
                return "Unknown";
        }
    }

    protected function map_level($level){
        switch($level){
            case $this->LEVEL_EMERGENCY:
                return "Emergency";
            case $this->LEVEL_ALERT:
                return "Alert";
            case $this->LEVEL_CRITICAL:
                return "Critical";
            case $this->LEVEL_ERROR:
                return "Error";
            case $this->LEVEL_WARNING:
                return "Warning";
            case $this->LEVEL_NOTICE:
                return "Notice";
            case $this->LEVEL_DEBUG:
                return "Debug";
            case $this->LEVEL_INFO:
            default:
                return "Info";
        }
    }

    protected function recordActivity($action, $module, $type, $oldData = null, $newData = null, $level = 7,User $user = null, $actor = null){
        $user = $user ?? auth()->user();
        $log = new Logs;
        $log->user_id = $user?->id ?? null;
        $log->actor = $actor ?? $user?->profile?->full_name ?? $user->username ?? null;
        $log->action = $action;
        $log->type = $type;
        $log->module = $module;
        $log->level = $level;
        $log->old_data = $oldData ? json_encode($oldData) : null;
        $log->new_data = $newData ? json_encode($newData) : null;
        $log->save();
        return $log;
    }

    protected function logEmergency($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_EMERGENCY);
    }

    protected function logAlert($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_ALERT);
    }

    protected function logCritical($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_CRITICAL);
    }

    protected function logError($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_ERROR);
    }

    protected function logWarning($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_WARNING);
    }

    protected function logNotice($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_NOTICE);
    }

    protected function logInfo($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_INFO);
    }

    protected function logDebug($action, $module, $type, $oldData = null, $newData = null){
        return $this->recordActivity($action, $module, $type, $oldData, $newData, $this->LEVEL_DEBUG);
    }

}