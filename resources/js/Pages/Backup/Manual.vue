<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>

<template>
  <AuthenticatedLayout>
    <div class="manual-container max-w-7xl mx-auto p-6 bg-white shadow-md rounded-md">
      <!-- Header Section -->
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Backup Manual</h1>

      <!-- Image Section for Backup 1 -->
      <div class="image-container text-center mb-10">
        <img
          src="/Images/Backup/Backup1.png"
          alt="Backup Step 1"
          class="image max-w-full h-80 object-contain mx-auto rounded-md shadow-lg"
        />
        <p class="mt-2 text-gray-700 text-sm">1. Click the Back up now Button to Back up Data.        </p>
      </div>

      <!-- Image Section for Backup 2 -->
      <div class="image-container text-center mb-10">
        <img
          src="/Images/Backup/Backup2.png"
          alt="Backup Step 2"
          class="image max-w-full h-80 object-contain mx-auto rounded-md shadow-lg"
        />
        <p class="mt-2 text-gray-700 text-sm">2. Server file path location where the backup file is being saved.        </p>
        <p class="mt-2 text-gray-700 text-sm">3. Backup data.        </p>

      </div>

      <!-- Image Section for Backup 3 -->
      <div class="image-container text-center mb-10">
        <img
          src="/Images/Backup/Backup3.png"
          alt="Backup Step 3"
          class="image max-w-full h-80 object-contain mx-auto rounded-md shadow-lg"
        />
        <p class="mt-2 text-gray-700 text-sm">4. Click to Download the backup data from the server to your computer.        </p>
      </div>

      <!-- Image Section for Backup 4 -->
      <div class="image-container text-center mb-10">
        <img
          src="/Images/Backup/Backup4.png"
          alt="Backup Step 4"
          class="image max-w-full h-80 object-contain mx-auto rounded-md shadow-lg"
        />
        <p class="mt-2 text-gray-700 text-sm">5. Extract the file from Downloads or In the File server folder.        </p>
      </div>

      <!-- Image Section for Backup 5 -->
      <div class="image-container text-center mb-10">
        <img
          src="/Images/Backup/Backup5.png"
          alt="Backup Step 5"
          class="image max-w-full h-80 object-contain mx-auto rounded-md shadow-lg"
        />
        <p class="mt-2 text-gray-700 text-sm">6. Choose the Extracted SQL file and Click the Restore database Button.        </p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.manual-container {
  background-color: #f9fafb; /* Light background for a clean layout */
}

.image-container img {
  max-width: 100%; /* Ensure the image is responsive */
  height: 20rem; /* Set a fixed height for uniform image sizes */
  object-fit: contain; /* Maintain aspect ratio while scaling */
  margin: 0 auto; /* Center the image horizontally */
}

.image-container p {
  font-size: 1rem;
  color: #4a4a4a;
  margin-top: 0.5rem;
}

.image-container {
  margin-bottom: 2.5rem; /* Spacing between the sections */
}
</style>
