<template>
  <div class="flex h-full items-center">
    <div
      class="relative grid w-full gap-x-2 rounded-lg border border-foreground/25 bg-page-background p-2 shadow-md shadow-foreground/10 sm:grid-cols-2"
    >
      <div v-if="!noType" class="sm:col-span-2">
        <div class="flex items-center gap-1 text-xs font-semibold">
          <span class="leading-tight">Addresss Type</span>
          <div
            v-if="editor.type.error"
            class="flex items-center gap-1 leading-none text-rose-500"
          >
            <div class="flex items-center justify-center">
              <TIcon name="error" size="xs" class="text-negative" />
            </div>
            {{ editor.type.errorMessage }}
          </div>
        </div>
        <div
          class="TScroll flex h-[2.125rem] items-center gap-1 overflow-x-auto overflow-y-hidden scrollbar-h-1"
        >
          <div v-if="types.loading" class="flex items-center gap-1">
            <TSkeleton class="h-[2.125rem] w-32" type="radio" />
            <TSkeleton class="h-[2.125rem] w-20" type="radio" />
          </div>
          <div v-else class="flex items-center gap-1">
            <TRadio
              v-if="nullableType"
              label="None"
              :value="null"
              v-model="editor.type.value"
            />
            <TRadio
              v-for="type in types.data"
              :key="type.value"
              :label="type.label"
              :value="type.value"
              v-model="editor.type.value"
            />
          </div>
        </div>
      </div>
      <TList
        :label="editor.region.name"
        :options="regions.data"
        v-model="editor.region.value"
        :loading="regions.loading"
        :error="editor.region.error"
        :errorMessage="editor.region.errorMessage"
        :itemHeight="34"
        innerClass="bg-light text-dark"
        @update:modelValue="(val) => loadProvinces(val)"
      />
      <TList
        :label="editor.province.name"
        :options="provinces.data"
        v-model="editor.province.value"
        :loading="provinces.loading"
        :error="editor.province.error"
        :errorMessage="editor.province.errorMessage"
        :readonly="provinces.data.length <= 0"
        innerClass="bg-light text-dark"
        @update:modelValue="(val) => loadCities(val)"
      />
      <TList
        :label="editor.city.name"
        :options="cities.data"
        v-model="editor.city.value"
        :loading="cities.loading"
        :error="editor.city.error"
        :errorMessage="editor.city.errorMessage"
        :readonly="cities.data.length <= 0"
        innerClass="bg-light text-dark"
        @update:modelValue="(val) => loadBarangays(val)"
      />
      <TList
        :label="editor.barangay.name"
        :options="barangays.data"
        v-model="editor.barangay.value"
        :loading="barangays.loading"
        :error="editor.barangay.error"
        :errorMessage="editor.barangay.errorMessage"
        :readonly="barangays.data.length <= 0"
        innerClass="bg-light text-dark"
      />
      <div class="grid gap-x-2 sm:col-span-2 sm:grid-cols-7">
        <TInput
          :label="editor.location.name"
          v-model="editor.location.value"
          :error="editor.location.error"
          :errorMessage="editor.location.errorMessage"
          :readonly="
            types.loading ||
            regions.loading ||
            provinces.loading ||
            cities.loading ||
            barangays.loading
          "
          innerClass="bg-light text-dark"
          class="sm:col-span-5"
          @keyup.enter="saveAddress"
        />
        <TInput
          :label="editor.zipCode.name"
          v-model="editor.zipCode.value"
          :error="editor.zipCode.error"
          :errorMessage="editor.zipCode.errorMessage"
          type="number"
          hideNumberButtons
          :maxlength="4"
          :readonly="
            types.loading ||
            regions.loading ||
            provinces.loading ||
            cities.loading ||
            barangays.loading
          "
          innerClass="bg-light text-dark"
          class="sm:col-span-2"
          @keyup.enter="saveAddress"
        />
      </div>
      <div class="flex items-center justify-end gap-2 sm:col-span-2">
        <TCheckBox
          v-model="editor.isMain"
          label="Is Main Address"
          iconSize="sm"
          class="flex-auto"
          :disabled="address?.isMain"
        />
        <TButton
          label="Save"
          class="rounded-md border-primary bg-primary bg-glossy px-3 py-1 text-light"
          :disabled="
            types.loading ||
            regions.loading ||
            provinces.loading ||
            cities.loading ||
            barangays.loading ||
            loading
          "
          @click="saveAddress"
        />
        <TButton
          label="Cancel"
          class="rounded-md px-3 py-1"
          :disabled="loading"
          @click="emit('cancel')"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, ref } from "vue";
