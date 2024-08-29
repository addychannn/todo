<?php

namespace App\Traits;

use App\Models\Task;
use Illuminate\Http\Request;

trait TaskTrait
{
    public function searchQuery($term = ''){
        $search_keys = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY);
        $tasks = Task::withTrashed()->where(function($query) use ($search_keys){
            foreach($search_keys as $key){
                $query->orWhere('taskName','ilike','%'.$key.'%');
            }
        });
        return $tasks;
    }
}