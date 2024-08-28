<template>
  <component :is="tag">
    <SearchableColumn
      :label="label"
      :columnName="column"
      :modelValue="search"
      @update:modelValue="(val) => emit('columnSearch', val)"
      v-model:order="_pagination.order"
      :currentColumn="_pagination.sort"
      searchable
      :sortable="sortable"
      :closeOnOutsideClick="false"
      @sort="(val) => emit('sort', val)"
    />
  </component>
</template>

<script setup>
import { useVModel } from "@vueuse/core";
const props = defineProps({
  tag: {
    type: [Element, String],
    default: "th",
  },
  sortable: {
    type: Boolean,
    default: false,
  },
  search: [Object, String, Number],
  pagination: Object,
  label: String,
  column: String,
});

const emit = defineEmits(["columnSearch", "sort", "update:pagination"]);

const _pagination = useVModel(props, "pagination", emit);
</script>
