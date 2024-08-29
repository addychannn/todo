<?php

namespace Database\Seeders;

use App\Models\Lists;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lists = [
            [
                'list_name' => "Task List Name 1"
            ],
            [
                'list_name' => "Task List Name 2"
            ]
        ];

        foreach($lists as $list){
            Lists::create([
                'list_name'=>$list['list_name']
            ]);
        }
    }
}
