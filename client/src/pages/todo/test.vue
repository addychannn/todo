<template>
  <div class="relative overflow-x-auto shadow-lg sm:rounded-lg p-10">
        <table class="w-full text-sm text-center rtl:text-center text-gray-500 dark:text-gray-400">
          <!-- Table Header -->
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-3">Brand Name</th>
              <th scope="col" class="px-6 py-3">Deleted at</th>
              <th scope="col" class="px-6 py-3">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="brand in brands" :key="brand?.hash" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
              <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ brand?.name }}</td>
              <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ brand?.deleted_at }}</td>
              <td class="grid grid-cols-3 px-6 py-4">
                <!-- Edit button -->
                <button @click="openEditModal(brand.hash)" type="button"
                :disabled="brand?.deleted_at?true:false" 
                >
                  <!-- <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000FF">
                    <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                  </svg> -->
                  <TIcon name="edit" class="select-none text-blue-500 hover:text-gray-300 hover:rotate-180 hover:ease-in duration-1000" size="md" />
                </button>
                <!-- Delete button -->
                <button @click="openConfirmModal(brand.hash,'delete')" type="button">
                  <!-- <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FF0000">
                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                  </svg> -->
                  <TIcon name="delete" class="select-none text-red-500 hover:text-gray-300" size="md" />
                </button>
                <!-- Restore -->
                <button  @click="openConfirmModal(brand.hash,'restore')" type="button">
                  <!-- <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#008000"><path d="M440-320h80v-166l64 62 56-56-160-160-160 160 56 56 64-62v166ZM280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z"/></svg> -->
                  <TIcon name="restore_from_trash" class="select-none text-emerald-500 hover:text-gray-300" size="md" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
  </div>

      <TPagination
      v-model="searchOptions.page"
      v-model:limit="searchOptions.limit"
      v-model:offset="searchOptions.offset"
      v-model:totalPage="searchOptions.pages"
      :total="searchOptions.total"
      class="justify-center gap-4 pt-10"
      linkClass="aspect-square w-6 p-1 text-sm leading-none flex items-center justify-center rounded-md"
      @paginate="getAllBrands(searchOptions.term)"
  />

  <!-- Edit Brand Modal -->
  <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 relative w-full max-w-md">
      <button @click="closeEditModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
      <EditBrand 
      :brand="brand_details" 
      @update="handleUpdate" 
      @close="closeEditModal($event)" />
    </div>
  </div>

     <!-- Confirmation Modal -->
     <TDialog 
      :modelValue="showConfirmModal" 
      persistent
      class="relative bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-200">
          <DeleteConfirmation
              :text="'Are you sure you to delete?'"
              v-if="actionMode == 'delete'"
              @confirm="deleteBrand()" 
              @cancel="openConfirmModal()"
              />
              <RestoreConfirmation
              :text="'Restore this deleted item?'"
              v-if="actionMode == 'restore'"
              @confirm="restoreBrand()" 
              @cancel="openConfirmModal()"
              />
   </TDialog>  

  </template>

  <script setup>
  import { ref, inject, onMounted, watchEffect, reactive } from 'vue';
  import EditBrand from './EditBrand.vue';
  import DeleteConfirmation from '../Confirmation.vue';
  import RestoreConfirmation from '../Confirmation.vue';
  import { notify } from '@/scripts';
  
  const $api = inject('$api');

  const props = defineProps({
  brand: {
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

const brands = ref([]);
const showConfirmModal = ref(false);
const showEditModal = ref(false);
const brand_details =ref(null);
const brandToDelete = ref(null);
const currentBrand = ref(null);
const actionMode = ref(null);

const searchOptions = reactive({
  page : 1,
  pages: 1,
  limit: 5,
  offset: 0,
  term: "",
  total: 0,

});

const addNewBrand = () => {
  brands.value.push(props.brand);
  console.log(props.brand)
}

const getAllBrands = (term = null) => {
  emit("loading")
  $api.get('/brands',{
      params:{
          term: term,
          limit: searchOptions.limit,
          offset: searchOptions.offset,
      }
  }).then((response) => {
    brands.value = response.data.data;
    searchOptions.total = response.data.count;
    console.log(brands.value);

  }).finally(()=>{
      emit("doneLoading")
  });
};

const deleteBrand = () => {
$api.delete(`/delete/brand/${brandToDelete.value}`)
  .then((response) => {
    if (response.data.type === 'negative') {
      brands.value = brands.value.filter(brand => brand?.hash !== response.data.data?.hash);
    }
    notify({
        group: "main",
        title: response.data.title,
        type: response.data.type,
        duration: response.data.duration
      });
    openConfirmModal(); 
  })
  .catch((error) => {
    console.error("Error deleting brand:", error.response?.data?.message || error.message);
  })
}

const restoreBrand =()=>{
  $api.get(`/brands/restore/${brandToDelete.value}`)
    .then((response) => {
     if (response.data.type === 'positive'){
      const index = brands.value.findIndex(brand => brand.hash === response.data.data?.hash);
      if (index !== -1) {
        brands.value[index] = response.data.data;
      }
      openConfirmModal();
     }
      })
    .catch((error) => {
      console.error("Error deleting brand:", error.response?.data?.message || error.message);
    });
}


const openConfirmModal = (hash = null, mode = null) => {
  brandToDelete.value = hash;
  actionMode.value= mode;
  showConfirmModal.value = !showConfirmModal.value ;
};

const openEditModal = (brand) => {
  currentBrand.value = { ...brand };
  showEditModal.value = !showEditModal.value ;
  brand_details.value = brands.value.find((item)=>item.hash==brand)
};

const handleUpdate = (updatedBrand) => {
  updateBrand(updatedBrand);
  closeEditModal();
};

const closeEditModal = (event) => {
  showEditModal.value = false;
  currentBrand.value = null;
  const index = brands.value.findIndex(brand => brand.hash === event?.hash);
      if (index !== -1) {
        brands.value[index] = event;
      }
};

watchEffect(() => {
  console.log(props.searchedTerms )
  if (props.brand != null) {
    addNewBrand();
  }
  if (props.searchedTerms != ""){
      searchOptions.offset = 0;
      getAllBrands(props.searchedTerms);
  }
});

onMounted(() => {
  getAllBrands("");
});


</script>