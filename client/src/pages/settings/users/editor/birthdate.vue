<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Birthdate </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody>
      <TSimpleDate
        v-model="editor.birthdate.value"
        :error="editor.birthdate.error"
        :errorMessage="editor.birthdate.errorMessage"
        @updating="onUpdating"
      />
      <div class="group">
        <span class="font-semibold">Age: </span>
        <span>
          {{ age?.years ?? 0 }}
        </span>
        <span class="opacity-0 transition md:group-hover:opacity-100">
          <span class="px-1">Years</span>
          <span>{{ age?.months ?? 0 }}</span>
          <span class="px-1">Months</span>
          <span>{{ age?.days ?? 0 }}</span>
          <span class="px-1">Days</span>
        </span>
      </div>
    </TCardBody>

    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="saveBirthdate"
      />
      <TButton
        label="Cancel"
        class="rounded-full px-3 py-1"
        @click="emit('close')"
      />
    </TCardFooter>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { computed, inject, onMounted, reactive, ref } from "vue";
import { InputField, notify, transitions, Helpers } from "@/scripts";
import dayjs from "dayjs";
import objectSupport from "dayjs/plugin/objectSupport";

dayjs.extend(objectSupport);

const $api = inject("$api");

const props = defineProps({
  user: Object,
  api: String,
});

const emit = defineEmits(["update:user", "close"]);

const tmpDate = ref();
const loading = ref(false);
const loadingMessage = ref("");

const editor = reactive({
  birthdate: new InputField(props.user.profile?.birthdate)
    .setName("Birthdate")
    .setRules(),
});

const age = computed(() => Helpers.computeAge(editor.birthdate.value));

const saveBirthdate = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating birthdate, please wait...";

    $api
      .patch(props.api, {
        birthdate: editor.birthdate.value,
      })
      .then((response) => {
        emit("update:user", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .catch((error) => Helpers.onRequestError(error, editor))
      .finally(() => {
        loading.value = false;
      });
  }
};

const isAllFilled = () => {
  return (
    tmpDate.value?.month != null &&
    !!tmpDate.value?.day &&
    !!tmpDate.value?.year
  );
};

const validate = () => {
  editor.birthdate.resetError();
  if (!isAllFilled()) {
    editor.birthdate.setError("Please fill in a complete birthday");
    return false;
  }

  let date = dayjs(editor.birthdate.value);
  if (date.isAfter(dayjs())) {
    //editor.birthdate.setError("Please enter a valid birthday");
    editor.birthdate.setError("We're sorry, time travelers are not permitted!");
    return false;
  }
  return true;
};

const onUpdating = (data) => {
  tmpDate.value = data;
};
</script>
