<template>
    <div class="flex flex-col gap-4 p-4">
      <TInnerLoading
      :text="'Loading'"
      :active="loader"
      :isFullScreen="true"
      />
      <div class="flex justify-start">  
              <!-- Welcome Banner  -->
          <div class="flex flex-row gap-4 w-full p-10 rounded-lg bg-gradient-to-r from-emerald-500 via-emerald-600">
            <div>
              <TIcon name="account_circle" size="2xl" />
            </div>
            <div class="flex flex-col">
              <a class="text-white text-2xl font-semibold">Good Morning!</a>
              <a class="text-white text-lg">John Doe</a>
            </div>
          </div>
      </div>
        <div class="flex flex-col gap-4 p-4">
          <input @input="debounceInput" type="text" id="table-search-users" class="block p-2 text-md text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for List">
        <div class="flex flex-row justify-between gap-4">
             <!-- add button -->
              <div class="w-full justify-start">
                <button @click="showModal = true" type="button" class="px-3 py-2 w-full text-md font-medium text-center inline-flex items-center text-black border-2 border-dashed  border-dark/25 rounded-md hover:bg-slate-100">
                    <TIcon name="add" class="select-none text-black " size="md" />
                        Add Task
                </button> 
              </div>
        </div>   
        <div>
            <TaskTable
            :task = "storage" 
            :searchedTerms="searchedTerms"
            @loading="loader = true"
            @doneLoading="loader = false"
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
import { debounce } from 'lodash';

const loader = ref(false);
const showModal = ref(false);
const storage = ref(null);
const searchedTerms= ref("");

const closeModal =(event)=>{
    showModal.value= false;
    storage.value=event;
 
}

const debounceInput = debounce((e)=>{
   searchedTerms.value= e.target.value 
 },2000);

</script>