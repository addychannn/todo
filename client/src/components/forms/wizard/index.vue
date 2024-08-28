<template>
  <div
    class="relative flex flex-col"
    :class="[transitioning && 'overflow-hidden']"
  >
    <div class="flex items-center px-4">
      <TTabProgress
        :modelValue="_tab"
        @update:modelValue="jumpPage"
        :pages="tabs"
        :progressClass="progressClass"
        v-bind="_stepClasses"
        class="mb-5 flex-auto"
      />
    </div>
    <div class="flex flex-auto flex-col">
      <transition
        enter-from-class="opacity-0 blur-md"
        leave-to-class="opacity-0 blur-md"
        :enter-active-class="'transition duration-300 delay-300 inset-x-0 absolute'"
        :leave-active-class="'transition duration-300 inset-x-0 absolute'"
        @before-leave="transitioning = true"
        @after-enter="transitioning = false"
      >
        <component
          :is="tabs[tab].component"
          :key="`wiz_tab_${tabs[tab].name}`"
          v-model:inputFields="editor"
          v-bind="tabs[_tab].bindings ?? {}"
          @next="nextPage"
          @prev="prevPage"
          @fields="changeFields"
        />
      </transition>
    </div>
  </div>
</template>

<script setup>
import { useVModel } from "@vueuse/core";
import { computed, ref } from "vue";
const props = defineProps({
  /**
   * [
   *  {
   *   label: ...,
   *   name: ...,
   *   icon: ...,
   *   component: ...,
   *   bindings: ...,
   *  }
   * ]
   */
  tabs: Object,
  tab: Number,
  progressClass: {
    type: [String, Object],
    default: "before:!bg-primary !bg-gray-300/75 !min-h-[0.35rem]",
  },
  stepClasses: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits([
  "update:modelValue",
  "update:tab",
  "submit",
  "error",
]);

const transitioning = ref(false);
const fields = ref([]);
const groups = ref([]);
const editor = ref({});

const _tab = useVModel(props, "tab", emit);

const _stepClasses = computed(() =>
  Object.assign(
    {},
    {
      bgClass: "!bg-background-accent",
      activeClass: "bg-primary",
      activeIconClass: "text-light",
      activeLabelClass: "text-primary",
      idleClass: "bg-gray-400/75",
      idleIconClass: "text-dark",
      idleLabelClass: "text-foreground",
    },
    props.stepClasses
  )
);

const hasError = computed(() =>
  Object.values(editor.value).some((item) => item.error)
);

const nextPage = () => {
  changePage(_tab.value + 1);
};

const prevPage = () => {
  changePage(_tab.value - 1);
};

const jumpPage = async (index) => {
  if (_tab.value > index) {
    changePage(index);
  } else {
    for (let i = _tab.value; i < index; i++) {
      if (validate(fields.value) && !hasError.value) {
        changePage(_tab.value + 1);
        await new Promise((resolve) => setTimeout(resolve, 300));
      }
    }
  }
};

const changePage = (page) => {
  if (page == props.tabs.length) {
    emit("submit");
  } else if (page >= props.tabs.length) {
    _tab.value = props.tabs.length - 1;
  } else if (_tab.value < page && validate(fields.value)) {
    _tab.value = page;
  } else if (_tab.value > page) {
    _tab.value = page;
  }
};

const changeFields = (val) => {
  fields.value = val;
  Object.assign(groups.value, {
    [props.tabs[_tab.value].name]: val,
  });
};

const validate = (f = null) => {
  let tmp = Object.keys(editor.value);
  tmp.forEach((k) => {
    editor.value[k].resetError();
  });

  if (f == null) {
    tmp.forEach((k) => {
      editor.value[k].validate();
    });
  } else {
    f.forEach((k) => {
      editor.value[k].validate();
    });
  }
  let _hasError = !Object.values(editor.value).some((item) => item.error);
  if (!_hasError) {
    let errorGroup = {};
    tmp
      .filter((k) => editor.value[k].error)
      .forEach((t) => {
        Object.assign(errorGroup, { [t]: ["error"] });
      });
    onError(errorGroup, false);
  }
  return _hasError;
};

const onError = (errors, pageChange = false) => {
  let hasErrors = Object.keys(errors);
  let _index = -1;
  let keys = props.tabs.map((t) => t.name);
  let errorGroups = [];
  keys.forEach((mod) => {
    if (!!groups.value[mod]) {
      let included = groups.value[mod].some((r) => hasErrors.includes(r));
      if (_index < 0 && included) {
        _index = keys.indexOf(mod);
        errorGroups.push(mod);
      }
    }
  });
  emit("error", errorGroups);
  if (_index > -1 && pageChange) {
    changePage(_index);
  }
};

const reset = () => {
  let tmp = Object.values(editor.value);
  tmp.forEach((item) => {
    item.reset();
  });
  changePage(0);
};

defineExpose({
  nextPage,
  prevPage,
  jumpPage,
  changePage,
  validate,
  reset,
  onError: (e) => onError(e, true),
  editor,
});
</script>
