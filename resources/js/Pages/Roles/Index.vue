<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { ref, defineProps } from "vue";
import {
    TrashIcon,
    PencilSquareIcon,
    ShieldExclamationIcon,
    UserPlusIcon,
} from "@heroicons/vue/24/solid";
import DangerButton from "@/Components/DangerButton.vue";

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

const selectedRoleName = ref("");
const selectedRoleId = ref(0);

const deleteModal = (id: number, name: string) => {
    selectedRoleName.value = name;
    selectedRoleId.value = id;
};

const deleteRole = (id: number) => {
    router.delete(route("users.roles-permissions.roles.delete", { id: id }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            alert("deleted successfully");
            selectedRoleId.value = 0;
            selectedRoleName.value = "";
        },
        onError: () => {
            alert("Somthing went wrong");
            selectedRoleId.value = 0;
            selectedRoleName.value = "";
        },
    });
};
</script>
<template>
    <Head title="Roles" />
    <AuthenticatedLayout>
        <template #mobileMenuName> Roles </template>

        <div class="card shadow p-5 rounded-sm bg-white dark:bg-slate-800">
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
                    <div>
                        <Link
                            class="disabled:text-gray-500 inline-flex hover:border-transparent items-center px-4 py-3 btn bg-kwikweb-400 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-kwikweb dark:hover:bg-white focus:bg-kwikweb-200 dark:focus:bg-white active:bg-kwikweb-900 dark:active:bg-gray-300 focus:outline-none focus:ring-offset-2 transition ease-in-out duration-150"
                            :href="route('users.roles-permissions.roles.add')"
                        >
                            <UserPlusIcon class="h-5" />add role
                        </Link>
                    </div>
                </div>
            </div>
            <div v-if="roles.data.length > 0" class="w-full">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Role name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(role, index) in roles.data" :key="role.id">
                            <th>{{ role.number }}</th>
                            <td>{{ role.name }}</td>
                            <td>
                                <Link
                                    class="inline-flex items-center px-4 py-3 bg-none dark:bg-none rounded-md font-semibold text-xs text-blue-500 dark:text-blue-500 uppercase tracking-widest hover:text-blue-900 dark:hover:text-blue-400 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150"
                                    :href="
                                        route('users.roles-permissions.roles.edit', {
                                            id: role.id,
                                        })
                                    "
                                >
                                    <PencilSquareIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    class="inline-flex items-center px-4 py-3 bg-none dark:bg-none rounded-md font-semibold text-xs text-red-500 dark:text-red-500 uppercase tracking-widest hover:text-red-900 dark:hover:text-red-400 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150"
                                    onclick="deleteModal.showModal()"
                                    @click="deleteModal(role.id, role.name)"
                                >
                                    <TrashIcon class="h-5 w-5"></TrashIcon>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :pagination="roles"/>
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

        <dialog id="deleteModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">
                    Are you sure you want to delete this role?
                </h3>
                <p class="py-4">
                    You are about to delete this role "{{ selectedRoleName }}"
                </p>
                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Cancel</button>
                        <DangerButton
                            @click="deleteRole(selectedRoleId)"
                            class="ml-2"
                            >Yes, Delete it
                        </DangerButton>
                    </form>
                </div>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
