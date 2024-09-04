<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateListRequest;
use App\Http\Resources\ListResource;
use App\Models\Lists;
use App\Models\Task;
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
    

    // public function updateListAndTasks(Request $request, $id) {
    //     $listData = $request->only(['list_name']);
    //     $tasksData = $request->input('tasks', []);
        
    //     $list = Lists::byHash($id);
        
    //     if ($list) {

    //         $list->update($listData);
            
    //         foreach ($tasksData as $taskData) {
    //             $task = Task::byHash($taskData['hash']);
    //             if ($task) {
    //                 $task->update([
    //                     'task_name' => $taskData['name']
    //                 ]);
    //             }
    //         }
            
    //         return response()->json([
    //             'data' => new ListResource($list),
    //             'title' => 'Updated Successfully',
    //             'type' => 'positive',
    //             'duration' => '3000'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'message' => 'List not found',
    //             'type' => 'negative',
    //             'duration' => '3000'
    //         ]);
    //     }
    // }

    public function updateList(UpdateListRequest $request, $id){
        $fields = $request->validated();

        $list = Lists::byHash($id);

        if($list){
            $list->update($fields);
            $list->refresh();

            return response([
                'data'=> $list ? new ListResource($list):null,
                'title'=>'Updated Successfully',
                'type'=>'positive',
                'duration'=>'3000'
            ]);
        }

    }

    public function deleteList($id) {
        $list = Lists::byHash($id);
    
        if ($list) {
           
            $list->tasks()->delete();
         
            $list->delete();
    
            return response()->json([
                'message' => 'List and its tasks have been deleted successfully.',
                'type' => 'positive',
                'duration' => '3000'
            ]);
        } else {
            return response()->json([
                'message' => 'List not found.',
                'type' => 'negative',
                'duration' => '3000'
            ]);
        }
    }
    
    
    


}
