<?php

namespace App\Http\Controllers;

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
    

    public function updateListAndTasks(Request $request, $id) {
        $fields = $request->validate([
            "list_name" => "required",
            "tasks" => "nullable|array"
        ]);
    
        $list = Lists::byHash($id);
    
        if ($list) {
            $list->update(['list_name' => $fields["list_name"]]);
    
            if ($fields["tasks"]) {
                foreach ($fields["tasks"] as $taskData) {
                    if (isset($taskData['hash'])) {
                        // Update existing task
                        $task = Task::byHash($taskData['hash']);
                        if ($task) {
                            $task->update([
                                'task_name' => $taskData['name']
                            ]);
                        }
                    } else {
                        // Create new task
                        Task::create([
                            'list_id' => $list->hash,
                            'task_name' => $taskData['name']
                        ]);
                    }
                }
            }
            return response()->json([
                'data' => new ListResource($list),
                'title' => 'Updated Successfully',
                'type' => 'positive',
                'duration' => '3000'
            ]);
        } else {
            return response()->json([
                'message' => 'List not found',
                'type' => 'negative',
                'duration' => '3000'
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
