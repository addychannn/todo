<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListResource;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function getAllLists(Request $request){
        $request->validate([
            'term'=> 'nullable',
            'limit'=> 'nullable',
            'offset'=>'nullable',
        ]);

        $lists = $this->searchQuery($request->input('term'));
        $count = $lists->count();
        $lists = $lists->limit($request->input('limit'))->offset($request->input('offset'))->orderBy('created_at', 'asc')->get();

       return response()->json([
        'data'=>$lists?ListResource::collection($lists):null,
        'count'=>$count
       ]);
    }
}
