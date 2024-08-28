<template>
  <div class="relative flex flex-col gap-1 rounded-lg">
    <div class="flex-auto">
      <TPagination
        v-model="pagination.page"
        v-model:limit="pagination.limit"
        v-model:offset="pagination.offset"
        v-model:totalPage="pagination.pages"
        :total="pagination.total"
        :class="[pagination.pages <= 1 && '!hidden']"
        :maxPages="1"
        hideEllipsis
        iconSize="sm"
        class="justify-end gap-1"
        linkClass="aspect-square w-6 p-1 text-sm leading-none flex items-center justify-center rounded-md"
        @paginate="getActivies"
      />
      <table class="w-full">
        <thead>
          <tr
            class="border-b-4 border-foreground/25 font-semibold [&>th]:text-start"
          >
            <DateColumn
              :search="search.date"
              label="Date/Time"
              column="date"
              class="w-32 transition-all duration-1000"
              sortable
              v-model:pagination="pagination"
              @columnSearch="(val) => onColumnSearch('date', val)"
              @sort="onSort"
            />
            <th class="px-2 pb-0.5 pt-3">Actions</th>
          </tr>
        </thead>
        <tbody class="odd:[&>tr]:bg-foreground/5">
          <tr v-if="logs.length <= 0">
            <td
              colspan="100%"
              class="text-center font-semibold italic text-gray-400"
            >
              No Activies!
            </td>
          </tr>
          <tr
            v-for="log in logs"
            :key="log.id"
            class="border-b border-foreground/25 [&>td]:transition-colors [&>td]:hover:bg-foreground/10"
          >
            <td class="px-1">
              <div class="grid leading-tight">
                <div class="font-semibold leading-tight">
                  {{ Helpers.formatDate(log.date, null, "MMM DD, YYYY") }}
                </div>
                <div class="text-xs leading-tight">
                  {{ Helpers.formatDate(log.date, null, "hh:mm A") }}
                </div>
              </div>
            </td>
            <td class="px-1 leading-tight">
              {{ log.action }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <TPagination
      v-model="pagination.page"
      v-model:limit="pagination.limit"
      v-model:offset="pagination.offset"
      v-model:totalPage="pagination.pages"
      :total="pagination.total"
      :class="[pagination.pages <= 1 && '!hidden']"
      hideEllipsis
      iconSize="sm"
      class="justify-center gap-1"
      linkClass="aspect-square w-6 p-1 text-sm leading-none flex items-center justify-center rounded-md"
      @paginate="getActivies"
    />
    <TInnerLoading :active="loading" text="Getting logs..." />
  </div>
</template>
<script setup>
import { inject, onMounted, ref } from "vue";
import { useVModel } from "@vueuse/core";
import { useSearcher } from "@/plugins/composables";
import { Helpers } from "@/scripts";
import dayjs from "dayjs";
import DateColumn from "../settings/utils/common/columnSearch/dateRange.vue";

const $api = inject("$api");

const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(["update:modelValue"]);

const user = useVModel(props, "modelValue", emit);

const { searcher, pagination } = useSearcher("/user/activites", {
  pagination: { sort: "date", order: "desc" },
});

const search = ref({
  date: [dayjs().startOf("day"), dayjs().endOf("day")],
});
const loading = ref(false);
const logs = ref([]);
const levels = ref({
  data: [],
  loading: false,
});

const getActivies = () => {
  loading.value = true;
  let tmp = null;
  if (!!search.value?.date) {
    tmp = [
      dayjs(search.value?.date?.[0] ?? new Date())
        .startOf("day")
        .format(),
      dayjs(search.value?.date?.[1] ?? new Date())
        .endOf("day")
        .format(),
    ];
  }

  searcher({ date: tmp })
    .then((response) => {
      pagination.value.total = response.data.count;
      logs.value = response.data.data;
    })
    .finally(() => {
      loading.value = false;
    });
};

const getLevels = () => {
  levels.value.loading = true;
  $api
    .get("/log/levels")
    .then((response) => {
      let lvls = {};
      Object.keys(response.data).forEach((item) => {
        Object.assign(lvls, {
          [response.data[item]]: Helpers.CapitalizeFirstLetter(item),
        });
      });

      levels.value.data = lvls;
    })
    .finally(() => {
      levels.value.loading = false;
    });
};

const getLevelName = (level) => {
  return levels.value.data[level];
};

const onSort = (val) => {
  pagination.value.sort = val;
  getActivies();
};

const onColumnSearch = (name, val) => {
  Object.assign(search.value, { [name]: val });
  getActivies();
};

onMounted(() => {
  getActivies();
  getLevels();
});
</script>
