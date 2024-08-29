<template>
     <table class="w-full text-sm text-center rtl:text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 rounded-s-lg">
                    Task Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Description
                </th>
                <th scope="col" class="px-6 py-3 rounded-e-lg">
                   Status
                </th>
                <th scope="col" class="px-6 py-3 rounded-e-lg">
                   Action
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="task in tasks" :key="task?.hash" class="bg-white dark:bg-gray-800">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ task?.taskName }}
                </th>
                <td class="px-6 py-4">
                    {{ task?.description }}
                </td>
                <td class="px-6 py-4">
                    <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">DONE</span>
                </td>
                <td class="flex px-6 py-4 justify-center">
                    <th>
                        <div class="flex flex-row gap-2">
                        <button @click="" type="button">
                            <TIcon name="edit" class="select-none text-blue-500 hover:text-gray-300 hover:rotate-180 hover:ease-in duration-1000" size="md" />
                        </button>
                        <!-- Delete button -->
                        <button @click="" type="button">
                            <TIcon name="delete" class="select-none text-red-500 hover:text-gray-300" size="md" />
                        </button>
                        <!-- Restore -->
                        <button @click="" type="button">
                            <TIcon name="restore_from_trash" class="select-none text-emerald-500 hover:text-gray-300" size="md" />
                        </button>
                    </div>
                    </th>
                 
                </td>
            </tr>
           
        </tbody>
     
    </table>
</template>

<script setup>
    import { ref, inject, onMounted, watchEffect, reactive } from 'vue';

    const $api = inject('$api');

    const props = defineProps({
        task: {
            type:Object,
            default: null
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

    const tasks = ref([]);

    const searchOptions = reactive({
    page : 1,
    pages: 1,
    limit: 5,
    offset: 0,
    term: "",
    total: 0,

  });

  const addNewTask = () => {
    tasks.value.push(props.task);
  };

    const getAllTasks = (term=null) => {
        emit("loading")
        $api.get('/tasks',{
            params: {
                term:term,
                limit: searchOptions.limit,
                offset: searchOptions.offset,
            }
        }).then((response) => {
            tasks.value=response.data.data;
            searchOptions.total = response.data.count;
        }).finally(()=>{
            emit("doneLoading")
        });
    };


    watchEffect(() => {
        if(props.task != null){
            addNewTask();
        }
        if (props.searchedTerms != ""){
            searchOptions.offset = 0;
            getAllTasks(props.searchedTerms)
        }
    });

    onMounted(() => {
        getAllTasks("");
    });
</script>