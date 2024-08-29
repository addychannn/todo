<?php

namespace App\Traits;

use App\Models\Task;
use Illuminate\Http\Request;

trait TaskTrait
{
    public function searchQuery($term = ''){
        $search_keys = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY);
        $tasks = Task::withTrashed()->with(['lists'])->where(function($query) use ($search_keys){
            foreach($search_keys as $key){
                $query->orWhere('task_name','ilike','%'.$key.'%')
                    ->orWhereHas('lists',function($q) use ($key){
                        $q->where('list_name','ilike','%'.$key.'%');
                    });
            }
        });
        return $tasks;
    }
}
