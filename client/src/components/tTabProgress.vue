<template>
  <div v-if="pages.length > 1" class="relative px-3 py-5">
    <TProgress
      :value="progress"
      :class="[
        vertical && '!min-w-[0.5rem]',
        !vertical && '!min-h-[0.5rem]',
        ...progClass,
      ]"
      :vertical="vertical"
    />
    <div
      v-for="(p, index) in progPos"
      :key="`progress_indicator_${p.label.toLowerCase()}`"
      class="group absolute flex aspect-square w-10 -translate-x-1/2 -translate-y-1/2 cursor-pointer select-none items-center justify-center rounded-full delay-150"
      :class="[
        vertical && 'left-1/2',
        !vertical && 'top-1/2',
        p.bgClass ?? bgClass,
      ]"
      :style="stepLoc(p.pos)"
      @click="emit('update:modelValue', index)"
    >
      <div
        class="absolute inset-0 rounded-full transition-colors"
        :class="{
          [p.activeClass ?? activeClass]: progress >= p.pos,
          '!bg-opacity-50': progress <= p.pos,
        }"
      />
      <div
        class="absolute left-1/2 top-1/2 h-[80%] w-[80%] -translate-x-1/2 -translate-y-1/2 rounded-full delay-150"
        :class="[
          progress >= p.pos && (p.activeClass ?? activeClass),
          progress < p.pos && (p.idleClass ?? idleClass),
        ]"
      />
      <div class="relative flex items-center justify-center">
        <TIcon
          :name="p.icon ?? 'help_outline'"
          class="transition-all delay-150"
          :class="[
            !p.icon && 'opacity-0',
            progress < p.pos
              ? p.idleIconClass ?? idleIconClass
              : p.activeIconClass ?? activeIconClass,
          ]"
        />
        <span
          class="absolute whitespace-nowrap text-xs font-bold transition-colors delay-150"
          :class="[
            progress >= p.pos
              ? p.activeLabelClass ?? activeLabelClass
              : p.idleLabelClass ?? idleLabelClass,
            labelPos,
          ]"
        >
          {{ p.label }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
const props = defineProps({
  modelValue: Number,
  pages: {
    type: Object,
    default: () => [],
  },
  delay: {
    type: Number,
    default: 300,
  },
  spacing: {
    type: String,
    default: "between",
    validator: (val) => ["between", "evenly"].indexOf(val) > -1,
  },
  vertical: {
    type: Boolean,
    default: false,
  },
  labelPosition: {
    type: String,
    default: "bottom",
    validator: (val) => ["left", "top", "right", "bottom"].indexOf(val) > -1,
  },
  progressClass: {
    type: [String, Object],
    default: "",
  },
  bgClass: {
    type: [String, Object],
    default: "!bg-background-accent",
  },

  activeClass: {
    type: [String, Object],
    default: "bg-primary",
  },
  activeIconClass: {
    type: [String, Object],
    default: "text-light",
  },
  activeLabelClass: {
    type: [String, Object],
    default: "text-primary",
  },

  idleClass: {
    type: [String, Object],
    default: "bg-gray-400/75",
  },
  idleIconClass: {
    type: [String, Object],
    default: "text-dark",
  },
  idleLabelClass: {
    type: [String, Object],
    default: "text-foreground",
  },
});

const emit = defineEmits(["update:modelValue"]);

const progClass = computed(() =>
  Array.isArray(props.progressClass)
    ? props.progressClass
    : [props.progressClass]
);

const labelPos = computed(() => {
  let pos = "-bottom-6 left-1/2 -translate-x-1/2";
  if (props.labelPosition == "left") {
    pos = "right-full -translate-x-2 top-1/2 -translate-y-1/2";
  } else if (props.labelPosition == "top") {
    pos = "-top-6 left-1/2 -translate-x-1/2";
  } else if (props.labelPosition == "right") {
    pos = "left-full translate-x-2 top-1/2 -translate-y-1/2";
  }
  return pos;
});

const progress = computed(() => {
  if (props.spacing == "between") {
    return progressBetween();
  } else {
    return progressEvenly();
  }
});

const progPos = computed(() => {
  if (props.spacing == "between") {
    return progPosBetween();
  } else {
    return progPosEvenly();
  }
});

const progressEvenly = () => {
  let ctr = props.pages.length ?? 0;
  let p = 100 / (ctr + 1);
  let index = (props.modelValue + 1) * p;
  return index;
};

const progressBetween = () => {
  let ctr = props.pages.length ?? 0;
  if (props.modelValue < 1) {
    return 2;
  } else if (props.modelValue == ctr - 1) {
    return 98;
  } else {
    let p = (100 / (ctr - 1)) * props.modelValue;
    return p;
  }
};

const progPosEvenly = () => {
  let r = [];
  let ctr = props.pages;
  let p = 100 / (ctr.length + 1);
  for (let i in ctr) {
    r.push(getStepData(props.pages[i], (i * 1 + 1) * p));
  }
  return r;
};

const progPosBetween = () => {
  let r = [];
  let ctr = props.pages.length;
  if (ctr > 1) {
    r.push(getStepData(props.pages[0], 2));
    for (let i = 1; i < ctr - 1; i++) {
      r.push(getStepData(props.pages[i], (100 / (ctr - 1)) * i));
    }
    r.push(getStepData(props.pages[ctr - 1], 98));
  }
  return r;
};

const getStepData = (page, pos) => {
  return {
    pos: pos,
    label: page.label,
    icon: page.icon,
    bgClass: page.bgClass,
    activeClass: page.activeClass,
    activeIconClass: page.activeIconClass,
    activeLabelClass: page.activeLabelClass,
    idleClass: page.idleClass,
    idleIconClass: page.idleIconClass,
    idleLabelClass: page.idleLabelClass,
  };
};

const stepLoc = (pos) => {
  if (props.vertical) {
    return {
      top: `${pos}%`,
    };
  }
  return {
    left: `${pos}%`,
  };
};
</script>
