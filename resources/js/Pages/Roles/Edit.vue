<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
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

const updateRole = (id: number) => {
    form.put(route("users.roles-permissions.roles.update", { id: id }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            alert("Updated successfully");
        },
        onError: () => {
            alert("Error");
        },
    });
};

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

        <div class="card shadow p-5 rounded-sm bg-white dark:bg-slate-800">
            <div class="flex justify-start ml-5">
                <span class="font-bold text-xl">EDIT ROLE</span>
            </div>
            <div>
                <div class="flex justify-start m-5">
                    <form
                        class="w-full"
                        @submit.prevent="updateRole(props.role.id)"
                    >
                        <div>
                            <div class="flex flex-col m-2">
                                <div class="mt-5">
                                    <div class="flex flex-start gap-1">
                                        <label for="plan_name" class="font-bold"
                                            >Role name:
                                        </label>
                                        <span class="text-red-500 font-bold"
                                            >*</span
                                        >
                                    </div>

                                    <TextInput
                                        id="role_name"
                                        type="text"
                                        :class="{
                                            'input-bordered input-error':
                                                props.errors.name,
                                        }"
                                        class="mt-2 !max-w-full"
                                        v-model="form.name"
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="props.errors.name"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="m-5 flex justify-end">
                            <Link
                                :href="route('users.roles-permissions.roles.index')"
                                class="inline-flex items-center btn px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                class="ml-2"
                                type="submit"
                                :disabled="form.processing"
                                >Update Role Name
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
            <div class="flex flex-col justify-start m-5">
                <div class="flex justify-start">
                    <span class="font-bold text-xl">Permissions</span>
                </div>
                <div
                    v-if="props.flash.successMessage"
                    role="alert"
                    class="alert alert-success"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="stroke-current shrink-0 h-6 w-6"
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
                    <span
                        >temporary alert: {{ props.flash.successMessage }}</span
                    >
                </div>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <div
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="relative m-5 bg-slate-100 w-50 h-50 rounded p-0"
                    >
                        <label class="label cursor-pointer">
                            <span class="label-text">{{
                                permission.name
                            }}</span>
                            <input
                                type="checkbox"
                                @change="
                                    assignPermission(
                                        permission.name,
                                        props.role.id
                                    )
                                "
                                :checked="
                                    props.role.permissions.some(
                                        (rolePermission) =>
                                            rolePermission.name ===
                                            permission.name
                                    )
                                "
                                class="checkbox ml-2"
                            />
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>