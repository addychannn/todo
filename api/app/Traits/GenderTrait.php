<?php

namespace App\Traits;
use Illuminate\Http\Request;

use App\Models\Gender;

trait GenderTrait
{
    public function searchGenderQuery($search = "", $column = "name"){
        $searchKeys = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        $genders = Gender::where(function ($query) use ($searchKeys, $column) {
            foreach($serachKeys as $key) {
                $query->orWhere($column, $search, "LIKE", "%".$key."%");
            }
        });
        return $genders;
    }

    public function searchGender($searh = "", $limit = 100, $offset = 0, $orderBy = "name", $order = "asc", $column = "name"){
        $genders = $this->searchGenderQuery($search, $column)
            ->orderBy($orderBy, $order)
            ->when(!!$limit, function ($query) use($limit, $offset){
                $query->offset($offset)->limit($limit);
            });
        return $genders;
    }
    
    public function searchGenderCount($search = "", $column = "name"){
        $genders = $this->searchGenderQuery($search, $column)
            ->selectRaw("count(*) as count")->first();
        return $genders->count;
    }
}