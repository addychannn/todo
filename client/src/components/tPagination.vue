<template>
  <nav class="flex select-none items-center">
    <slot
      v-if="!hideBoundaryLinks"
      name="first_page"
      :page="1"
      :change="() => changePage(1)"
      :active="prevActive"
    >
      <TButton
        @click="changePage(1)"
        :icon="startIcon"
        :iconSize="iconSize"
        :class="linkClass"
        :disabled="!prevActive"
      />
    </slot>

    <slot
      v-if="!hideDirectionLinks"
      name="prev_page"
      :page="data.prev"
      :change="() => changePage(data.prev)"
      :active="prevActive"
    >
      <TButton
        @click="changePage(data.prev)"
        :icon="prevIcon"
        :iconSize="iconSize"
        :class="linkClass"
        :disabled="!prevActive"
      />
    </slot>

    <slot
      v-if="!hideEllipsis && data.pages.length > 1 && data.pages[0] != 1"
      name="ellipsis"
    >
      <span :class="ellsipsisClass">{{ ellipsis }}</span>
    </slot>

    <slot
      v-for="page in data.pages"
      :key="`page_${page}`"
      :page="page"
      :active="data.currentPage == page"
      :change="() => changePage(page)"
    >
      <TButton
        @click="changePage(page)"
        :label="`${page}`"
        :class="[linkClass, data.currentPage == page && activeClass]"
        :disabled="data.pageLinks.length <= 0"
      />
    </slot>

    <slot
      v-if="
        !hideEllipsis &&
        data.pages.length > 1 &&
        data.pages[data.pages.length - 1] != data.totalPages
      "
      name="ellipsis"
    >
      <span :class="ellsipsisClass">{{ ellipsis }}</span>
    </slot>

    <slot
      v-if="!hideDirectionLinks"
      name="next_page"
      :page="data.next"
      :change="() => changePage(data.next)"
      :active="nextActive"
    >
      <TButton
        @click="changePage(data.next)"
        :icon="nextIcon"
        :iconSize="iconSize"
        :class="linkClass"
        :disabled="!nextActive"
      />
    </slot>

    <slot
      v-if="!hideBoundaryLinks"
      name="last_page"
      :page="data.totalPages"
      :change="() => changePage(data.totalPages)"
      :active="nextActive"
    >
      <TButton
        @click="changePage(data.totalPages)"
        :icon="endIcon"
        :iconSize="iconSize"
        :class="linkClass"
        :disabled="!nextActive"
      />
    </slot>
  </nav>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";

const props = defineProps({
  // Current page number
  modelValue: {
    type: Number,
    default: 1,
    validator: (val) => val >= 0,
  },

  // total number of data to paginate
  total: {
    type: Number,
    default: 1,
  },

  // Number of computed page (only used for binding total page)
  totalPage: {
    type: Number,
    default: 1,
  },

  // Number of records per page to show
  limit: {
    type: Number,
    default: 10,
    validator: (val) => val > 0,
  },

  // index offset (only used for binding offset)
  offset: {
    type: Number,
    default: 0,
    validator: (val) => {
      return val >= 0;
    },
  },

  // Number of pages to show in the pagination
  maxPages: {
    type: Number,
    default: 5,
    validator: (val) => val > 0,
  },

  linkFormat: {
    type: Function,
    default: (page) => page,
  },

  iconSize: {
    type: String,
    default: "md",
    validator: (val) => {
      return (
        ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl"].indexOf(
          val
        ) > -1
      );
    },
  },

  nextIcon: {
    type: String,
    default: "navigate_next",
  },
  prevIcon: {
    type: String,
    default: "navigate_before",
  },
  startIcon: {
    type: String,
    default: "keyboard_double_arrow_left",
  },
  endIcon: {
    type: String,
    default: "keyboard_double_arrow_right",
  },

  ellipsis: {
    type: String,
    default: "...",
  },

  hideEllipsis: {
    type: Boolean,
    default: false,
  },

  hideBoundaryLinks: {
    type: Boolean,
    default: false,
  },

  hideDirectionLinks: {
    type: Boolean,
    default: false,
  },

  linkClass: {
    type: String,
    default: "p-1 w-8 h-8 rounded-md",
  },

  activeClass: {
    type: String,
    default: "bg-primary text-light bg-glossy",
  },
  ellsipsisClass: {
    type: String,
    default: "px-1 flex items-center justify-center text-center leading-none",
  },
});

