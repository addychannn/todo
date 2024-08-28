<template>
  <Menu>
    <Float
      flip
      portal
      :offset="offset"
      :shift="6"
      :arrow="arrowOffset"
      :placement="placement"
      v-bind="useTransition('opacity')"
    >
      <MenuButton
        v-bind="$attrs"
        class="relative overflow-hidden rounded-md p-0"
        @click.stop
      >
        <FocusHelper color="bg-foreground" />
        <slot></slot>
        <SizeObserver v-if="autoWidth" @resize="onResize" />
      </MenuButton>

      <MenuItems
        v-if="!!items"
        class="max-h-[75vh] min-w-[8rem] overflow-hidden rounded-lg border-x border-foreground/25 bg-page-background text-foreground shadow-md focus:outline-none"
        :style="{ width: `${menuWidth}px` }"
      >
        <FloatArrow
          v-if="arrow"
          class="absolute h-5 w-5 rotate-45 bg-page-background"
        />
        <div class="relative overflow-hidden rounded-md py-0.5">
          <MenuItem
            class="absolute inset-x-0 top-0 flex items-center bg-foreground/25 py-0.5"
          >
            <div></div>
          </MenuItem>
          <template
            v-for="(item, index) in items"
            :key="`menu_item_${index}_${item.label}`"
          >
            <template v-if="!!item.separator">
              <MenuItem
                class="flex items-center border-b"
                :class="[item.separatorColor ?? 'border-foreground/25']"
              >
                <div></div>
              </MenuItem>
            </template>
            <template v-else>
              <MenuItem
                v-if="!item.hidden"
                v-slot="{ active }"
                :disabled="item.disabled || !item.action"
                :class="[
                  ..._itemClass,
                  (item.disabled || !item.action) &&
                    '!cursor-not-allowed !text-gray-400',
                ]"
              >
                <button
                  type="button"
                  class="flex w-full items-center px-2 py-1.5 text-left leading-none transition-colors"
                  :class="[
                    active && (item.activeColor ?? 'bg-primary text-light'),
                  ]"
                  @click="item.action() ?? null"
                >
                  <TIcon
                    v-if="!!item.icon"
                    :name="item.icon"
                    size="sm"
                    class="mr-1"
                  />
                  <span
                    class="px-1 leading-none"
                    :class="[!!item.icon && 'border-l']"
                  >
                    {{ item.label }}
                  </span>
                </button>
              </MenuItem>
            </template>
          </template>
          <MenuItem
            class="absolute inset-x-0 bottom-0 flex items-center bg-foreground/25 py-0.5"
          >
            <div></div>
          </MenuItem>
        </div>
      </MenuItems>
    </Float>
  </Menu>
</template>

<script setup>
import { Float, FloatArrow } from "@headlessui-float/vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { ref, computed } from "vue";
import { transitions } from "@/scripts";

const props = defineProps({
  /**
   * label: String,
   * action: function,
   * icon: string,
   * disabled: Boolean
   * hidden: Boolean
   *
   * OR
   *
   * separator: Boolean,
   * separatorColor: String (Border color)
   */
  items: Object,

  arrow: {
    type: Boolean,
    default: true,
  },

  offset: {
    type: Number,
    default: 8,
  },

  arrowOffset: {
    type: Number,
    default: 8,
  },

  autoWidth: {
    type: Boolean,
    default: false,
  },

  placement: {
    type: String,
    default: "bottom-end",
    validator: (val) =>
      [
        "left",
        "top",
        "right",
        "bottom",

        "left-start",
        "top-start",
        "right-start",
        "bottom-start",

        "left-end",
        "top-end",
        "right-end",
        "bottom-end",
      ].indexOf(val) > -1,
  },

  itemClass: {
    type: [String, Array],
    default: () => [],
  },
});

const menuWidth = ref(0);

const _itemClass = computed(() =>
  Array.isArray(props.itemClass) ? props.itemClass : [props.itemClass]
);

const onResize = (size) => {
  menuWidth.value = size.width;
};

const useTransition = (name) => {
  let trans = transitions[name];
  return {
    enter: trans.enterActiveClass,
    enterFrom: trans.enterFromClass,
    enterTo: trans.enterToClass,
    leave: trans.leaveActiveClass,
    leaveFrom: trans.leaveFromClass,
    leaveTo: trans.leaveToClass,
  };
};
</script>
