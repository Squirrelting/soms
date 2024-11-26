<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

// Define props to accept data from the backend
const props = defineProps({
    user: { // Accept user prop from backend
        type: Object,
        required: true
    },
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

// console.log('User:', props.user); 
</script>


<template>
    <Head title="Profile" />
  
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <!-- Update Profile Form -->
          <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
              class="max-w-xl"
            />
          </div>
  
          <!-- Update Password Form -->
          <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <UpdatePasswordForm class="max-w-xl" />
          </div>
  
          <!-- Loop through User and show Delete Form based on roles -->
          <div 
          v-if="!props.user.roles[0].name.includes('POD') && !props.user.roles[0].name.includes('ADVISER')"
          class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <DeleteUserForm class="max-w-xl" />
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
