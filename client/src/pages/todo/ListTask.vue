<template>
    <div class="flex flex-col gap-4 p-4">
      <div class="flex justify-start">     
          <img class="h-auto max-w-full w-full" src="public\images\banner.png" alt="image description">
        </div>
        <div class="flex flex-col gap-4 p-4">
          
        <div class="flex flex-row justify-center">
             <!-- add button -->
              <div class="w-full">
                <button @click="showModal = true" type="button" class="px-3 py-2 w-full text-md font-medium text-center inline-flex items-center text-black border-2 border-dashed  border-dark/25 rounded-md hover:bg-slate-100">
                    <TIcon name="add" class="select-none text-black " size="md" />
                        Add Task
                </button>
        </div>
        </div>   
        <div>
            <TaskTable
            :task = "storage" 
            />
        </div>

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
          <CreateTask
          @added="closeModal($event)"
          @close="showModal = false" />
        </div>
      </div>  
</template>

<script setup>
import { ref } from 'vue';
import TaskTable from './TaskTable.vue';
import CreateTask from './CreateTask.vue';

const showModal = ref(false);
const storage = ref(null);

const closeModal =(event)=>{
    showModal.value= false;
    storage.value=event
}

</script>