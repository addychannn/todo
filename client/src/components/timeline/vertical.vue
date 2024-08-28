<template>
  <div
    class="group/timeline relative box-border flex items-stretch bg-page-background"
  >
    <div
      class="min-w-6 relative flex items-start justify-center"
      :class="[!slots.datetime && 'mr-2']"
    >
      <div
        class="z-10 box-border flex aspect-square items-center justify-center rounded-full border-2 p-0.5 leading-none"
        :class="[
          active
            ? 'border-primary bg-page-background'
            : 'border-transparent bg-transparent',
        ]"
      >
        <span
          class="flex !aspect-square items-center justify-center rounded-full border p-0.5 leading-none"
          :class="[
            active
              ? 'border-transparent'
              : 'border-gray-400 bg-page-background',
          ]"
        >
          <slot name="icon" :active="active">
            <TIcon
              :name="icon"
              :type="iconType"
              :size="iconSize"
              :class="[active ? 'text-primary' : 'text-gray-400']"
            />
          </slot>
        </span>
      </div>
      <div
        class="absolute left-1/2 top-3 z-[3] h-full -translate-x-1/2 border-x border-gray-400 group-last/timeline:hidden"
      ></div>
    </div>
    <div class="grid flex-auto grid-cols-12 items-start" :class="_contentClass">
      <div v-if="!!slots.datetime" class="col-span-12 px-2 py-1 md:col-span-3">
        <div
          class="relative flex items-center gap-1 md:justify-center"
          :class="[active && 'font-semibold']"
        >
          <slot name="datetime" :active="active" />
        </div>
      </div>

      <div
        class="col-span-12 flex flex-col items-stretch justify-start pb-3 md:col-span-9"
      >
        <div class="flex items-center">
          <slot name="title" :active="active">
            <span class="font-semibold">
              {{ title }}
            </span>
          </slot>
        </div>

        <div class="text-sm">
          <slot :active="active">
            <TReadMore
              class="line-clamp-4 leading-tight"
              overlayCollapsedClass="bg-gradient-to-t from-page-background"
            >
              {{ text }}
            </TReadMore>
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, useSlots } from "vue";
import dayjs from "dayjs";

const slots = useSlots();
const props = defineProps({
  title: String,
  text: {
    type: String,
    default: "",
  },
  active: {
    type: Boolean,
    default: false,
  },

  contentClass: {
    type: [String, Array],
    default: () => [],
  },

  icon: {
    type: String,
    default: "circle",
  },
  iconSize: {
    type: String,
    default: "sm",
    validator: (val) =>
      ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl"].indexOf(val) >
      -1,
  },
  iconType: {
    type: String,
    default: "filled",
    validator: (val) =>
      ["filled", "outlined", "round", "sharp", "two-tone"].indexOf(val) > -1,
  },
});

const _contentClass = computed(() =>
  Array.isArray(props.contentClass) ? props.contentClass : [props.contentClass]
);
</script>
