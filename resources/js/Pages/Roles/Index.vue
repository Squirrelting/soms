<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { ref, defineProps } from "vue";
import {
    PencilSquareIcon,
    ShieldExclamationIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps<{
    roles: {
        data: Array<{
            id: number;
            name: string;
            number: number;
        }>;
    };
}>();

const searchTerm = ref("");

const search = () => {
    router.get(
        route("users.roles-permissions.roles.index"),
        {
            searchTerm: searchTerm.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};
</script>
<template>
    <Head title="Roles" />
    <AuthenticatedLayout>
        <template #mobileMenuName> Roles </template>

        <div class="card shadow p-5 rounded-sm bg-white">
            <div class="flex flex-col mb-2">
                <div class="flex justify-start">
                    <h1 class="text-xl mb-2">Roles list</h1>
                </div>

                <div class="flex flex-row gap-2 justify-end mb-2">
                    <div>
                        <input
                            type="text"
                            placeholder="Search..."
                            class="input input-bordered w-full max-w-xs"
                            @keyup="search"
                            v-model="searchTerm"
                        />
                    </div>
                </div>
            </div>
            <div v-if="roles.data.length > 0" class="w-full">
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">No.</th>
                            <th class="py-2 px-4 text-left border">
                                Role name
                            </th>
                            <th class="py-2 px-4 text-left border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(role, index) in roles.data" :key="role.id">
                            <td class="py-2 px-4 border">{{ role.number }}</td>
                            <td class="py-2 px-4 border">{{ role.name }}</td>
                            <td class="py-2 px-4 border">
                                <div
                                    v-if="
                                        !['admin', 'super-admin'].includes(role.name)
                                    "
                                >
                                    <Link
                                        class="inline-flex items-center px-4 py-3 bg-none dark:bg-none rounded-md font-semibold text-xs text-blue-500 dark:text-blue-500 uppercase tracking-widest hover:text-blue-900 dark:hover:text-blue-400 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150"
                                        :href="
                                            route(
                                                'users.roles-permissions.roles.edit',
                                                {
                                                    id: role.id,
                                                }
                                            )
                                        "
                                    >
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :pagination="roles" />
            </div>
            <div v-else class="w-full">
                <div
                    class="justify-between mb-6 rounded-lg bg-white p-6 sm:flex sm:justify-start dark:bg-gray-800"
                >
                    <ShieldExclamationIcon class="h-10" />
                    <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                        <div class="mt-5 sm:mt-0">
                            <h2
                                class="text-lg font-bold text-gray-900 dark:text-white"
                            >
                                No roles found
                            </h2>
                        </div>
                        <div
                            class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
