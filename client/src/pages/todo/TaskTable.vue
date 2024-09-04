<template>
    <div class="flex gap-8 flex-col">
        <div class="flex flex-row justify-between gap-4">
          
          <div class="w-full justify-start">
            <button @click="showModal = true" type="button" class="px-3 py-2 w-full text-md font-medium text-center inline-flex items-center text-black border-2 border-dashed  border-dark/25 rounded-md hover:bg-slate-100">
                <TIcon name="add" class="select-none text-black " size="md" />
                    Add Task
            </button> 
          </div>
        </div> 
        <div class="grid grid-cols-4 gap-2">
    
    <!-- cards -->
    <div 
        v-for="list in lists"
        :key="list?.hash"
        class="w-full max-w-lg p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-row justify-between">
            <h5 class="flex justify-start mb-4 text-xl font-medium text-gray-500 dark:text-gray-400">{{ list?.list_name }}</h5>
            <div class="flex justify-end items-start">
                <button 
                @click="openEditModal(list.hash)" 
                type="button">
                    <TIcon name="edit" class="select-none text-blue-500 hover:text-blue-800 hover:ease-in duration-300 hover:scale-125" size="md" />
                </button>

                <div class="flex items-center justify-end">
                  <button
                   @click="openConfirmModal(list.hash, 'deleteList')"
                   type="button">
                      <TIcon name="delete" class="select-none text-red-500 " size="md" />
                  </button> 
            </div>
            </div>
        </div>

        <ul class="text-md font-medium text-gray-900">
            <li
                v-for="task in list?.tasks"
                :key="task.hash"
                class="w-full border-b hover:bg-slate-200 border-gray-200 dark:border-gray-600">
                <div class="flex flex-row justify-between gap-2 items-center ps-3">
                  <div class="flex justify-start">
                      <TCheckBox
                        v-model="task.status"
                    />
                    <label 
                        :class="task.status ? 'line-through' : ''"
                        class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ task?.task_name }}</label>
                  </div>

                  <div class="flex justify-end">
                      <button
                          type="button"
                          @click="openTaskConfirmModal(task.hash, 'deleteTask')"
                          class="text-red-500 hover:text-red-700"
                      >
                          <TIcon name="close" class="select-none text-red-500 hover:text-red-800 hover:ease-in duration-300 hover:scale-125" size="md" />
                      </button>
                  </div>
                    
                </div>
            </li>
        </ul>
    </div>
    </div>
</div>
 

  <!-- Edit List Modal -->
  <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 relative w-full max-w-md">
          <button @click="closeEditModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
          </button>
          <UpdateTask 
              :list="list_details" 
              @update="handleUpdate" 
              @close="closeEditModal($event)" />
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

  <!-- confirmation dialog -->
  <TDialog 
      :modelValue="showConfirmModal" 
      persistent
      class="relative bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-200">
          <DeleteListConfirmation
              :text="'Are you sure you want to delete this List?'"
              v-if="actionMode == 'deleteList'"
              @confirm="deleteList()" 
              @cancel="openConfirmModal()"
              />
        <DeleteTaskConfirmation
            :text="'Are you sure you want to delete this Task?'"
            v-if="actionMode === 'deleteTask'"
            @confirm="deleteTask()" 
            @cancel="openConfirmModal()"
            />
           
   </TDialog>  
</template>

<script setup>
import { ref, inject, onMounted, watchEffect, reactive, watch } from 'vue';
import UpdateTask from './UpdateTask.vue';
import DeleteListConfirmation from '../Confirmation.vue';
import DeleteTaskConfirmation from '../Confirmation.vue';
import CreateTask from './CreateTask.vue';
import { notify } from '@/scripts';

const $api = inject('$api');

const props = defineProps({
    list: {
        type: Object,
        default: null,
    },
    searchedTerms: {
        type: String,
        default: null,
    }
});

const emit = defineEmits([
    "loading",
    "doneLoading"
  ])

const lists = ref([]);
const showEditModal = ref(false);
const currentList = ref(null);
const list_details = ref(null);
const listToDelete = ref(null);
const taskToDelete = ref(null);
const showConfirmModal = ref(false);
const actionMode = ref(null);
const showModal = ref(false);
const storage = ref(null);

