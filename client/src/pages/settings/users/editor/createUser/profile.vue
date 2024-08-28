<template>
  <TFormWizardTab v-model:fields="fields">
    <template #default="{ editor, next }">
      <TInput
        v-if="!!editor.first_name"
        :label="editor.first_name.name"
        v-model="editor.first_name.value"
        :error="editor.first_name.error"
        :errorMessage="editor.first_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      />
      <TInput
        v-if="!!editor.middle_name"
        :label="editor.middle_name.name"
        v-model="editor.middle_name.value"
        :error="editor.middle_name.error"
        :errorMessage="editor.middle_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      />
      <TInput
        v-if="!!editor.last_name"
        :label="editor.last_name.name"
        v-model="editor.last_name.value"
        :error="editor.last_name.error"
        :errorMessage="editor.last_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      />
      <div class="grid md:grid-cols-2 md:gap-5">
        <TDate
          v-if="!!editor.birthdate"
          :label="editor.birthdate.name"
          v-model="editor.birthdate.value"
          prevent-min-max-navigation
          :max-date="new Date()"
          :error="editor.birthdate.error"
          :errorMessage="editor.birthdate.errorMessage"
          :transformFn="(e) => Helpers.formatDate(e, null, 'YYYY-MM-DD')"
          innerClass="bg-light text-dark"
        />
        <TList
          v-if="!!editor.gender"
          :options="genders.data"
          :label="editor.gender.name"
          v-model="editor.gender.value"
          :error="editor.gender.error"
          :errorMessage="editor.gender.errorMessage"
          :emitFormat="(e) => e.value"
          innerClass="bg-light text-dark"
        />
      </div>
    </template>
  </TFormWizardTab>
</template>

<script setup>
import { inject, onMounted, ref } from "vue";
import { InputField, Helpers } from "@/scripts";

const $api = inject("$api");

const genders = ref({
  loading: false,
  data: [],
});
const fields = ref({
  first_name: new InputField().setName("First Name").setRules("required"),
  middle_name: new InputField().setName("Middle Name"),
  last_name: new InputField().setName("Last Name").setRules("required"),
  birthdate: new InputField().setName("Birthdate").setRules("required"),
  gender: new InputField().setName("Gender").setRules("required"),
});

const loadGenders = () => {
  genders.value.loading = true;
  $api
    .get("/genders")
    .then((response) => {
      genders.value.data = response.data.data.map((item) => ({
        value: item.id,
        label: item.name,
      }));
    })
    .finally(() => {
      genders.value.loading = false;
    });
};

onMounted(() => {
  loadGenders();
});
</script>
