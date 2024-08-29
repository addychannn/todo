<template>
    <div class="flex flex-col gap-10 p-20">
        <div class="flex justify-center pb-4">
            <p class="text-xl font-bold">To Do List</p>
        </div>
        <div class="flex flex-row justify-between">
             <input @input="debounceInput" type="text" id="table-search-users" class="block p-2 text-md text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Task">
             <!-- add button -->
              <div class="flex justify-center items-center">
                <button @click="showModal = true" type="button"  class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Add Task</button>    
              </div>
        </div>   
        <div>
            <TaskTable />
        </div>
    </div>
</template>

<script setup>
import TaskTable from './TaskTable.vue';
</script>