<template>
  <div class="flex flex-col">
    <div
      class="group flex items-center px-2 pb-0.5 pt-1 font-bold"
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
        @click.stop
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
            <slot
              :id="id"
              :close="() => close()"
              :onSearch="(query) => onSearch(query)"
              :model="search"
            >
              <TInput
                v-model="search"
                label="Search"
                innerClass="bg-light text-dark pr-0 overflow-hidden"
                class="min-w-max"
                @keyup.enter="onSearch(search), close()"
              >
                <template #append>
                  <TButton
                    label="Ok"
                    class="self-stretch bg-primary bg-glossy px-2 text-light"
                    @click="onSearch(search), close()"
                  />
                </template>
              </TInput>
            </slot>
          </div>
        </template>
      </TPopover>
    </div>
    <slot
      v-if="!!modelValue"
      name="selected"
      :search="search"
      :clear="() => onSearch(null)"
    >
      <div class="flex items-center gap-1 leading-tight text-gray-400">
        <div class="flex-auto px-1 text-sm font-normal italic">
          {{ modelValue }}
        </div>
        <TButton
          icon="close"
          iconSize="xs"
          class="rounded-full p-0.5"
          @click="onSearch(null)"
        />
      </div>
    </slot>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { Helpers } from "@/scripts";
const props = defineProps({
  modelValue: [Object, String],
  label: String,
  columnName: String,
  currentColumn: String,
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
});

const emit = defineEmits(["update:modelValue", "update:order", "sort"]);

const id = Helpers.uniqid();

const search = ref(props.modelValue);
const popShowing = ref(false);

const value = computed(() => props.modelValue);

const _order = computed({
  get: () => props.order,
  set: (val) => emit("update:order", val),
});

const sortActive = computed(() => props.columnName == props.currentColumn);

const onSearch = (query) => {
  emit("update:modelValue", query);
};

const toggleSort = () => {
  if (props.sortable) {
    _order.value = _order.value == "asc" ? "desc" : "asc";
    emit("sort", props.columnName);
  }
};

watch(value, (val) => {
  search.value = val;
});

watch(popShowing, (val) => {
  if (!val) {
    search.value = props.modelValue;
  }
});
</script>
