<template>
  <TCard
    class="relative min-h-[26rem] w-[95dvw] max-w-sm rounded-2xl bg-slate-100 bg-opacity-20 text-slate-900 shadow-md backdrop-blur-sm dark:bg-opacity-75"
  >
    <div class="flex w-full flex-col items-center px-2 py-2 md:flex-row">
      <div class="flex-auto text-center text-lg font-semibold">
        Profile update is required before continuing.
      </div>
    </div>
    <TCardBody>
      <TInput
        :label="editor.first_name.name"
        v-model="editor.first_name.value"
        :error="editor.first_name.error"
        :errorMessage="editor.first_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveProfile"
      />
      <TInput
        :label="editor.middle_name.name"
        v-model="editor.middle_name.value"
        :error="editor.middle_name.error"
        :errorMessage="editor.middle_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveProfile"
      />
      <TInput
        :label="editor.last_name.name"
        v-model="editor.last_name.value"
        :error="editor.last_name.error"
        :errorMessage="editor.last_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveProfile"
      />
      <TList
        :options="genders.data"
        :loading="genders.loading"
        :label="editor.gender.name"
        v-model="editor.gender.value"
        :error="editor.gender.error"
        :errorMessage="editor.gender.errorMessage"
        :format="(val) => val"
        :emitFormat="(val) => val"
        valueKey="id"
        labelKey="name"
        innerClass="bg-light text-dark"
      />
      <div class="grid">
        <div
          class="font-semibold"
          :class="{ 'text-negative': editor.birthdate.error }"
        >
          Birth Date
        </div>
        <TSimpleDate
          :label="editor.birthdate.name"
          v-model="editor.birthdate.value"
          :error="editor.birthdate.error"
          :errorMessage="editor.birthdate.errorMessage"
          format="YYYY-MM-DD"
          innerClass="bg-light text-dark"
          @keyup.enter="saveProfile"
        />
      </div>
      <div
        class="flex items-stretch first:[&>button]:rounded-l-full last:[&>button]:rounded-r-full"
      >
        <TButton
          label="Save"
          class="flex-auto bg-primary bg-glossy px-3 py-1 text-light"
          @click="saveProfile"
        />
        <TButton
          icon="logout"
          iconSize="sm"
          label="Sign out"
          class="bg-negative px-3 py-1 text-light"
          @click="user.logout()"
        />
      </div>
    </TCardBody>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { computed, defineAsyncComponent, inject, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { Helpers, InputField, notify } from "@/scripts";
import { useAuthStore } from "@/stores";

const InfoItem = defineAsyncComponent(() =>
  import("@/pages/settings/users/editor/infoItem.vue")
);

const props = defineProps({});

const $api = inject("$api");
const router = useRouter();
const route = useRoute();
const user = useAuthStore();
const loading = ref(false);
const loadingMessage = ref("");
const modal = ref({
  show: false,
  type: "",
});
const genders = ref({
  loading: false,
  data: [],
});
const editor = ref({
  first_name: new InputField(user.profile?.first_name)
    .setName("First Name")
    .setRules("required"),
  middle_name: new InputField(user.profile?.middle_name)
    .setName("Middle Name")
    .setRules(""),
  last_name: new InputField(user.profile?.last_name)
    .setName("Last Name")
    .setRules("required"),
  birthdate: new InputField(user.profile?.birthdate)
    .setName("Birth date")
    .setRules("required"),
  gender: new InputField(user.profile?.gender)
    .setName("Gender")
    .setRules("required"),
});

const age = computed(() => Helpers.computeAge(user.profile?.birthdate));

const openModal = (type) => {};

const saveProfile = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Saving profile, please wait...";
    let data = Object.keys(editor.value).map((item) => ({
      [item]: editor.value[item].value,
    }));
    let params = {};

    Object.keys(editor.value).forEach((item) => {
      let tmp = { [item]: editor.value[item].value };
      if (item == "gender") tmp = { gender: editor.value[item].value.id };
      Object.assign(params, tmp);
    });

    $api
      .post("/user/profile", params)
      .then((response) => {
        user.profile = response.data.data.profile;
        notify({
          type: "positive",
          title: "Success!",
          text: response.data.message,
        });
        if (user.hasProfileName) {
          router.push(route.query.redirect || { name: "HomePage" });
        }
      })
      .catch((error) => {
        Helpers.onRequestError(error, editor.value);
      })
      .finally(() => {
        loading.value = false;
      });
  }
};

const validate = () => {
  let tmp = Object.values(editor.value);
  tmp.forEach((item) => {
    item.validate();
  });
  return !tmp.some((item) => item.error);
};

const getGenders = () => {
  genders.value.loading = true;
  $api
    .get("/genders")
    .then((response) => {
      genders.value.data = response.data.data;
    })
    .finally(() => {
      genders.value.loading = false;
    });
};

onMounted(() => {
  if (user.hasProfileName) {
    router.push(route.query.redirect || { name: "HomePage" });
  } else {
    getGenders();
  }
});
</script>
