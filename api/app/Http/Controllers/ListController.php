<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateListRequest;
use App\Http\Resources\ListResource;
use App\Models\Lists;
use App\Traits\ListTrait;
use Illuminate\Http\Request;

class ListController extends Controller
{
    use ListTrait;
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

    public function updateList(UpdateListRequest $request, $id){
        $fields = $request->validated();
        $list = Lists::byHash($id);
        
        if($list){
            $list->update($fields);
            $list->refresh();
            return response()->json([
                'data'=>$list ? new ListResource($list):null,
                'title'=>'Updated Successfully',
                'type'=>'positive',
                'duration'=>'3000'
            ]);
        }
        else{
            return response()->json([
                'message'=>'not updated',
                'type'=>'negative',
                'duration'=>'3000'
            ]);
           }

    }


}
