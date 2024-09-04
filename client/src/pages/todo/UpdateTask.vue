<template>
  <div class="flex flex-col max-w-sm mx-auto gap-4">
    <h2 class="text-lg font-semibold mb-4">Update List</h2>

    <TInput
      :label="'List Name'"
      v-model="form.list_name"
    />
    <div v-for="(task, index) in form.tasks" :key="index" class="flex gap-2 items-center">

      <TInput
        :label="'Task Name'"
        v-model="task.name"
        class="w-full"
      />

      <!-- <button
        type="button"
        @click="openConfirmModal(task.hash, 'delete')"
        class="text-red-500 hover:text-red-700"
      >
        <TIcon name="close" class="select-none text-red-500 hover:text-red-800 hover:ease-in duration-300 hover:scale-125" size="md" />
      </button> -->
    </div>
    
    <div class="w-full mt-4">
      <button
        type="button"
        @click="addInputTask"
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
        @click="update"
        class="text-white bg-green-700 border focus:outline-none hover:bg-green-800 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-800 dark:text-white dark:border-green-600 dark:hover:bg-green-700 dark:hover:border-green-600 dark:focus:ring-green-700"
      >
        UPDATE
      </button>
    </div>
  </div>

  <!-- Confirmation Dialog -->
  <TDialog 
    :modelValue="showConfirmModal" 
    persistent
    class="relative bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-200">
    <DeleteConfirmation
      :text="'Are you sure you want to delete?'"
      v-if="actionMode === 'delete'"
      @confirm="deleteTask" 
      @cancel="openConfirmModal"
    />
  </TDialog>  
</template>

<script setup>
import { ref, reactive, inject, onMounted } from 'vue';
import { notify } from '@/scripts';
import DeleteConfirmation from '../Confirmation.vue';

const $api = inject('$api');

const props = defineProps({
  list: {
    type: Object,
    default: null
  },
  task: {
    type: Array,
    default: null
  }
});

const emit = defineEmits(['close', 'added']);

const showConfirmModal = ref(false);
const taskToDelete = ref(null);
const actionMode = ref(null);

const form = reactive({
  hash: '',
  list_name: '',
  tasks: [{ name: '', isNew: true }],
});

const storeListData = () => {
  form.hash = props.list?.hash || '';
  form.list_name = props.list?.list_name || '';
  form.tasks = props.list?.tasks?.map(task => ({
    name: task.task_name,
    hash: task.hash,
    isNew: false 
  })) || [{ name: '', isNew: true }]; 
};

const addInputTask = () => {
  form.tasks.push({ name: '', isNew: true });
};

const addNewTask = (task) => {
  if (task.name.trim() === '') {
    notify({ group: "main", title: "Task name cannot be empty", type: 'negative' });
    return;
  }
  
  $api.post('/add/task', {
    list_id: form.hash,
    task_name: task.name
  }).then((response) => {
    notify({ group: "main", title: response.data.title, type: response.data.type }, response.data.duration);
    task.isNew = false; 
    emit("added", response.data.task); 
  }).catch((error) => {
    console.error('Error adding task:', error);
  });
};

const update = () => {
  $api.patch(`/update/list/${form.hash}`, form)
    .then((response) => {
      if (response.data.type === 'positive') {
        notify({
          group: 'main',
          title: response.data.title,
          type: response.data.type,
          duration: response.data.duration
        });
        console.log(response.data.data)
        emit('close', response.data.data);
      }
    })
    .catch((error) => {
      console.error("Error updating list and tasks:", error.response?.data?.message || error.message);
    });
};

const deleteTask = () => {
  $api.delete(`/delete/task/${taskToDelete.value}`)
    .then((response) => {
      if (response.data.type === 'negative') {
        form.tasks = form.tasks.filter(task => task.hash !== taskToDelete.value);

        notify({
          group: "main",
          title: response.data.title,
          type: response.data.type,
          duration: response.data.duration
        });

        openConfirmModal(); 
      } else {
        console.warn('Unexpected response type:', response.data.type);
      }
    })
    .catch((error) => {
      console.error("Error deleting task:", error.response?.data?.message || error.message);
    });
};

const openConfirmModal = (taskHash = null, mode = null) => {
  taskToDelete.value = taskHash;
  actionMode.value = mode;
  showConfirmModal.value = !showConfirmModal.value;
};

const handleCancel = () => {
  emit('close');
};

onMounted(() => {
  storeListData();
});
</script>
