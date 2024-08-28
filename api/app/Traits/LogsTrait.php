<?php

namespace App\Traits;
use Illuminate\Http\Request;

use App\Models\User;

trait LogsTrait
{
    use LoggerTrait;
    
    protected $module_names = [
        '0x000' => 'SYSTEM',
		'0x00A' => 'Permissions',
        '0x00B' => 'Roles',
        '0x00C' => 'Users',
		'0x01C' => 'Authentication',
		'0x01D' => 'Registration',
		'0x01E' => 'AccountManagement',
		'0x01F' => 'AccountRecovery',
    ];


    public function log($action, $module_code, $type, $oldData = null, $newData = null, $level = 7, User $user = null, $actor = null)
    {
        $module = $this->module_names[$module_code];
        if(!$this->module_names[$module_code]){
            throw new \Exception("Unknown module code: $module_code");
        }

        return $this->recordActivity($action, $module, $type, $oldData, $newData, $level, $user,$actor);
    }
}