<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";

// Correct type annotation inside defineProps
const props = defineProps({
    route: String,
    label: String,
    pattern: String,
});

const isActive = ref(false);
const redirectRoute = () => {
    router.get(route(props.route));
}

onMounted(() => {
    const currentRouteName = usePage().props.currentRouteName as string;
    isActive.value = currentRouteName.includes(props.pattern);
    console.log(isActive.value);
    console.log('label: ' + props.label);
    console.log('pattern: ' + props.pattern);
    console.log('current route name: ' + currentRouteName);
});
</script>

<template>
    <li class="mr-3 flex-1">
        <div
            @click="redirectRoute()"
            class="cursor-pointer block py-1 md:py-3 pl-0 md:pl-1 align-middle text-white no-underline hover:text-white border-b-2 hover:border-blue-500"
            :class="{'border-blue-500' : isActive }"
        >
            <i class="fa fa-wallet pr-0 md:pr-3"></i>
            <span
                class="pb-1 md:pb-0 text-xs md:text-base text-black block md:inline-block"
            >{{ props.label }}</span>
        </div>
    </li>
</template>
