<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const props = defineProps({
  route: String,
  label: String,
  pattern: String,
});

const isActive = ref(false);

const redirectRoute = () => {
  router.get(route(props.route));
};

onMounted(() => {
  const currentRouteName = usePage().props.currentRouteName as string;
  isActive.value = currentRouteName.includes(props.pattern);
});
</script>

<template>
  <li class="mr-3 flex-1">
    <div
      @click="redirectRoute()"
      class="cursor-pointer flex items-center py-1 md:py-3 pl-0 md:pl-1 align-middle text-white no-underline hover:text-white border-b-2 hover:border-blue-500"
      :class="{'border-blue-500' : isActive }"
    >
      <slot /> <!-- Heroicon slot -->
      <span
        :class="['pb-1 md:pb-0 text-sm', isActive ? 'text-blue-500' : 'text-black']"
        class="block md:inline-block ml-2"
        >{{ props.label }}</span>
    </div>
  </li>
</template>
