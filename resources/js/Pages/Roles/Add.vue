<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import { defineProps } from "vue";

const props = defineProps<{
    errors: {
        name: string;
    };
}>();

const form = useForm({
    name: "",
});

const submitRole = () => {
    form.post(route("users.roles-permissions.roles.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            alert("Added successfully");
        },
        onError: () => {
            alert("Error");
        },
    });
};
</script>
<template>
    <Head title="Add Roles" />
    <AuthenticatedLayout>
        <template #mobileMenuName> Add Roles </template>

        <div class="card shadow p-5 rounded-sm bg-white dark:bg-slate-400">
            <div class="flex justify-start ml-5">
                <span class="font-bold text-xl">ADD ROLE</span>
            </div>
            <div>
                <div class="flex justify-start m-5">
                    <form class="w-full" @submit.prevent="submitRole()">
                        <div>
                            <div class="flex flex-col m-2">
                                <div class="mt-5">
                                    <div class="flex flex-start gap-1">
                                        <label for="role_name" class="font-bold"
                                            >Role name:
                                        </label>
                                        <span class="text-red-500 font-bold"
                                            >*</span
                                        >
                                    </div>

                                    <TextInput
                                        id="plan_name"
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
                                >Save Role
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
