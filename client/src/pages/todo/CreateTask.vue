<template>
  <div class="flex flex-col max-w-sm mx-auto gap-4">
    <h2 class="text-lg font-semibold mb-4">Create List</h2>
    <TInput
      :label="'List Name'"
      v-model="form.list_name"
    />
    <div v-for="(task, index) in form.task_name" :key="index" class="flex flex-row gap-2 justify-between w-full">
      <TInput
        :label="'Task Name'"
        v-model="form.task_name[index]"
        class="w-full"
      />
      <button
        type="button"
        @click="removeTask(index)"
        class="text-red-500 hover:text-red-700"
      >
        <TIcon name="close" class="select-none text-red-500 hover:text-red-800 hover:ease-in duration-300 hover:scale-125" size="md" />
      </button>
    </div>
    <div class="w-full">
      <button
        @click="addTask"
        type="button"
        class="px-3 py-2 w-full text-md font-medium text-center inline-flex items-center text-black border-2 border-dashed border-dark/25 rounded-md hover:bg-slate-100"
      >
        <TIcon name="add" class="select-none text-black" size="md" />
        Add Task
      </button>
    </div>
    <div class="grid grid-cols-2 pt-5">
    
      <button
        type="button"
        @click="handleCancel"
        class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
      >
        CANCEL
      </button>
    
      <button
        type="button"
        @click="addList"
        class="text-white bg-green-700 border focus:outline-none hover:bg-green-800 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-800 dark:text-white dark:border-green-600 dark:hover:bg-green-700 dark:hover:border-green-600 dark:focus:ring-green-700"
      >
        ADD LIST
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, inject } from 'vue';
import { notify } from '@/scripts';

const $api = inject('$api');

const emit = defineEmits(['added', 'close']);

const form = reactive({
  list_name: '',
  task_name: [''],
});

const addTask = () => {
  form.task_name.push('');
};

const removeTask = (index) => {
  if (form.task_name.length > 1) {
    form.task_name.splice(index, 1);
  }
};

const addList = () => {
  $api.post('/create/task', form)
    .then((response) => {
      notify({ group: 'main', title: response.data.title, type: response.data.type }, response.data.duration);
      emit('added', response.data.task);
    })
    .catch((error) => {
      console.error('Error adding task:', error);
    });
};

const handleCancel = () => {
  emit('close');
};
</script>
