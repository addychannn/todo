<template>
  <div class="relative flex max-h-[65dvh] min-w-max flex-col">
    <div class="flex items-center justify-end px-2 py-1">
      <TButton
        icon="restore"
        label="Restore Default"
        class="rounded-md px-2"
        @click="emit('reset')"
      />
    </div>
    <div class="flex-auto overflow-y-auto px-2 py-1">
      <Sortable
        v-if="showing"
        class="flex flex-col gap-1"
        ref="sortableRef"
        :list="_columns"
        item-key="column"
        :options="options"
        @end="onEnd"
      >
        <template #item="{ element, key }">
          <div
            :data-id="key"
            class="flex cursor-pointer select-none items-center gap-1 rounded-md border border-foreground/25"
            @click="toggle(key)"
          >
            <div class="itens-center flex justify-center">
              <TIcon
                :name="element.show ? 'check_box' : 'check_box_outline_blank'"
              />
            </div>
            <div class="flex-auto px-2 py-1">{{ element.label }}</div>
            <div
              class="handle flex cursor-grab items-center self-stretch"
              @click.stop
            >
              <TIcon name="drag_indicator" />
            </div>
          </div>
        </template>
      </Sortable>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue";
import { useVModel } from "@vueuse/core";
import { Sortable } from "sortablejs-vue3";
const props = defineProps({
  columns: Object,
});
const emit = defineEmits(["update:columns", "reset"]);

const sortableRef = ref(null);
const showing = ref(true);
const options = ref({
  handle: ".handle",
  animation: 200,
  group: "cols",
  disabled: false,
  ghostClass: "ghost",
  forceFallback: true,
});

const _columns = useVModel(props, "columns", emit);

const onEnd = () => {
  let sorted = sortableRef.value.sortable.toArray();
  let result = sorted.map((item) =>
    _columns.value.find((col) => col.column == item)
  );
  _columns.value = result;
};
const toggle = (key) => {
  let tmp = _columns.value.find((item) => item.column == key);
  tmp.show = !tmp.show;
};
</script>

<style scoped lang="scss">
.ghost {
  @apply bg-info text-dark opacity-25;
}
</style>
