<template>
  <th class="min-w-[12.5rem]">
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
      <template #default="{ close, onSearch }">
        <div class="grid min-w-[14rem] gap-1">
          <div
            class="TScroll max-h-96 flex-auto overflow-y-auto bg-background-accent scrollbar-w-1"
          >
            <div class="grid gap-1">
              <template v-for="barangay in barangays" :key="barangay.code">
                <TButton class="rounded-md" @click="selectBarangay(barangay)">
                  <div
                    class="pointer-events-none flex items-center gap-1 px-3 py-1"
                  >
                    <TIcon
                      :name="
                        isSelected(barangay)
                          ? 'check_box'
                          : 'check_box_outline_blank'
                      "
                    />
                    {{ barangay.name }}
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
              @click="onSearch(selected?.length > 0 ? selected : null), close()"
            />
            <TButton
              label="Cancel"
              class="rounded-lg px-2 py-1"
              @click="close"
            />
          </div>
        </div>
      </template>
      <template #selected="{ clear }">
        <div
          v-if="search.length > 0"
          class="flex items-start gap-1 px-2 text-sm leading-none text-gray-400"
        >
          <div
            class="flex w-[12rem] flex-auto flex-wrap items-center gap-1 whitespace-nowrap"
          >
            <template
              v-for="barangay in search.sort(
                (a, b) => b.name.length - a.name.length
              )"
              :key="`${barangay.name}_${barangay.id}`"
            >
              <div
                class="flex max-w-full items-center gap-0.5 rounded-full border border-foreground/25 py-1 pl-2 pr-1 font-normal uppercase leading-none"
              >
                <div class="line-clamp-1 flex-auto leading-tight">
                  {{ barangay.name }}
                </div>
                <div class="flex items-center justify-center">
                  <TButton
                    icon="close"
                    iconSize="xs"
                    class="aspect-square w-4 rounded-full"
                    @click="
                      selectBarangay(barangay),
                        emit(
                          'columnSearch',
                          selected?.length > 0 ? selected : null
                        )
                    "
                  />
                </div>
              </div>
            </template>
          </div>
          <span class="leading-none">
            <TButton
              icon="close"
              iconSize="xs"
              class="aspect-square w-[1.375rem] rounded-full p-1"
              @click="clear"
            />
          </span>
        </div>
      </template>
    </SearchableColumn>
  </th>
</template>

<script setup>
import { computed, inject, onMounted, ref, watch } from "vue";
import { useVModel } from "@vueuse/core";
import { InputField } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  search: [Object, String, Number],
  sortable: {
    type: Boolean,
    default: false,
  },
  pagination: Object,
  label: String,
  column: String,
});

const emit = defineEmits(["columnSearch", "sort", "update:pagination"]);

const _pagination = useVModel(props, "pagination", emit);

const loading = ref(false);
const barangays = ref([]);
const selected = ref(props.search ?? []);

const loadBarangays = () => {
  loading.value = true;

  $api
    .get("/address/barangays/141102000")
    .then((response) => {
      barangays.value = response.data.map((item) => ({
        name: item.name,
        code: item.code,
      }));
    })
    .finally(() => {
      loading.value = false;
    });
};

const isSelected = (barangay) => {
  return !!selected.value.find((item) => item.code == barangay.code);
};

const selectBarangay = (barangay) => {
  if (isSelected(barangay)) {
    selected.value = selected.value.filter(
      (item) => item.code != barangay.code
    );
  } else {
    selected.value.push(barangay);
  }
};

watch(
  () => props.search,
  (val) => {
    selected.value = val ?? [];
  }
);

onMounted(() => {
  loadBarangays();
});
</script>
