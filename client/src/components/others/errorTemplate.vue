<template>
  <div class="flex items-center justify-center">
    <div class="flex max-w-xs flex-col text-center">
      <div v-if="!!_code && !hiddenCode" class="text-9xl font-semibold">
        {{ _code }}!
      </div>
      <div v-if="!!_error" class="text-2xl font-semibold">{{ _error }}</div>
      <div
        v-if="!!_message"
        class="flex justify-center text-center text-sm italic"
      >
        {{ _message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ErrorDescriptions, ErrorHandling, ErrorMessages } from "@/scripts";
import { computed } from "vue";

const props = defineProps({
  response: { type: Object, default: null },
  code: { type: Number, default: null },
  error: { type: String, default: null },
  description: { type: String, default: null },
});

const ResponseError = computed(
  () => props.response?.response ?? props.response ?? null
);

const hiddenCode = computed(() =>
  isNaN(ResponseError.value?.status ?? props.code)
);

let ErrorHandler = computed(() =>
  ResponseError.value
    ? new ErrorHandling(ResponseError.value)
    : {
        code: props.code,
        message: ErrorMessages[props.code],
        error: ErrorDescriptions[props.code],
      }
);

let _code = computed(() => props.code ?? ErrorHandler.value.code);
let _error = computed(() => props.error ?? ErrorHandler.value.error);
let _message = computed(() => props.description ?? ErrorHandler.value.message);
</script>
