<template>
  <router-link
    class="flex cursor-pointer select-none flex-col align-middle transition-all odd:bg-dark/5 hover:scale-[1.01] hover:bg-dark/10 dark:odd:bg-light/5 dark:hover:bg-light/10 md:table-row"
    :to="{ name: 'settings-users', params: { id: user.id } }"
  >
    <td class="p-1 md:px-3 md:py-2">
      <div class="flex items-center gap-2">
        <TImage
          :src="user.profile?.image?.thumbnails.small"
          class="h-8 w-8 rounded-full border-2 shadow-md"
          :class="[!!user.verified ? 'border-positive' : 'border-negative']"
        >
          <template #error>
            <div class="flex w-full items-center justify-center">
              <TIcon name="account_circle" class="text-gray-500" size="lg" />
            </div>
          </template>
        </TImage>
        <span v-if="!!user.profile?.full_name">
          {{ user.profile?.full_name }}
        </span>
        <span v-else class="italic text-gray-400"> (No Name) </span>
      </div>
    </td>
    <td class="p-1 md:px-3 md:py-2">{{ user.username }}</td>
    <td class="p-1 md:px-3 md:py-2">{{ user.email }}</td>
    <td class="p-1 md:px-3 md:py-2">
      <div class="flex flex-wrap items-start gap-1">
        <template v-for="role in user.roles" :key="role.id">
          <div
            class="rounded-full border border-dark/25 bg-slate-300 px-2 py-0.5 text-sm text-dark"
          >
            {{ role.name }}
          </div>
        </template>
      </div>
    </td>
    <td class="p-1 text-end md:px-3 md:py-2">
      <TIcon name="chevron_right" />
    </td>
  </router-link>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({
  user: Object,
});
</script>
