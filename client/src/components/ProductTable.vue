<template>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-10">
      <table class="w-full text-sm text-center rtl:text-center text-gray-500 dark:text-gray-400">
        <!-- Table Header -->
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">Laptop Name</th>
            <th scope="col" class="px-6 py-3">Brand</th>
            <th scope="col" class="px-6 py-3">Color</th>
            <th scope="col" class="px-6 py-3">Price</th>
            <th scope="col" class="px-6 py-3">Deleted at</th>
            <th scope="col" class="px-6 py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in laptops" :key="product?.hash" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ product?.name }}</td>
            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ product?.brand?.name }}</td>
            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ product?.color?.name }}</td>
            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ numeral(product?.price).format('0,0.00') }}</td>
               <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ product?.deleted_at }}</td>
            <td class="grid grid-cols-3 px-6 py-4">
              <!-- Edit button -->
              <button @click="openEditModal(product.hash)" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000FF">
                  <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                </svg>
              </button>
              <!-- Delete button -->
              <button @click="openConfirmModal(product.hash,'delete')" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FF0000">
                  <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                </svg>
              </button>
              <!-- Restore -->
              <button @click="openConfirmModal(product.hash,'restore')" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#008000"><path d="M440-320h80v-166l64 62 56-56-160-160-160 160 56 56 64-62v166ZM280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z"/></svg>
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

  
    <!-- Edit Product Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 relative w-full max-w-md">
        <button @click="closeEditModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <EditProduct 
        :product="laptop_details" 
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
            @confirm="deleteLaptop()" 
            @cancel="openConfirmModal()"
            />
            <RestoreConfirmation 
            :text="'Restore this deleted item?'"
            v-if="actionMode == 'restore'"
            @confirm="restoreLaptop()" 
            @cancel="openConfirmModal()"
            />
     </TDialog>  
   
  </template>
  
  <script setup>
    import { ref, inject, onMounted, watchEffect, reactive } from 'vue';
    import EditProduct from '../pages/products/EditProduct.vue';
    import DeleteConfirmation from '../pages/Confirmation.vue';
    import RestoreConfirmation from '../pages/Confirmation.vue';
    import numeral from 'numeral';

  const $api = inject('$api');
  
  const props = defineProps({
    laptop: {
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

  const laptops = ref([]);
  const showConfirmModal = ref(false);
  const showEditModal = ref(false);
  const laptop_details = ref(null);
  const laptopToDelete = ref(null);
  const currentLaptop = ref(null);
  const actionMode = ref(null);
  
  const searchOptions = reactive({
    page : 1,
    pages: 1,
    limit: 5,
    offset: 0,
    term: "",
    total: 0,

  });

  const addNewLaptop = () => {
    laptops.value.push(props.laptop);
    console.log(props.laptop);
  };
  
  const getAllLaptops = (term = null) => {
    emit("loading")
    $api.get('/products',{
        params:{
            term: term,
            limit: searchOptions.limit,
            offset: searchOptions.offset,
        }
    }).then((response) => {
      laptops.value = response.data.data;
      searchOptions.total = response.data.count;
      console.log(laptops.value);

    }).finally(()=>{
        emit("doneLoading")
    })
    ;
  };

  const deleteLaptop = () => {
  $api.delete(`/delete/laptop/${laptopToDelete.value}`)
    .then((response) => {
      if (response.data.type === 'negative') {
        laptops.value = laptops.value.filter(laptop => laptop?.hash !== response.data.data?.hash);
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
      console.error("Error deleting laptop:", error.response?.data?.message || error.message);
    })
   
    
    ;
};

  const restoreLaptop =()=>{
    $api.get(`/products/restore/${laptopToDelete.value}`)
      .then((response) => {
       if (response.data.type === 'positive'){
        const index = laptops.value.findIndex(laptop => laptop.hash === response.data.data?.hash);
        if (index !== -1) {
          laptops.value[index] = response.data.data;
        }
        openConfirmModal();
       }
        })
      .catch((error) => {
        console.error("Error deleting laptop:", error.response?.data?.message || error.message);
      });
  }
  
  const updateLaptop = (updatedLaptop) => {
    $api.patch(`/update/laptop/${updatedLaptop.hash}`, updatedLaptop)
      .then(() => {
        const index = laptops.value.findIndex(laptop => laptop.hash === updatedLaptop.hash);
        if (index !== -1) {
          laptops.value[index] = updatedLaptop;
        }
        console.log("Laptop updated successfully");
      })
      .catch((error) => {
        console.error("Error updating laptop:", error.response?.data?.message || error.message);
      });
  };
  
  const openConfirmModal = (hash = null, mode = null) => {
    laptopToDelete.value = hash;
    actionMode.value= mode;
    showConfirmModal.value = !showConfirmModal.value ;
  };

  const openEditModal = (laptop) => {
    currentLaptop.value = { ...laptop };
    showEditModal.value = !showEditModal.value ;
    laptop_details.value = laptops.value.find((item)=>item.hash==laptop)
  };
  
  const handleUpdate = (updatedLaptop) => {
    updateLaptop(updatedLaptop);
    closeEditModal();
  };
  
  const closeEditModal = (event) => {
    showEditModal.value = false;
    currentLaptop.value = null;
    const index = laptops.value.findIndex(laptop => laptop.hash === event?.hash);
        if (index !== -1) {
          laptops.value[index] = event;
        }
  };
  
  watchEffect(() => {
    console.log(props.searchedTerms )
    if (props.laptop != null) {
      addNewLaptop();
    }
    if (props.searchedTerms != ""){
        searchOptions.offset = 0;
        getAllLaptops(props.searchedTerms);
    }
  });
  
  onMounted(() => {
    getAllLaptops("");
  });
  </script>
  