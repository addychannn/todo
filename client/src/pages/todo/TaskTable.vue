<template>
    <div class="grid grid-cols-4 gap-2">
        <div class="w-full max-w-lg p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-row justify-between">
            <h5 class="flex justify-start mb-4 text-xl font-medium text-gray-500 dark:text-gray-400">List Name</h5>
            <div class="flex justify-end items-start">
                <button @click="" type="button">
                    <TIcon name="edit" class="select-none text-blue-500 hover:text-blue-800  hover:ease-in duration-300 hover:scale-125" size="md" />
                  </button>
              </div>
        </div>
        
       <!-- checkbox tasks -->
        <ul class="text-md font-medium text-gray-900 ">
            <li class="w-full border-b hover:bg-slate-200 border-gray-200 dark:border-gray-600">
                <div class="flex items-center ps-3">
                    <input id="vue-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <label for="vue-checkbox" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Task 1</label>
                </div>
            </li>
            
        </ul>
    </div>
    </div>
    
</template>

<script setup>
</script>