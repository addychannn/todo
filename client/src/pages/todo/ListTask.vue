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
              <a class="text-white text-lg">{{ authStore.profile?.full_name ?? authStore.username }}</a>
            </div>
          </div>
      </div>
        <div class="flex flex-col gap-4 p-4">
          <input @input="debounceInput" type="text" id="table-search-users" class="block p-2 text-md text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for List">
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

</template>

<script setup>
import { ref } from 'vue';
import TaskTable from './TaskTable.vue';
import { debounce } from 'lodash';
import { useAuthStore } from "@/stores";

const authStore = useAuthStore();
const loader = ref(false);
const storage = ref(null);
const searchedTerms= ref("");

const debounceInput = debounce((e)=>{
   searchedTerms.value= e.target.value 
 },2000);

</script>