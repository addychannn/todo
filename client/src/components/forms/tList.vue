<template>
  <Combobox
    :modelValue="parseValue(data)"
    @update:modelValue="emitValue"
    nullable
    :multiple="multiple"
    v-slot="{ open }"
  >
    <Float
      as="template"
      :portal="portal"
      flip
      :middleware="floatingMiddlewares"
      strategy="fixed"
      placement="bottom-start"
      :offset="4"
      leave="transition ease-in duration-100"
      leave-from="opacity-100"
      leave-to="opacity-0"
      floating-as="template"
      @hide="(query = ''), _onFilter('')"
    >
      <TFieldWrapper
        :tag="searchable ? 'label' : ComboboxButton"
        as="label"
        :label="label"
        :error="error"
        :errorMessage="errorMessage"
        :disabled="disabled"
        :readonly="(readonlyOnLoad && loading) || readonly"
        :hint="hint"
        :cursor="searchable ? cursor : 'cursor-pointer'"
        v-bind="$attrs"
        class="relative"
        :class="_wrapperClass"
        :onResize="onResize"
        :innerClass="innerClass"
        @click="(evt) => (searchable ? null : evt.preventDefault())"
      >
        <template v-for="(index, name) in slots" v-slot:[name]="data">
          <slot :name="name" v-bind="data"></slot>
        </template>
        <template #default="{ classNames, attr }">
          <template v-if="multiple">
            <div class="inline-flex flex-wrap items-center gap-1 pt-4">
              <template v-if="!noCounter && data.length > 0">
                <slot name="itemCount" :total="data.length">
                  <span class="text-xs leading-none text-gray-400">
                    {{ `${data.length} Selected` }}
                  </span>
                </slot>
              </template>
              <template v-if="!hideSelectedItem">
                <slot
                  name="selectedItem"
                  v-for="s in data"
                  :key="s"
                  :data="s"
                  :remove="(evt) => removeSelected(evt, s, open)"
                >
                  <div
                    class="pointer-events-none inline-flex min-h-[1.375rem] items-center gap-1 rounded-md border border-dark/25 bg-gray-200 px-0.5"
                  >
                    <span class="text-sm">
                      {{ s[labelKey] }}
                    </span>
                    <TButton
                      icon="close"
                      iconSize="xs"
                      tabindex="-1"
                      class="pointer-events-auto aspect-square rounded-full bg-dark/20 p-0.5"
                      @click="(evt) => removeSelected(evt, s, open)"
                    />
                  </div>
                </slot>
              </template>
            </div>
          </template>
          <ComboboxInput
            v-if="searchable"
            :display-value="(item) => (multiple ? '' : item?.[labelKey])"
            @change="
              (e) => {
                _onFilter(e.target.value);
              }
            "
            v-bind="attr"
            :placeholder="
              (Array.isArray(data) ? data.length > 0 : !!data) ? null : ' '
            "
            class="min-h-[1.375rem]"
            :class="[
              multiple && '!w-auto',
              ...classNames,
              clearable && (Array.isArray(data) ? data.length > 0 : !!data)
                ? ''
                : '',
            ]"
          />
          <ComboboxButton
            as="div"
            v-else
            class="min-h-[1.375rem] min-w-[3.75rem]"
            :class="[...classNames, clearable && !!data ? '' : '']"
          >
            {{ data?.[labelKey] }}
          </ComboboxButton>
        </template>
        <template #append>
          <template v-if="loading">
            <TSpinnerOrbit class="h-6 w-6" color="hsl(var(--color-dark))" />
          </template>
          <template v-else>
            <TButton
              v-if="clearable && (multiple ? data.length > 0 : !!data)"
              icon="close"
              iconSize="sm"
              class="rounded-full text-gray-400"
              @click="data = multiple ? [] : null"
            />
            <ComboboxButton
              class="flex items-center"
              :disabled="disabled || readonly || loading"
            >
              <TIcon class="text-gray-400" name="unfold_more" />
            </ComboboxButton>
          </template>
          <slot name="append"></slot>
        </template>
        <template #error>
          <slot name="error"></slot>
        </template>
      </TFieldWrapper>
      <ComboboxOptions
        class="relative max-w-[95dvw] overflow-y-auto overflow-x-hidden rounded-lg border border-gray-500/25 bg-light text-dark shadow-lg focus:outline-none sm:text-sm"
        :style="`min-width: ${comboSize.width}px`"
        as="div"
        unmount
      >
        <div
          v-if="
            (!filtered && !options) ||
            filtered.length <= 0 ||
            options.length <= 0
          "
          class="relative cursor-default select-none px-4 py-2 font-semibold text-gray-700"
        >
          {{ loading ? "Loading..." : "Nothing found." }}
        </div>
        <UseVirtualList
          v-else
          v-on-click-outside="onOutsideClick"
          ref="virtualList"
          class="TScroll w-full"
          @click="emit('clickedInside')"
          :list="filtered"
          :options="{ itemHeight: itemHeight }"
          :style="{ height: _height }"
        >
          <template #default="{ data }">
            <ComboboxOption
              v-slot="{ selected, active }"
              :value="data"
              as="template"
            >
              <slot
                name="option"
                :value="data"
                :selected="selected"
                :active="active"
              >
                <div
                  class="relative flex cursor-pointer select-none items-center gap-1 rounded-md px-2 leading-none text-dark"
                  :class="[
                    selected && 'bg-slate-700 text-light',
                    active && 'bg-slate-600 text-light',
                  ]"
                  :style="{ height: `${itemHeight}px` }"
                  role="option"
                  tabindex="-1"
                  :aria-selected="selected"
                >
                  <div
                    class="block flex-auto truncate leading-none"
                    :class="selected ? 'font-medium' : 'font-normal'"
                  >
                    <div class="font-semibold leading-tight">
                      {{ data[labelKey] }}
                    </div>
                    <div
                      v-if="!!data[subKey]"
                      class="text-xs italic leading-none"
                      :class="[
                        active || selected ? 'text-white' : 'text-gray-500/75',
                      ]"
                    >
                      {{ data[subKey] }}
                    </div>
                  </div>
                  <span
                    v-if="selected"
                    class="flex items-center"
                    :class="
                      active || selected ? 'text-white' : 'text-secondary'
                    "
                  >
                    <TIcon name="check" size="sm" />
                  </span>
                </div>
              </slot>
            </ComboboxOption>
          </template>
        </UseVirtualList>
        <slot name="appendOptions" />
      </ComboboxOptions>
    </Float>
  </Combobox>
