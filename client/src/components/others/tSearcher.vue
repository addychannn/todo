<template>
  <TCard
    class="mt-20 max-h-[calc(95vh_-_8rem)] w-[95dvw] max-w-[596px] bg-background-accent text-foreground"
    @keyup.esc="emit('close')"
    tabindex="-1"
  >
    <TCardHeader class="border-b-0 pt-3" ref="headerComponent">
      <TInput
        v-model="data.search"
        :label="`Search ${title}`"
        class="w-full !pb-0"
        innerClass="bg-light text-dark"
        @keyup.enter="search(null)"
      >
        <template #prepend>
          <TIcon name="search" />
        </template>
        <template v-if="closable" #append>
          <button
            tabindex="-1"
            class="rounded border px-1 text-xs uppercase shadow-inner hover:bg-gray-300/25"
            @click="emit('close')"
            role="button"
          >
            <span class="sr-only">Press escape to close</span>
            <span aria-hidden="true">esc</span>
          </button>
        </template>
      </TInput>
    </TCardHeader>
    <TCardBody class="min-h-[12rem] overflow-auto">
      <div class="mb-2 flex items-center text-sm leading-none">
        <div class="flex flex-auto gap-1">
          <span
            class="mr-2 pr-2"
            :class="[(!!selectedFilter || !!selectedColumn) && 'border-r']"
          >
            {{ data.settings ? "Settings" : "Recent" }}
          </span>
          <span
            v-if="data.filter && selectedFilter"
            class="origin-center select-none rounded-full border bg-gray-300 px-3 shadow-sm hover:scale-105 dark:bg-gray-500 dark:shadow-white"
          >
            {{ selectedFilter.label }}
          </span>
          <span
            v-if="data.column && selectedColumn"
            class="origin-center select-none rounded-full border bg-gray-300 px-3 shadow-sm hover:scale-105 dark:bg-gray-500 dark:shadow-white"
          >
            {{ selectedColumn.label }}
          </span>
        </div>

        <TButton
          v-if="columns.length > 0 || filters.length > 0"
          @click="data.settings = !data.settings"
        >
          <TIcon
            name="settings"
            size="sm"
            class="origin-center select-none leading-none transition-all hover:scale-105"
            :class="[
              data.settings && 'text-gray-700 dark:text-gray-100',
              !data.settings && 'text-gray-400',
            ]"
          />
        </TButton>
      </div>
      <div
        v-if="data.settings"
        class="flex flex-col gap-2 rounded-md border bg-slate-100 p-2 dark:bg-slate-700"
      >
        <div
          v-if="columns.length > 0"
          class="flex flex-col rounded-2xl border bg-slate-50 px-3 dark:bg-slate-500"
        >
          <div class="flex-auto select-none text-sm uppercase opacity-50">
            Search By:
          </div>
          <div class="grid grid-cols-12 gap-2 p-2">
            <TRadio
              v-for="column in columns"
              :key="`search_col_${column.label}`"
              v-model="data.column"
              class="col-span-4"
              :value="column.value"
              :label="column.label"
              name="column"
            />
          </div>
        </div>
        <div
          v-if="filters.length > 0"
          class="flex flex-col rounded-2xl border bg-slate-50 px-3 dark:bg-slate-500"
        >
          <div class="flex-auto select-none text-sm uppercase opacity-50">
            Filter:
          </div>
          <div class="grid grid-cols-12 gap-2 p-2">
            <TRadio
              v-for="filter in filters"
              :key="`user_filter_${filter.value}`"
              v-model="data.filter"
              class="col-span-4"
              :value="filter.value"
              :label="filter.label"
              name="filter"
            />
          </div>
        </div>
      </div>
      <div v-else class="flex flex-col gap-2 rounded-md border p-2">
        <template v-if="store.getSearchHistory?.length <= 0">
          <div
            class="flex-auto text-center font-semibold italic text-gray-400/75"
          >
            No recent search!
          </div>
        </template>
        <template
          v-for="history in store.getSearchHistory"
          :key="`user_search_history_${history.timestamp}`"
        >
          <div
            class="flex cursor-pointer items-center rounded-md border bg-slate-300 py-2 leading-none transition-colors hover:bg-slate-800/75 hover:text-slate-50 dark:bg-slate-600/75 dark:hover:bg-slate-900/75"
            @click="search(history)"
          >
            <div class="flex items-center px-2 leading-none">
              <TIcon name="history" size="lg" class="text-gray-400" />
            </div>
            <div class="flex flex-auto flex-col justify-center leading-none">
              <div
                class="leading-none"
                :class="[
                  !history.search && 'text-xs italic leading-none',
                  !!history.search && 'text-xl font-semibold',
                ]"
              >
                {{ !history.search ? "(empty)" : history.search }}
              </div>
              <div
                class="flex flex-col-reverse gap-1 pr-3 leading-none md:flex-row md:items-center"
              >
                <div class="px-1 text-xs leading-none">
                  {{
                    dayjs(history.timestamp).format("MMMM DD, YYYY hh:mm:ss a")
                  }}
                </div>
                <div class="flex flex-wrap gap-2 px-1 leading-none md:border-l">
                  <span
                    v-if="!!getSelectedFilter(history.filter)"
                    class="rounded-full border bg-gray-500/50 px-2 py-1 text-xs leading-none shadow"
                  >
                    {{ getSelectedFilter(history.filter).label }}
                  </span>
                  <span
                    v-if="!!getSelectedCol(history.column)"
                    class="rounded-full border bg-gray-500/50 px-2 py-1 text-xs leading-none shadow"
                  >
                    {{ getSelectedCol(history.column).label }}
                  </span>
                </div>
              </div>
            </div>
            <div class="flex items-center px-3">
              <TButton
                class="aspect-square rounded-full p-1"
                @click.prevent.stop="store.removeHistory(history)"
              >
                <TIcon name="close" size="sm" />
              </TButton>
            </div>
          </div>
        </template>
      </div>
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        @click="search(null)"
        class="overflow-auto rounded-full border bg-primary bg-glossy px-3 py-2 text-white shadow-md transition-colors"
        label="Search"
      >
      </TButton>
      <TButton
        @click="emit('close')"
        class="overflow-auto rounded-full px-3 py-2"
        label="Cancel"
      >
      </TButton>
    </TCardFooter>
  </TCard>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import dayjs from "dayjs";

const props = defineProps({
  store: Object,
  title: {
    type: String,
    default: "",
  },
  closable: {
    type: Boolean,
    default: false,
  },
  columns: {
    type: Object,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => [],
  },
});

const emit = defineEmits(["search", "close"]);

const headerComponent = ref(null);

const data = reactive({
  search: "",
  column: null,
  filter: null,

  settings: false,
});

const selectedColumn = computed(() => getSelectedCol(data.column));
const selectedFilter = computed(() => getSelectedFilter(data.filter));

const getSelectedCol = (val) =>
  props.columns.find((column) => column.value == val);
const getSelectedFilter = (val) =>
  props.filters.find((filter) => filter.value == val);

const search = (history = null) => {
  let searchData = {
    search: history?.search ?? data.search,
    column: history?.column ?? data.column,
    filter: history?.filter ?? data.filter,
  };
  props.store.addHistory(searchData);
  emit("search", searchData);
};

onMounted(() => {
  headerComponent.value?.$el?.firstElementChild?.focus();
  props.columns.forEach((element) => {
    if (!!element.default) {
      data.column = element.value;
    }
  });
});
</script>
