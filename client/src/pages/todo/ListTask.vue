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
            <TaskTable
            :task = "storage" />
        </div>
    </div>

     <!-- Add Task Modal -->
     <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 relative w-full max-w-md">
          <!-- Close Button -->
          <button @click="showModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
          <!-- Modal Content -->
          <CreateTask />
        </div>
      </div>  
</template>

<script setup>
import { ref } from 'vue';
import TaskTable from './TaskTable.vue';
import CreateTask from './CreateTask.vue';

const showModal = ref(false);
const storage = ref(null);


</script>