</template>

<script setup>
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxOption,
  ComboboxOptions,
} from "@headlessui/vue";
import { Float } from "@headlessui-float/vue";
import { computed, onMounted, reactive, ref, useSlots, watch } from "vue";
import { vOnClickOutside, UseVirtualList } from "@vueuse/components";
import { limitShift, shift } from "@floating-ui/dom";
import { useWindowSize, useVModel } from "@vueuse/core";

const { width, height } = useWindowSize();

const slots = useSlots();

const props = defineProps({
  modelValue: [Object, String, Number, Boolean],

  /**
   * options
   * {
   *  label: String,
   *  value: Any,
   *  description: String
   * }
   */
  options: Array,
  name: String,
  label: {
    type: String,
    default: null,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  hint: {
    type: String,
    default: null,
  },
  error: {
    type: Boolean,
    default: null,
  },
  errorMessage: {
    type: String,
    default: null,
  },
  cursor: {
    type: String,
    default: "cursor-text",
    validator: (val) => /^cursor-/.test(val),
  },
  clearable: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  readonlyOnLoad: {
    type: Boolean,
    default: true,
  },
  searchable: {
    type: Boolean,
    default: true,
  },
  wrapperClass: {
    type: [String, Array],
    default: () => [],
  },
  innerClass: {
    type: [String, Array],
    default: () => [],
  },
  portal: {
    type: Boolean,
    default: true,
  },
  itemHeight: {
    type: Number,
    default: 30,
  },
  maxItemShown: {
    type: Number,
    default: 10,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  labelKey: {
    type: String,
    default: "label",
  },
  subKey: {
    type: String,
    default: "description",
  },
  valueKey: {
    type: String,
    default: "value",
  },
  format: {
    type: Function,
    default: null,
  },
  hideSelectedItem: {
    type: Boolean,
    default: false,
  },
  noCounter: {
    type: Boolean,
    default: false,
  },
  onFilter: {
    type: Function,
    default: null,
  },
  emitFormat: {
    type: Function,
    default: null,
  },
});

const emit = defineEmits([
  "update:modelValue",
  "clickedOutside",
  "clickedInside",
]);

const query = ref("");
const virtualList = ref(null);
const floatingMiddlewares = ref([
  shift({
    crossAxis: true,
    limiter: limitShift({
      crossAxis: false,
    }),
  }),
]);

const comboSize = ref({
  width: 0,
  height: 0,
});

const filtered = ref([]);

const data = useVModel(props, "modelValue", emit);

const _wrapperClass = computed(() =>
  Array.isArray(props.wrapperClass) ? props.wrapperClass : [props.wrapperClass]
);

const _height = computed(() => {
  let maxItems = props.maxItemShown;
  let totalItems = filtered.value.length;
  let itemHeight = props.itemHeight;

  let itemsToShow = totalItems > maxItems ? maxItems : totalItems || 1;

  let optionsHeight = itemHeight * itemsToShow;

  if (optionsHeight >= height.value) {
    let minimized = Math.floor(height.value / itemHeight);
    optionsHeight = itemHeight * minimized;
  }
  return `${1 * optionsHeight}px`;
});

const multiTag = computed(() =>
  props.searchable ? "template" : ComboboxButton
);

const onResize = (size) => {
  comboSize.value.width = size.offsetWidth;
  comboSize.value.height = size.offsetHeight;
};

const onOutsideClick = (ev) => {
  emit("clickedOutside", ev);
};

const removeSelected = (e, val, open) => {
  if (!open) {
    e.preventDefault();
  }
  let index = data.value.indexOf(val);
  if (index > -1) {
    data.value.splice(index, 1);
  }
};

const _format = (val) => {
  return !!props.format ? props.format(val) : val;
};

const _onFilter = (_query = "") => {
  filtered.value = !!props.onFilter
    ? props.onFilter(_query, props.options)
    : defaultFilter(_query, props.options);
};

const _emitFormat = (val) => {
  if (!!props.emitFormat) {
    let result = null;
    if (props.multiple) {
      result = val?.map(props.emitFormat) ?? [];
    } else {
      result = props.emitFormat(val);
    }
    return result;
  }
  return props.multiple ? val : val?.[props.valueKey] ?? null;
};

const defaultFilter = (_query, _items) => {
  return _query == ""
    ? _items.map((item) => _format(item))
    : _items
        .filter((item) =>
          item[props.labelKey]
            .toLowerCase()
            .replace(/\s+/g, "")
            .includes(_query.toLowerCase().replace(/\s+/g, ""))
        )
        .map((item) => _format(item));
};

const parseValue = (selected) => {
  if (!!props.format || props.multiple) {
    return selected;
  }
  return props.options.find((item) => item[props.valueKey] == selected) ?? null;
};

const emitValue = (selected) => {
  emit("update:modelValue", _emitFormat(selected));
};

watch(filtered, () => {
  virtualList.value?.scrollTo(0);
});

watch(
  () => props.modelValue,
  (val) => {
    query.value = null;
  }
);

watch(
  () => props.options,
  (items) => {
    filtered.value = _format(items);
  }
);

onMounted(() => {
  _onFilter();
});
</script>
