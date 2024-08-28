<template>
  <div
    class="group flex items-center"
    :class="[sortable && 'cursor-pointer select-none']"
    @click="toggleSort"
  >
    <div class="flex-auto">{{ label }}</div>
    <TIcon
      v-if="sortable && sortActive"
      :name="_order == 'desc' ? 'arrow_drop_up' : 'arrow_drop_down'"
      size="xs"
    />
    <TPopover
      v-if="searchable"
      v-model:shown="popShowing"
      arrow
      icon="filter_alt"
      iconSize="xs"
      btnClass="rounded-lg p-0.5"
    >
      <template #default="{ close }">
        <div class="grid gap-1 p-1">
          <div class="flex items-center gap-1 text-sm">
            <div class="flex-auto leading-none">
              <span class="font-semibold">Filter:</span> {{ label }}
            </div>
            <TButton
              icon="close"
              iconSize="sm"
              @click="close"
              class="rounded-full"
            />
          </div>
          <TList
            v-model="search"
            :label="label"
            :options="options"
            :loading="loading"
            :portal="false"
            class="min-w-[12rem]"
            innerClass="text-dark bg-light"
          />
          <div class="flex items-center justify-end gap-1">
            <TButton
              label="Ok"
              class="rounded-lg bg-primary bg-glossy px-2 py-1 text-light"
              @click="onSelect(), close()"
            />
            <TButton
              label="Cancel"
              class="rounded-lg px-2 py-1"
              @click="close"
            />
          </div>
        </div>
      </template>
    </TPopover>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, ref, watch } from "vue";
import { vOnClickOutside } from "@vueuse/components";
import { Helpers } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  modelValue: Object,
  api: String,
  label: String,
  columnName: String,
  currentColumn: String,
  key: String,
  searchable: {
    type: Boolean,
    default: false,
  },
  sortable: {
    type: Boolean,
    default: false,
  },
  order: {
    type: String,
    default: "desc",
    validator: (val) => ["asc", "desc"].indexOf(val.toLowerCase()) > -1,
  },
  formatData: {
    type: Function,
    default: (val) => val,
  },
});

const emit = defineEmits(["update:modelValue", "update:order", "sort"]);

const noneSelection = {
  value: null,
  label: "None",
};

const id = Helpers.uniqid();

const loading = ref(false);
const search = ref(props.modelValue?.value ?? null);
const popShowing = ref(false);
const options = ref([]);

const value = computed(() => props.modelValue);
const _order = computed({
  get: () => props.order,
  set: (val) => emit("update:order", val),
});

const sortActive = computed(() => props.columnName == props.currentColumn);

const onSearch = () => {
  if (!!search.value) {
    emit("update:modelValue", search.value);
  }
};

const toggleSort = () => {
  if (props.sortable) {
    _order.value = _order.value == "asc" ? "desc" : "asc";
    emit("sort", props.columnName);
  }
};

const fetchData = () => {
  loading.value = true;

  $api
    .get(props.api)
    .then((response) => {
      options.value = props.formatData(response.data.data);
      options.value.unshift(noneSelection);
    })
    .finally(() => {
      loading.value = false;
    });
};

const onSelect = (val) => {
  emit(
    "update:modelValue",
    options.value.find((item) => item.value == search.value)
  );
};

watch(value, (val) => {
  search.value = val?.value;
});

onMounted(() => {
  fetchData();
});

watch(popShowing, (val) => {
  if (!val) {
    search.value = props.modelValue?.value ?? null;
  }
});
</script>
