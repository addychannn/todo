<?php

namespace App\Traits;

use App\Models\Lists;
use Illuminate\Http\Request;

trait ListTrait
{
    public function searchQuery($term = ''){
        $search_keys = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY);
        $lists = Lists::withTrashed()->where(function($query) use ($search_keys){
            foreach($search_keys as $key){
                $query->orWhere('list_name','ilike','%'.$key.'%');
            }
        });
        return $lists;
    }
}