<template>
      <div class="flex flex-col max-w-sm mx-auto gap-4">
        <h2 class="text-lg font-semibold mb-4">Create List</h2>
        <TInput
            :label="'List Name'"
             v-model="form.list_name"
        />
        <TInput
            :label="'Task Name'"
             v-model="form.task_name"
        />
        <div class="flex flex-row justify-end ">
            <button type="button" class="px-3 py-2 text-md font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <TIcon name="add" class="select-none text-white " size="md" />
                    Add Task
            </button>
        </div>
       
        <div class="grid grid-cols-2 pt-5">
            <!-- Cancel Button -->
            <button 
                type="button" 
                 @click="handleCancel"
                class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                CANCEL
            </button>

            <!-- Add Button -->
            <button 
                type="button" 
                @click="addList" 
                class="text-white bg-green-700 border focus:outline-none hover:bg-green-800 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-800 dark:text-white dark:border-green-600 dark:hover:bg-green-700 dark:hover:border-green-600 dark:focus:ring-green-700">
                ADD LIST
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, inject } from 'vue';
import { notify } from '@/scripts';

const $api = inject('$api');
const emit = defineEmits([
    "added",
    "close" 
]);

const form = reactive({
    list_name: "",
    task_name: "",
})

const addList = () => {
    $api.post('/create/task', form).then((response) => {
        notify({group:"main", title: response.data.title, type:response.data.type},response.data.duration)
        emit("added", response.data.task);
    }).catch((error) => {
        console.error('Error adding task:', error);
    });
}

const handleCancel = () => {
    emit('close');
};


</script>