const emit = defineEmits([
  "update:modelValue",
  "update:offset",
  "update:limit",
  "update:totalPage",
  "paginate",
]);

const data = ref({
  start: 0,
  end: 10,
  pages: 0,

  currentPage: 0,
  next: null,
  prev: null,

  totalPages: 0,
  pageLinks: [],
});

const page = computed(() => props.modelValue);
const pageTotal = computed(() => props.total);
const pageLimit = computed({
  get: () => props.limit,
  set: (val) => emit("update:limit", val),
});

const nextActive = computed(
  () => data.value.currentPage < data.value.totalPages
);
const prevActive = computed(() => data.value.currentPage > 1);

const changePage = (page, emitData = true) => {
  paginate(
    page,
    props.total,
    props.limit,
    props.maxPages,
    emitData && data.value.currentPage != page
  );
};

const paginate = (currentPage, total, limit, maxPages, emitData = true) => {
  // Calculate the total number of pages
  let totalPages = Math.ceil(total / limit);

  // Ensure the current page is within bounds
  let _currentPage = Math.min(Math.max(1, currentPage), totalPages);

  // Calculate the start and end indices for the current page
  let startIndex = ((_currentPage > 0 ? _currentPage : 1) - 1) * limit;
  let endIndex = Math.min(startIndex + limit, total);

  // Calculate the start and end page numbers for the pagination links
  let startPage = Math.max(1, _currentPage - Math.floor(maxPages / 2));
  let endPage = Math.min(startPage + maxPages - 1, totalPages);

  // Ensure the pagination links are within bounds
  let startOffset = Math.max(0, maxPages - (endPage - startPage + 1));
  let newStartPage = Math.max(1, startPage - startOffset);
  let newEndPage = Math.min(newStartPage + maxPages - 1, totalPages);

  let pages = Array.from(
    { length: newEndPage - newStartPage + 1 },
    (_, i) => i + newStartPage
  );

  // Return an object containing the pagination information
  data.value = {
    start: startIndex,
    end: endIndex,
    pages: pages.length > 0 ? pages : [1],
    currentPage: _currentPage,
    next: null,
    prev: null,
    totalPages: totalPages,
    pageLinks: [],
  };

  pages.forEach((page) => {
    data.value.pageLinks.push(props.linkFormat(page));
  });

  // Add ellipsis at the beginning of the pagination links if necessary
  let prevPage = _currentPage - 1;
  data.value.prev = prevPage;
  if (newStartPage > 1) {
    let firstPage = 1;
    let prevEllipsis = prevPage > 1 ? props.ellipsis : "";
    data.value.pageLinks.unshift(firstPage, prevPage, prevEllipsis);
  }

  // Add ellipsis at the end of the pagination links if necessary
  let nextPage = _currentPage + 1;
  data.value.next = nextPage;
  if (newEndPage < totalPages) {
    let lastPage = totalPages;

    let nextEllipsis = nextPage < totalPages ? props.ellipsis : "";
    data.value.pageLinks.push(nextEllipsis, nextPage, lastPage);
  }

  emit("update:offset", startIndex);
  emit("update:totalPage", totalPages);
  emit("update:modelValue", _currentPage);
  if (emitData) {
    emit("paginate", data.value);
  }
};

watch(page, (val) => {
  changePage(props.modelValue, false);
});

watch(pageLimit, (val) => {
  changePage(props.modelValue);
});

watch(pageTotal, (val) => {
  changePage(props.modelValue, false);
});

onMounted(() => {
  changePage(props.modelValue, false);
});

defineExpose({
  changePage,
});
</script>
