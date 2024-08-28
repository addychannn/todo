<template>
  <div class="relative">
    <div class="flex items-center gap-2">
      <TInput
        label="Year"
        type="number"
        v-model="input.year"
        innerClass="bg-light text-dark"
      />
      <TInput
        label="Month"
        type="number"
        v-model="input.month"
        innerClass="bg-light text-dark"
      />
      <TInput
        label="Day"
        type="number"
        v-model="input.day"
        innerClass="bg-light text-dark"
      />
      <TButton
        label="Get Logs"
        class="rounded-full bg-glossy px-3 py-1"
        @click="getLogs(input.year, input.month, input.day)"
      />
    </div>
    <div>
      <template v-for="log in logs" :key="log">
        <div>{{ log }}</div>
      </template>
      <template v-for="log in summary" :key="log">
        <div>{{ log }}</div>
      </template>
    </div>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </div>
</template>

<script setup>
import { inject, onMounted, ref } from "vue";

const $api = inject("$api");

const props = defineProps({});

const loading = ref(false);
const loadingMessage = ref("");
const logs = ref([]);
const summary = ref([]);
const input = ref({
  year: 2023,
  month: 6,
  day: 18,
});

const getLogs = (year, month = null, day = null) => {
  loading.value = true;
  loadingMessage.value = "Fetching logs, please wait...";

  $api
    .get(
      `/logsy/${year}${!!month ? "/" + month : ""}${
        !!month && !!day ? "/" + day : ""
      }`
    )
    .then((response) => {
      logs.value = response.data.data;
      summary.value = response.data.summary;
    })
    .finally(() => {
      loading.value = false;
    });
};

onMounted(() => {
  getLogs(2023, 4);
});
</script>
