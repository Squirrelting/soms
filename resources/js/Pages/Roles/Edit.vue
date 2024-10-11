<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { defineProps } from "vue";

const props = defineProps<{
    errors: {
        name: string;
    };
    role: {
        id: number;
        name: string;
        permissions: Array<{
            id: number;
            name: string;
        }>;
    };
    permissions: Array<{
        id: number;
        name: string;
    }>;
    flash: {
        successMessage: string;
        errorMessage: string;
    };
}>();

const form = useForm({
    name: props.role.name,
});

const assignPermission = (name: string, id: number) => {
    router.post(
        route("users.roles-permissions.roles.assignPermission", { id: id }),
        { name: name },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};
</script>

<template>
    <Head title="Edit Role" />
    <AuthenticatedLayout>
        <template #mobileMenuName> Edit Role </template>

        <div class="relative card shadow p-5 rounded-sm bg-white dark:bg-slate-400">
            <!-- Cancel Button at the top-right -->
            <Link
                :href="route('users.roles-permissions.roles.index')"
                class="absolute top-4 right-4 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-red-500 focus:bg-red-700 focus:outline-none transition ease-in-out duration-150"
            >
                Back
            </Link>

            <div class="flex flex-col justify-start m-5">
                <div class="flex justify-start">
                    <span class="font-bold text-xl">Permissions</span>
                </div>

                <div
                    v-if="props.flash.successMessage"
                    role="alert"
                    class="alert alert-success mt-4 mb-4"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="stroke-current shrink-0 h-6 w-6 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span>{{ props.flash.successMessage }}</span>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="relative p-4 bg-gray-100 rounded-md shadow-md"
                    >
                        <label class="flex items-center cursor-pointer">
                            <span class="ml-2 font-medium">{{ permission.name }}</span>
                            <input
                                type="checkbox"
                                @change="assignPermission(permission.name, props.role.id)"
                                :checked="props.role.permissions.some(
                                    (rolePermission) => rolePermission.name === permission.name
                                )"
                                class="ml-auto checkbox appearance-none h-5 w-5 border border-gray-300 rounded-md checked:bg-green-500 checked:border-transparent focus:ring-green-500 focus:ring-2 focus:ring-offset-2"
                            />
                            <span
                                v-if="props.role.permissions.some(
                                    (rolePermission) => rolePermission.name === permission.name
                                )"
                                class="ml-2 text-green-500 text-xl"
                            >
                                ✔️
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