import { InputField, notify, Helpers } from "@/scripts";
import { useVModel } from "@vueuse/core";

const $api = inject("$api");

const props = defineProps({
  address: Object,
  api: String,
  loading: {
    type: Boolean,
    default: false,
  },
  loadingMessage: {
    type: String,
    default: "",
  },
  noType: {
    type: Boolean,
    default: false,
  },
  nullableType: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "update:user",
  "update:loading",
  "update:loadingMessage",
  "cancel",
]);

const types = ref({
  loading: false,
  data: [],
});
const regions = ref({
  loading: false,
  data: [],
});
const provinces = ref({
  loading: false,
  data: [],
});
const cities = ref({
  loading: false,
  data: [],
});
const barangays = ref({
  loading: false,
  data: [],
});

const editor = ref({
  region: new InputField().setName("Region"),
  province: new InputField().setName("Province"),
  city: new InputField().setName("City"),
  barangay: new InputField().setName("Barangay").setRules("required"),
  location: new InputField(props.address?.location)
    .setName("Address")
    .setRules("required|maxLength:255"),
  type: new InputField(props.address?.type?.id)
    .setName("Address Type")
    .setRules(props.nullableType || props.noType ? "" : "required"),
  zipCode: new InputField(props.address?.zipCode)
    .setName("Zip Code")
    .setRules("required|minLength:4"),
  isMain: props.address?.isMain ?? false,
});

const _loading = useVModel(props, "loading", emit);
const _loadingMessage = useVModel(props, "loadingMessage", emit);
const isEdit = computed(() => !!props.address?.id);

