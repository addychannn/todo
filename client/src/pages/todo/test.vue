<template>
    <div class="flex flex-col max-w-sm mx-auto gap-4">
      <h2 class="text-lg font-semibold mb-4">Update List</h2>
      
      <!-- List Name Input -->
      <TInput
        :label="'List Name'"
        v-model="form.list_name"
      />
      
      <!-- Task Inputs -->
      <div v-for="(task, index) in form.tasks" :key="index" class="flex gap-2 items-center">
        <TInput
          :label="'Task Name'"
          v-model="task.name"
        />
        <button
          type="button"
          @click="removeTask(index)"
          class="text-red-500 hover:text-red-700"
        >
          <TIcon name="delete" size="md" />
        </button>
      </div>
      
      <!-- Add Task Button -->
      <div class="w-full mt-4">
        <button
          type="button"
          @click="addTask"
          class="px-3 py-2 w-full text-md font-medium text-center inline-flex items-center text-black border-2 border-dashed border-dark/25 rounded-md hover:bg-slate-100"
        >
          <TIcon name="add" class="select-none text-black" size="md" />
          Add Task
        </button>
      </div>
      
      <!-- Action Buttons -->
      <div class="grid grid-cols-2 pt-5">
        <!-- Cancel Button -->
        <button
          type="button"
          @click="handleCancel"
          class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
        >
          CANCEL
        </button>
        
        <!-- Update Button -->
        <button
          type="button"
          @click="updateListAndTasks"
          class="text-white bg-green-700 border focus:outline-none hover:bg-green-800 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-800 dark:text-white dark:border-green-600 dark:hover:bg-green-700 dark:hover:border-green-600 dark:focus:ring-green-700"
        >
          UPDATE
        </button>
      </div>
    </div>
  </template>
  
  
  
  <script setup>
  import { ref, reactive, inject, onMounted } from 'vue';
  import { notify } from '@/scripts';
  
  const $api = inject('$api');
  
  const props = defineProps({
    list: {
      type: Object,
      default: null,
    },
  });
  
  const emit = defineEmits(['close']);
  
  const form = reactive({
    hash: '',
    list_name: '',
    tasks: [{ name: '' }], // Initialize with one empty task
  });
  
  const getAllData = () => {
    // Initialize form data from props
    form.hash = props.list?.hash || '';
    form.list_name = props.list?.name || '';
    form.tasks = props.list?.tasks.map(task => ({ name: task.task_name })) || [{ name: '' }];
  };
  
  const updateList = () => {
    return $api.patch(`/update/list/${form.hash}`, {
      list_name: form.list_name,
    }).then(response => {
      if (response.data.type === 'positive') {
        notify({ group: "main", title: response.data.title, type: response.data.type }, response.data.duration);
      } else {
        throw new Error(response.data.message);
      }
    });
  };
  
  const updateTasks = () => {
    // Ensure to map tasks correctly and handle task updates
    const taskUpdates = form.tasks.map((task, index) => {
      // Use task.hash if available for updating specific tasks
      const taskHash = props.list.tasks[index]?.hash;
      return $api.patch(`/update/task/${taskHash}`, {
        task_name: task.name,
      });
    });
  
    return Promise.all(taskUpdates).then(responses => {
      responses.forEach(response => {
        if (response.data.type !== 'positive') {
          throw new Error(response.data.message);
        }
      });
    });
  };
  
  const updateListAndTasks = () => {
    Promise.all([updateList(), updateTasks()])
      .then(() => {
        emit('close', { message: 'List and tasks updated successfully' });
      })
      .catch(error => {
        console.error('Error updating list and tasks:', error);
      });
  };
  
  const addTask = () => {
    form.tasks.push({ name: '' });
  };
  
  const removeTask = (index) => {
    if (form.tasks.length > 1) {
      form.tasks.splice(index, 1);
    }
  };
  
  const handleCancel = () => {
    emit('close');
  };
  
  onMounted(() => {
    getAllData();
  });
  </script>
  
  

  