<template>
  <div class="grid grid-cols-4 gap-2">
      <!-- cards -->
      <div 
          v-for="list in lists"
          :key="list?.hash"
          class="w-full max-w-lg p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
          <div class="flex flex-row justify-between">
              <h5 class="flex justify-start mb-4 text-xl font-medium text-gray-500 dark:text-gray-400">{{ list?.list_name }}</h5>
              <div class="flex justify-end items-start">
                  <button @click="openEditModal(list.hash)" type="button">
                      <TIcon name="edit" class="select-none text-blue-500 hover:text-blue-800 hover:ease-in duration-300 hover:scale-125" size="md" />
                  </button>
              </div>
          </div>

          <ul class="text-md font-medium text-gray-900">
              <li
                  v-for="task in list?.tasks"
                  :key="task.hash"
                  class="w-full border-b hover:bg-slate-200 border-gray-200 dark:border-gray-600">
                  <div class="flex flex-row gap-2 items-center ps-3">
                      <TCheckBox
                          v-model="task.status"
                      />
                      <label 
                          :class="task.status ? 'line-through' : ''"
                          class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ task?.task_name }}</label>
                  </div>
              </li>
          </ul>
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
</template>


<script setup>
import { ref, inject, onMounted, watchEffect, reactive } from 'vue';
import UpdateTask from './UpdateTask.vue';

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
]);

const lists = ref([]);
const showEditModal = ref(false);
const currentList = ref(null);
const list_details = ref(null);

const searchOptions = reactive({
    page: 1,
    pages: 1,
    limit: 5,
    offset: 0,
    term: "",
    total: 0,
});

const addNewList = () => {
    lists.value.push(props.list);
};

const getAllLists = (term = null) => {
    emit("loading");
    $api.get('/lists', {
        params: {
            term: term,
            limit: searchOptions.limit,
            offset: searchOptions.offset,
        }
    }).then((response) => {
        lists.value = response.data.data;
        searchOptions.total = response.data.count;
        console.log(lists.value);
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
            console.log("List updated successfully");
        })
        .catch((error) => {
            console.error("Error updating list:", error.response?.data?.message || error.message);
        });
};

const handleUpdate = (updatedList) => {
    updateList(updatedList);
    closeEditModal();
};

const closeEditModal = () => {
    showEditModal.value = false;
    currentList.value = null;
};

watchEffect(() => {
    if (props.list != null) {
        addNewList();
    }
    if (props.searchedTerms !== "") {
        searchOptions.offset = 0;
        getAllLists(props.searchedTerms);
    }
});

onMounted(() => {
    getAllLists("");
});
</script>