const saveAddress = () => {
  if (validate()) {
    _loading.value = true;
    _loadingMessage.value = "Saving address, please wait...";

    let addressId = isEdit.value ? `/${props.address.id}` : "";
    let data = {
      type: editor.value.type.value,
      barangay: editor.value.barangay.value,
      location: editor.value.location.value,
      zipCode: editor.value.zipCode.value,
      isMain: editor.value.isMain,
    };

    $api
      .patch(`${props.api}${addressId}`, data)
      .then((response) => {
        emit("update:user", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .catch((error) => Helpers.onRequestError(error, editor.value))
      .finally(() => {
        _loading.value = false;
      });
  }
};

const validate = () => {
  editor.value.barangay.validate();
  editor.value.location.validate();
  editor.value.type.validate();
  editor.value.zipCode.validate();

  return !(
    editor.value.barangay.error ||
    editor.value.location.error ||
    editor.value.type.error ||
    editor.value.zipCode.error
  );
};

const loadTypes = () => {
  types.value.loading = true;
  $api
    .get(`/address/types`)
    .then((response) => {
      types.value.data = mapForOptions(response.data, "id", "name");
    })
    .finally(() => {
      types.value.loading = false;
    });
};

const loadRegions = () => {
  regions.value.loading = true;
  regions.value.data = [];
  provinces.value.data = [];
  cities.value.data = [];
  barangays.value.data = [];

  editor.value.region.reset();
  editor.value.province.reset();
  editor.value.city.reset();
  editor.value.barangay.reset();
  $api
    .get("/address/regions")
    .then((response) => {
      regions.value.data = mapForOptions(
        response.data.regions,
        "code",
        "name",
        "regionName"
      );
    })
    .finally(() => {
      regions.value.loading = false;
    });
};

const loadProvinces = (regionCode) => {
  provinces.value.loading = true;
  provinces.value.data = [];
  cities.value.data = [];
  barangays.value.data = [];

  editor.value.province.reset();
  editor.value.city.reset();
  editor.value.barangay.reset();
  $api
    .get(`/address/provinces/${regionCode}`)
    .then((response) => {
      provinces.value.data = mapForOptions(response.data);
    })
    .finally(() => {
      provinces.value.loading = false;
    });
};

const loadCities = (provinceCode) => {
  cities.value.loading = true;
  cities.value.data = [];
  barangays.value.data = [];

  editor.value.city.reset();
  editor.value.barangay.reset();
  $api
    .get(`/address/cities/${provinceCode}`)
    .then((response) => {
      cities.value.data = mapForOptions(response.data);
    })
    .finally(() => {
      cities.value.loading = false;
    });
};

const loadBarangays = (cityCode) => {
  barangays.value.loading = true;
  barangays.value.data = [];

  editor.value.barangay.reset();
  $api
    .get(`/address/barangays/${cityCode}`)
    .then((response) => {
      barangays.value.data = mapForOptions(response.data);
    })
    .finally(() => {
      barangays.value.loading = false;
    });
};

const loadBarangayData = (barangayCode) => {
  regions.value.data = [];
  provinces.value.data = [];
  cities.value.data = [];
  barangays.value.data = [];
  barangays.value.loading = true;
  cities.value.loading = true;
  provinces.value.loading = true;
  regions.value.loading = true;

  editor.value.region.reset();
  editor.value.province.reset();
  editor.value.city.reset();
  editor.value.barangay.reset();

  $api
    .get(`/address/initial/barangay/${barangayCode}`)
    .then((response) => {
      regions.value.data = mapForOptions(
        response.data.regions,
        "code",
        "name",
        "regionName"
      );
      provinces.value.data = mapForOptions(response.data.provinces);
      cities.value.data = mapForOptions(response.data.cities);
      barangays.value.data = mapForOptions(response.data.barangays);
      editor.value.region.setValue(response.data.barangay.regionCode);
      editor.value.province.setValue(response.data.barangay.provinceCode);
      editor.value.city.setValue(response.data.barangay.cityCode);
      editor.value.barangay.setValue(response.data.barangay.code);
    })
    .finally(() => {
      barangays.value.loading = false;
      cities.value.loading = false;
      provinces.value.loading = false;
      regions.value.loading = false;
    });
};

const loadCityData = (cityCode) => {
  regions.value.data = [];
  provinces.value.data = [];
  cities.value.data = [];
  barangays.value.data = [];
  barangays.value.loading = true;
  cities.value.loading = true;
  provinces.value.loading = true;
  regions.value.loading = true;

  editor.value.region.reset();
  editor.value.province.reset();
  editor.value.city.reset();
  editor.value.barangay.reset();

  $api
    .get(`/address/initial/city/${cityCode}`)
    .then((response) => {
      regions.value.data = mapForOptions(
        response.data.regions,
        "code",
        "name",
        "regionName"
      );
      provinces.value.data = mapForOptions(response.data.provinces);
      cities.value.data = mapForOptions(response.data.cities);
      barangays.value.data = mapForOptions(response.data.barangays);

      editor.value.region.setValue(response.data.city.regionCode);
      editor.value.province.setValue(response.data.city.provinceCode);
      editor.value.city.setValue(response.data.city.code);
    })
    .finally(() => {
      barangays.value.loading = false;
      cities.value.loading = false;
      provinces.value.loading = false;
      regions.value.loading = false;
    });
};

const mapForOptions = (
  data,
  valueKey = "code",
  labelKey = "name",
  desc = null
) => {
  return data.map((item) =>
    Object.assign(
      {},
      {
        label: item[labelKey],
        value: item[valueKey],
      },
      desc
        ? {
            description: item[desc],
          }
        : {}
    )
  );
};

onMounted(() => {
  if (!props.noType) {
    loadTypes();
  }
  if (!!props.address?.barangay.code) {
    loadBarangayData(props.address?.barangay.code);
  } else {
    loadCityData("141102000"); // Baguio City
  }
});
</script>
