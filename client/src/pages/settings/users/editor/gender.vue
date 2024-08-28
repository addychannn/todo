<template>
  <TCard
    class="relative h-[95dvh] max-h-64 w-[95dvw] max-w-xs bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Gender </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <div class="bg-page-background px-2 py-1">
      <TInput label="Search" v-model="search" innerClass="bg-light text-dark" />
    </div>
    <TCardBody>
      <div class="grid gap-0.5">
        <TButton
          v-for="gender in filteredGenders"
          :key="gender.value"
          class="rounded-md px-1"
          :class="isSelected(gender) ? 'bg-primary text-light' : ''"
          @click="editor.gender.value = gender.value"
        >
          <div
            class="pointer-events-none relative flex items-center gap-1 pl-6"
          >
            <TIcon
              v-if="isSelected(gender)"
              name="done"
              size="sm"
              class="absolute left-1"
            />
            {{ gender.label }}
          </div>
        </TButton>
      </div>
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1 bg-page-background">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="saveGender"
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
import { transitions, notify, InputField, Helpers } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  user: Object,
  api: String,
});

const emit = defineEmits(["update:user", "close"]);

const search = ref("");
const loading = ref(false);
const loadingMessage = ref("");
const genders = ref([]);

const editor = reactive({
  gender: new InputField(props.user.profile?.gender?.id)
    .setName("Gender")
    .setRules("required"),
});

const filteredGenders = computed(() =>
  search.value === ""
    ? genders.value
    : genders.value.filter((item) =>
        item.label
          .toLowerCase()
          .replace(/\s+/g, "")
          .includes(search.value.toLowerCase().replace(/\s+/g, ""))
      )
);

const loadGenders = () => {
  loading.value = true;
  loadingMessage.value = "Loading Genders, please wait...";

  $api
    .get(`/genders`)
    .then((response) => {
      genders.value = response.data.data.map((item) => ({
        value: item.id,
        label: item.name,
      }));
    })
    .catch((error) => Helpers.onRequestError(error, editor))
    .finally(() => {
      loading.value = false;
    });
};

const saveGender = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating gender, please wait...";

    $api
      .patch(props.api, {
        gender: editor.gender.value,
      })
      .then((response) => {
        emit("update:user", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .finally(() => {
        loading.value = false;
      });
  }
};

const validate = () => {
  editor.gender.validate();
  return !editor.gender.error;
};

const isSelected = (gender) => {
  return gender.value == editor.gender.value;
};

onMounted(() => {
  loadGenders();
});
</script>
