<template>
  <TGroup label="Address" class="grid grid-cols-2 gap-x-2 bg-white px-2 pt-3">
    <TList
      v-model="address.region.value"
      @update:modelValue="loadProvinces"
      :options="regions.data"
      :label="address.region.name"
      :error="address.region.error"
      :errorMessage="address.region.errorMessage"
      :readonly="regions.loading"
      class="cols-span-1"
    >
      <template v-if="regions.loading" #append>
        <TSpinnerCircles class="h-6 w-6" />
      </template>
    </TList>

    <TList
      v-model="address.province.value"
      @update:modelValue="loadCities"
      :options="provinces.data"
      :label="address.province.name"
      :error="address.province.error"
      :errorMessage="address.province.errorMessage"
      class="cols-span-1"
      :readonly="provinces.loading"
      :disabled="!address.region.value"
    >
      <template v-if="provinces.loading" #append>
        <TSpinnerCircles class="h-6 w-6" />
      </template>
    </TList>

    <TList
      v-model="address.city.value"
      @update:modelValue="loadBarangays"
      :options="cities.data"
      :label="address.city.name"
      :error="address.city.error"
      :errorMessage="address.city.errorMessage"
      class="cols-span-1"
      :readonly="cities.loading"
      :disabled="!address.province.value"
    >
      <template v-if="cities.loading" #append>
        <TSpinnerCircles class="h-6 w-6" />
      </template>
    </TList>

    <TList
      v-model="barangay"
      :options="barangays.data"
      :label="address.barangay.name"
      :error="!!barangayError"
      :errorMessage="barangayError"
      class="cols-span-1"
      :readonly="barangays.loading"
      :disabled="!address.city.value"
    >
      <template v-if="barangays.loading" #append>
        <TSpinnerOrbit class="h-6 w-6" />
      </template>
    </TList>

    <TInput
      v-model="location"
      dense
      class="col-span-2"
      label="House No./Street/etc..."
      :error="!!locationError"
      :errorMessage="locationError"
      @keyup.enter="emit('submit')"
    >
    </TInput>

    <TInnerLoading
      v-if="!hideLoader"
      :active="_loading"
      :text="_loadingMessage"
    />
  </TGroup>
</template>

<script setup>
import { computed, inject, onMounted, reactive, ref, watch } from "vue";
import { InputField } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  barangayCode: String,
  location: String,
  cityCode: {
    type: String,
    default: "141102000",
  },
  locationError: {
    type: String,
    default: null,
  },
  barangayError: {
    type: String,
    default: null,
  },
  hideLoader: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: null,
  },
  loadingMessage: {
    type: String,
    default: null,
  },
});

const emit = defineEmits([
  "update:barangayCode",
  "update:location",
  "update:loading",
  "update:loadingMessage",
  "submit",
]);

const isLoading = ref(false);
const isLoadingMessage = ref("");

const _loading = computed({
  get: () => props.loading ?? isLoading.value,
  set: (val) => {
    isLoading.value = val;
    emit("update:loading", val);
  },
});

const _loadingMessage = computed({
  get: () => props.loadingMessage ?? isLoadingMessage.value,
  set: (val) => {
    isLoadingMessage.value = val;
    emit("update:loadingMessage", val);
  },
});

const barangay = computed({
  get: () => props.barangayCode,
  set: (val) => emit("update:barangayCode", val),
});

const location = computed({
  get: () => props.location,
  set: (val) => emit("update:location", val),
});

const regions = reactive({
  data: [],
  loading: false,
});
const provinces = reactive({
  data: [],
  loading: false,
});
const cities = reactive({
  data: [],
  loading: false,
});
const barangays = reactive({
  data: [],
  loading: false,
});

const address = reactive({
  region: new InputField(null).setName("Region"),
  province: new InputField(null).setName("Province"),
  city: new InputField(null).setName("City"),
  barangay: new InputField(props.barangayCode ?? null).setName("Barangay"),
  location: new InputField(props.location ?? null).setName("Location"),
});

const loadRegions = () => {
  regions.loading = true;
  provinces.data = [];
  cities.data = [];
  barangays.data = [];

  address.region.reset(null);
  address.province.reset(null);
  address.city.reset(null);
  address.barangay.reset(null);

  $api
    .get("/address/regions")
    .then((response) => {
      regions.data = mapRegions(response.data);
    })
    .finally(() => {
      regions.loading = false;
    });
};

const loadProvinces = (regionCode) => {
  provinces.loading = true;
  cities.data = [];
  barangays.data = [];

  address.province.reset(null);
  address.city.reset(null);
  address.barangay.reset(null);
  $api
    .get(`/address/provinces/${regionCode}`)
    .then((response) => {
      provinces.data = mapProvinces(response.data);
    })
    .finally(() => {
      provinces.loading = false;
    });
};

const loadCities = (provinceCode) => {
  cities.loading = true;
  barangays.data = [];

  address.city.reset(null);
  address.barangay.reset(null);
  $api
    .get(`/address/cities/${provinceCode}`)
    .then((response) => {
      cities.data = mapCities(response.data);
    })
    .finally(() => {
      cities.loading = false;
    });
};

const loadBarangays = (cityCode) => {
  barangays.loading = true;

  address.barangay.reset(null);
  $api
    .get(`/address/barangays/${cityCode}`)
    .then((response) => {
      barangays.data = mapBarangays(response.data);
    })
    .finally(() => {
      barangays.loading = false;
    });
};

const loadCity = (cityCode) => {
  _loading.value = true;
  _loadingMessage.value = "Preparing adress module, please wait...";
  $api
    .get(`/address/initial/city/${cityCode}`)
    .then((response) => {
      regions.data = mapRegions(response.data.regions);
      provinces.data = mapProvinces(response.data.provinces);
      cities.data = mapCities(response.data.cities);
      barangays.data = mapBarangays(response.data.barangays);

      let _city = response.data.cities.find((item) => item.code == cityCode);
      address.region.value = _city.regionCode;
      address.province.value = _city.provinceCode;
      address.city.value = cityCode;
    })
    .finally(() => {
      _loading.value = false;
    });
};

const mapRegions = (regions) => {
  return regions.map((item) => ({
    label: item.name,
    value: item.code,
    description: item.regionName,
  }));
};

const mapProvinces = (provinces) => {
  return provinces.map((item) => ({
    label: item.name,
    value: item.code,
  }));
};

const mapCities = (cities) => {
  return cities.map((item) => ({
    label: item.name,
    value: item.code,
  }));
};

const mapBarangays = (barangays) => {
  return barangays.map((item) => ({
    label: item.name,
    value: item.code,
  }));
};

onMounted(() => {
  if (!!props.cityCode) {
    loadCity(props.cityCode);
  } else {
    loadRegions();
  }
});
</script>
