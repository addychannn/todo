<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddNewTaskRequest;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Lists;
use App\Models\Task;
use App\Traits\TaskTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    use TaskTrait;

    public function createTask(CreateTaskRequest $request)
    {
        $fields = $request->validated();

        $list = Lists::create([
            'list_name' => $fields['list_name']
        ]);
        $tasks = [];
       
        if (isset($fields['task_name']) && is_array($fields['task_name'])) {
            foreach ($fields['task_name'] as $taskName) {
                $tasks[] = Task::create([
                    'list_id' => $list->hash,
                    'task_name' => $taskName,
                ]);
            }
        }
    
        return response()->json([
            'tasks' => TaskResource::collection($tasks), 
            'title' => "Added Tasks",
            'duration' => "3000",
            'type' => "positive"
        ]);
    }

    public function addNewTask(AddNewTaskRequest $request)
    {
        $fields = $request->validated();

        $task = Task::create([
            'list_id' => $fields['list_id'],
            'task_name' => $fields['task_name'],
        ]);

        return response()->json([
            'task' => $task ? new TaskResource($task) : null,
            'title' => 'Added task',
            'type' => 'positive',
            'duration' => '3000'
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

    public function updateTask(UpdateTaskRequest $request, $id) {
        $fields = $request->validated();
        $task = Task::byHash($id);
    
        if ($task) {
            $task->update($fields);
            $task->refresh();
            return response()->json([
                'data' => $task ? new TaskResource($task) : null,
                'title' => 'Updated Successfully',
                'type' => 'positive',
                'duration' => '3000'
            ]);
        } else {
            return response()->json([
                'message' => 'not updated',
                'type' => 'negative',
                'duration' => '3000'
            ]);
        }
    }

    public function deleteTask($id){
       
    $task = Task::byHash($id);
    
        if ($task) {
            $task->delete();
            $task->refresh();
            return response()->json([
                'data' => null,
                'title' => 'Deleted',
                'type' => 'negative',
                'duration' => '3000'
            ]);
        } else {
            Log::warning('Task not found', ['task_id' => $id]);
    
            return response()->json([
                'message' => 'Task not found',
                'type' => 'warning',
            ]);
        }
    }

    public function updateTaskStatus(Request $request, $taskHash)
    {
        $task = Task::where('hash', $taskHash)->firstOrFail();
        $task->status = $request->input('status');
        $task->save();

        return response()->json(['message' => 'Task status updated successfully', 'type' => 'positive']);
    }

    
    
    
    
}
