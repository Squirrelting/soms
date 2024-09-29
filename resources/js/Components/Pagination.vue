<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
    }
});

// Determine the current page
const currentPage = computed(() => props.pagination.current_page);
</script>

<template>
<div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
  <div class="flex flex-1 justify-between sm:hidden">
    <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
    <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
  </div>
  <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
    <div>
      <p class="text-sm text-gray-700">
        Showing
        <span class="font-medium">{{ pagination.from }}</span>
        to
        <span class="font-medium">{{ pagination.to }}</span>
        of
        <span class="font-medium">{{ pagination.total }}</span>
        results
      </p>
    </div>
    <div>
      <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
        <template v-for="link in pagination.links" :key="link.label">
          <Link
            preserve-scroll
            :href="link.url ?? ''"
            v-html="link.label"
            :class="[
              'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-500 focus:z-20 focus:outline-offset-0',
              link.active ? 'bg-gray-800 text-white' : 'text-gray-900'
            ]"
          />
        </template>
      </nav>
    </div>
  </div>
</div>
</template>
