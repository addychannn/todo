<template>
    <div class="grid grid-cols-4 gap-2">
        <!-- cards -->
        <div 
        v-for="task in tasks"
            :key="task?.hash"
         class="w-full max-w-lg p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-row justify-between">
            <h5 class="flex justify-start mb-4 text-xl font-medium text-gray-500 dark:text-gray-400">{{ task?.list_name }}</h5>
            <div class="flex justify-end items-start">
                <button @click="" type="button">
                    <TIcon name="edit" class="select-none text-blue-500 hover:text-blue-800  hover:ease-in duration-300 hover:scale-125" size="md" />
                  </button>
              </div>
        </div>
        
       <!-- checkbox tasks -->
        <ul class="text-md font-medium text-gray-900 ">
            <li
            v-for="task in tasks"
            :key="task?.hash"  class="w-full border-b hover:bg-slate-200 border-gray-200 dark:border-gray-600">
                <div class="flex items-center ps-3">
                    <input id="vue-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <label for="vue-checkbox" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ task?.task_name }}</label>
                </div>
            </li>
            
        </ul>
        </div>
    </div>
    
</template>

<script setup>
import { ref, inject, onMounted, watchEffect, reactive } from 'vue';

const $api = inject('$api');

const props = defineProps({
 

    task: {
      type: Object,
      default: null,
    },

    searchedTerms:{
        type:String,
        default: null,
    }
  });

  const emit = defineEmits([
    "loading",
    "doneLoading"
  ])

  // const lists = ref([]);
  const tasks = ref([]);
  const showConfirmModal = ref(false);
  const showEditModal = ref(false);
  const list_details = ref(null);
  const listToDelete = ref(null);
  const currentList = ref(null);
  const actionMode = ref(null);

  const searchOptions = reactive({
    page : 1,
    pages: 1,
    limit: 5,
    offset: 0,
    term: "",
    total: 0,

  });

  // const addNewList = () => {
  //   lists.value.push(props.list);
  // };

  const addNewTask = () => {
    tasks.value.push(props.task);
  };


  // const getAllLists = (term = null) => {
  //   emit("loading")
  //   $api.get('/lists',{
  //       params:{
  //           term: term,
  //           limit: searchOptions.limit,
  //           offset: searchOptions.offset,
  //       }
  //   }).then((response) => {
  //     lists.value = response.data.data;
  //     searchOptions.total = response.data.count;

  //   }).finally(()=>{
  //       emit("doneLoading")
  //   })
  //   ;
  // };

  
  const getAllTasks = (term = null) => {
    emit("loading")
    $api.get('/tasks',{
        params:{
            term: term,
            limit: searchOptions.limit,
            offset: searchOptions.offset,
        }
    }).then((response) => {
      tasks.value = response.data.data;
      searchOptions.total = response.data.count;

    }).finally(()=>{
        emit("doneLoading")
    })
    ;
  };

  watchEffect(() => {
    // if (props.list != null) {
    //   addNewList();
    // }
    if (props.task != null) {
      addNewTask();
    }
    // if (props.searchedTerms != ""){
    //     searchOptions.offset = 0;
    //     getAllLists(props.searchedTerms);
    // }
  });
  
  onMounted(() => {
    // getAllLists("");
    getAllTasks("");
  });

</script>