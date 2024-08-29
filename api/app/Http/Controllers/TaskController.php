<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Lists;
use App\Models\Task;
use App\Traits\TaskTrait;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use TaskTrait;

    public function createTask(CreateTaskRequest $request){
        $fields = $request ->validated();

        $list = Lists::create([
            'list_name'=>$fields['list_name']
        ]);

        $task = Task::create([
            'list_id'=>$list->hash,
            'task_name'=>$fields['task_name'],
        ]);

        return response()->json([
            'task'=> $task? new TaskResource($task):null,
            'title'=>"Added Task",
            'duration'=>"3000",
            'type'=>"positive"
        ]);
    }

    // separate na ung controller if ieedit each field



    public function getAllTasks(Request $request){
        $request->validate([
            'term'=> 'nullable',
            'limit'=> 'nullable',
            'offset'=>'nullable',
        ]);

        $tasks = $this->searchQuery($request->input('term'));
        $count = $tasks->count();
        $tasks = $tasks->limit($request->input('limit'))->offset($request->input('offset'))->orderBy('created_at', 'asc')->get();

       return response()->json([
        'data'=>$tasks?TaskResource::collection($tasks):null,
        'count'=>$count
       ]);
    }
}
