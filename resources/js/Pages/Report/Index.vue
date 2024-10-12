<script setup>
import { ref, watch, computed } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import axios from "axios"; 
import { CalendarDaysIcon } from "@heroicons/vue/24/solid";

</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <!-- Message from the controller -->
        <div
            v-if="$page.props.flash.message"
            role="alert"
            class="alert alert-info mt-4 mx-5 px-4 py-2"
        >
            <span class="ml-2 text-base text-gray-800">{{ $page.props.flash.message }}</span>
        </div>

        <!-- Student List with Search and Filters -->
        <div class="mt-4 mx-4">

            <div class="flex justify-between items-center mb-2 space-x-2">
    <h5 class="text-lg font-semibold text-gray-700">
        Student List Report
    </h5>
</div>
</div>

    </AuthenticatedLayout>
</template>

