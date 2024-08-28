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
      @sort="(val) => emit('sort', val)"
    >
      <template #default="{ model, onSearch, close }">
        <div class="min-w-max">
          <DatePicker
            :modelValue="model"
            @update:modelValue="(val) => (onSearch(val), close())"
            :inline="{ input: true }"
            :text-input="{ format: 'MM/dd/yyyy' }"
            :autoApply="false"
            :enable-time-picker="false"
            :week-start="0"
            :max-date="new Date()"
            range
            prevent-min-max-navigation
            format="MMMM dd, yyyy"
            class="first:[&>div]:mb-2 first:[&>div]:w-full"
            input-class-name="!bg-light !text-dark"
          />
        </div>
      </template>
      <template #selected="{ search, clear }">
        <div class="flex items-center gap-1 leading-tight text-gray-400">
          <div class="flex-auto text-sm font-normal italic">
            {{
              [...new Set(search.map((item) => Helpers.formatDate(item)))]
                .filter((n) => n)
                .join(" - ")
            }}
          </div>
          <TButton
            icon="close"
            iconSize="xs"
            class="rounded-full p-0.5"
            @click="clear"
          />
        </div>
      </template>
    </SearchableColumn>
  </component>
</template>
<script setup>
import { ref, defineAsyncComponent } from "vue";
import { useVModel } from "@vueuse/core";
import { Helpers } from "@/scripts";

const DatePicker = defineAsyncComponent(() => import("@vuepic/vue-datepicker"));

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
