<template>
  <div class="flex flex-col">
    <div
      class="group flex items-center px-2 pb-0.5 pt-3 font-bold"
      :class="[sortable && 'cursor-pointer select-none']"
      @click="toggleSort"
    >
      <div class="flex-auto">{{ label }}</div>
      <TIcon
        v-if="sortable && sortActive"
        :name="_order == 'desc' ? 'arrow_drop_up' : 'arrow_drop_down'"
        size="xs"
      />

      <TSpinnerOrbit v-if="loading" class="aspect-square w-4" />
      <TPopover
        v-else-if="searchable"
        v-model:shown="popShowing"
        icon="filter_alt"
        iconSize="xs"
        btnClass="rounded-lg p-0.5"
      >
        <template #default="{ close }">
          <div
            class="flex max-h-72 w-[100dvw] max-w-[15rem] flex-col gap-1 p-1"
          >
            <div
              class="flex items-center gap-1 border-b border-foreground/25 text-sm"
            >
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
            <div
              class="TScroll flex-auto overflow-y-auto bg-background-accent scrollbar-w-1"
            >
              <div class="grid gap-1">
                <template v-for="option in options" :key="option.id">
                  <TButton class="rounded-md" @click="selectRole(option)">
                    <div
                      class="pointer-events-none flex items-center gap-1 px-3 py-1"
                    >
                      <TIcon
                        :name="
                          isSelected(option)
                            ? 'check_box'
                            : 'check_box_outline_blank'
                        "
                      />
                      {{ option.label }}
                    </div>
                  </TButton>
                </template>
              </div>
            </div>
            <div
              class="flex items-center justify-end gap-1 border-t border-foreground/25"
            >
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

    <div
      v-if="props.modelValue.length > 0"
      class="flex items-center gap-1 px-2 leading-none text-gray-400"
    >
      <div class="flex flex-auto items-center gap-1">
        <template v-for="role in props.modelValue" :key="role.value">
          <span
            class="rounded-full border border-foreground/25 px-2 font-normal leading-none"
          >
            {{ role.label }}
          </span>
        </template>
      </div>
      <TButton
        icon="close"
        iconSize="xs"
        class="rounded-full p-0.5"
        @click="emit('update:modelValue', [])"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, ref, watch } from "vue";
import { vOnClickOutside } from "@vueuse/components";
import { Helpers } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  modelValue: Array,
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
    default: (val) => val.map((item) => ({ label: item.name, value: item.id })),
  },
});

const emit = defineEmits(["update:modelValue", "update:order", "sort"]);

const id = Helpers.uniqid();

const loading = ref(false);
const popShowing = ref(false);
const options = ref([]);
const selected = ref(props.modelValue ?? []);

const _order = computed({
  get: () => props.order,
  set: (val) => emit("update:order", val),
});

const sortActive = computed(() => props.columnName == props.currentColumn);
const value = computed(() => props.modelValue);

const toggleSort = () => {
  if (props.sortable) {
    _order.value = _order.value == "asc" ? "desc" : "asc";
    emit("sort", props.columnName);
  }
};

const fetchData = () => {
  loading.value = true;

  $api
    .get("/users/roles")
    .then((response) => {
      options.value = props.formatData(response.data.data);
    })
    .finally(() => {
      loading.value = false;
    });
};

const onSelect = () => {
  emit("update:modelValue", selected.value);
};

const selectRole = (role) => {
  if (isSelected(role)) {
    selected.value = selected.value.filter((item) => item.value != role.value);
  } else {
    selected.value.push(role);
  }
};

const isSelected = (role) => {
  return !!selected.value?.find((item) => item.value == role.value);
};

watch(value, (val) => {
  selected.value = [...val] ?? [];
});

watch(popShowing, (val) => {
  if (val) {
    selected.value = [...props.modelValue] ?? [];
  }
});

onMounted(() => {
  fetchData();
});
</script>
