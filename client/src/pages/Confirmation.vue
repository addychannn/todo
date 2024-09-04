<template>
    <Page>
        <div class="flex flex-col p-10 items-center dark:bg-dark">
            <div class="flex flex-col">
                <TIcon :name="props.icon" size="md" :class="props.icon == 'warning' ? 'text-red-500' : 'text-orange-500'"></TIcon>
            </div>
            <div class="flex flex-col pb-2 text-center">
                <span class="font-bold">{{ props.text }}</span>
                <span v-if="props.subText" class="text-sm text-red-500">{{ props.subText }}</span>
            </div>
            <div v-if="props.requireRemarks" class="flex flex-col pb-4 w-full">
                <quill-editor 
                    v-model:content="reason" 
                    content-type="html" 
                    theme="snow"
                    class="w-full h-full border text-lg bg-white" 
                    :toolbar="data.toolbarOptions"
                    placeholder="Input reason here (required)" 
                    required
                />
            </div>
            <div class="flex flex-row items-center  gap-2">
                <div @click="cancel" class="flex flex-col">
                    <div class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                        <span>{{ props.cancelButtonName }}</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div @click="confirm" class="text-white bg-green-700 border focus:outline-none hover:bg-green-800 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-800 dark:text-white dark:border-green-600 dark:hover:bg-green-700 dark:hover:border-green-600 dark:focus:ring-green-700">
                        <span>{{ props.confirmButtonName }}</span>
                    </div>
                </div>
                
            </div>
        </div>
    </Page>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { notify } from '@/scripts';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    text:{
        type:String,
        default:null
    },
    subText:{
        type:String,
        default:null
    },
    icon:{
        type:String,
        default:null
    },
    requireRemarks:{
        type: Boolean,
        default: false
    },
    confirmButtonName:{
        type: String,
        default: 'Confirm'
    },
    cancelButtonName:{
        type: String,
        default: 'Cancel'
    }

});

const emit = defineEmits(['confirm','cancel']);

const reason = ref();

const data = reactive({
    toolbarOptions: [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
        [{ 'color': [] }],          // dropdown with defaults from theme
        [{ 'align': [] }],
        ['clean']                                         // remove formatting button
    ]
});

const confirm = () => {
    if(props.requireRemarks){
        if(reason.value == null){
            notify({group:"main", title:"Reason field is required", text:"Please fill up reason field.", type:"negative"},'3000')
        }
        else{
            emit('confirm', reason.value)
        }
    }
    else{
        emit('confirm')
    }
};

const cancel = () => {
    emit('cancel')
};
</script>