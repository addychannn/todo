<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'task_name'=> "Task 1",
                'description'=> "This is sample task",
            ],
            [
                'task_name'=> "Task 2",
                'description'=> "This is sample task",
            ],
        ];

        foreach($tasks as $task){
            Task::create([
                'task_name'=>$task['task_name']
            ]);
        }
    }
}