const searchOptions = reactive({
    page: 1,
    pages: 1,
    offset: 0,
    term: "",
    total: 0,
});

const form = reactive({
  tasks: [{ name: '' }],
});

const addNewList = () => {
    lists.value.push(props.list);
};

const getAllLists = (term = null) => {
    emit("loading");
    $api.get('/lists', {
        params: {
            term: term,
            offset: searchOptions.offset,
        }
    }).then((response) => {
        lists.value = response.data.data;
        searchOptions.total = response.data.count;
    }).finally(() => {
        emit("doneLoading");
    });
};

const openEditModal = (hash) => {
    currentList.value = lists.value.find(list => list.hash === hash);
    list_details.value = { ...currentList.value };
    showEditModal.value = true;
};

const updateList = (updatedList) => {
    $api.patch(`/update/list/${updatedList.hash}`, updatedList)
        .then(() => {
            const index = lists.value.findIndex(list => list.hash === updatedList.hash);
            if (index !== -1) {
                lists.value[index] = updatedList;
            }
        })
        .catch((error) => {
            console.error("Error updating list:", error.response?.data?.message || error.message);
        });
};

const deleteList = () => {
    $api.delete(`/delete/list/${listToDelete.value}`)
        .then((response) => {
            if (response.data.type === 'positive') {
                lists.value = lists.value.filter(list => list.hash !== listToDelete.value);
                notify({
                    group: "main",
                    title: response.data.message,
                    type: response.data.type,
                    duration: response.data.duration
                });
                closeConfirmModal();
            } else {
                console.warn('Unexpected response type:', response.data.type);
            }
        })
        .catch((error) => {
            console.error("Error deleting list:", error.response?.data?.message || error.message);
        });
};

const updateTaskStatus = (taskHash, newStatus) => {
    $api.patch(`/update/task/${taskHash}`, { status: newStatus })
        .then(response => {
            notify({
                group: "main",
                title: response.data.message,
                type: response.data.type,
                duration: response.data.duration
            });
        })
        .catch(error => {
            console.error("Error updating task status:", error.response?.data?.message || error.message);
        });
};

const deleteTask = () => {   
  $api.delete(`/delete/task/${taskToDelete.value}`)
    .then((response) => {
      if (response.data.type === 'positive') {
        form.tasks = form.tasks.filter(task => task.hash !== taskToDelete.value);

        notify({
          group: "main",
          title: response.data.title,
          type: response.data.type,
          duration: response.data.duration
        });

        openTaskConfirmModal();
      } else {
        console.warn('Unexpected response type:', response.data.type);
      }
    })
    .finally(() => {
       getAllLists();
    });
};

const openTaskConfirmModal = (taskHash = null, mode = null) => {
  taskToDelete.value = taskHash;
  actionMode.value = mode;
  showConfirmModal.value = !showConfirmModal.value;
};

const openConfirmModal = (listHash = null, mode = null) => {
    listToDelete.value = listHash;
    actionMode.value = mode;
    showConfirmModal.value = !showConfirmModal.value; 
};

const closeConfirmModal = () => {
    showConfirmModal.value = false;
};

const closeModal =(event)=>{
    showModal.value= false;
    storage.value=event;
    getAllLists()
}

const handleUpdate = (updatedList) => {
    updateList(updatedList);
    closeEditModal();
};

const closeEditModal = (event) => {
    console.log(event);
    showEditModal.value = false;
    currentList.value = null;
    let listIndex = lists.value.findIndex(list => list.hash == event.hash)
    if(listIndex != -1){
        lists.value[listIndex] = event
    }
};

watchEffect(() => {
    if (props.list != null) {
        addNewList();
    }
    if (props.searchedTerms !== "") {
        searchOptions.offset = 0;
        getAllLists(props.searchedTerms);
    }

    lists.value.forEach(list => {
        list.tasks.forEach(task => {
            watch(() => task.status, (newStatus) => {
                updateTaskStatus(task.hash, newStatus);
            });
        });
    });
});

onMounted(() => {
    getAllLists("");
});
</script>

