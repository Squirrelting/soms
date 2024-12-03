<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FolderArrowDownIcon } from "@heroicons/vue/24/solid";
import axios from "axios";

const files = ref([]); // Store the list of backup files
const loading = ref(false); // Show a loading state
const filePath = ref(''); // File path location
let pollingInterval = null; // Polling interval ID

// Function to fetch the list of backup files
const fetchFiles = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/backup/files');
    files.value = response.data.files;
    if (response.data.files.length) {
      filePath.value = response.data.files[0].path.split('backups')[0] + 'backups'; // Extract base path
    }
  } catch (error) {
    Swal.fire("Error", "Failed to fetch backup files.", "error");
  } finally {
    loading.value = false;
  }
};

// Store the uploaded file
const restoreFile = ref(null); 

const handleFileUpload = (event) => {
  restoreFile.value = event.target.files[0];
};

const handleRestore = async () => {
  if (!restoreFile.value) {
    Swal.fire("Error", "Please select a SQL file to restore.", "error");
    return;
  }

  const formData = new FormData();
  formData.append("sql_file", restoreFile.value);

  try {
    const result = await Swal.fire({
      title: "Are you sure?",
      text: "This will overwrite the current database.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, restore!",
      cancelButtonText: "No, cancel",
    });

    if (!result.isConfirmed) return;

    loading.value = true;
    const response = await axios.post("/backup/restore", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    Swal.fire("Success", response.data.message, "success");
    restoreFile.value = null; // Reset the file input
  } catch (error) {
    Swal.fire("Error", error.response?.data?.message || "Restore failed.", "error");
  } finally {
    loading.value = false;
  }
};


// Polling function to check backup completion
const startPolling = () => {
  pollingInterval = setInterval(async () => {
    const response = await axios.get('/backup/files');
    if (response.data.files.length > files.value.length) {
      files.value = response.data.files;
      Swal.fire("Success", "Backup completed successfully!", "success");
      clearInterval(pollingInterval);
      loading.value = false;
    }
  }, 5000); // Poll every 5 seconds
};

// Function to handle backup creation
const handleBackup = async () => {
  try {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: 'Do you want to back up the database?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, back up!',
      cancelButtonText: 'No, cancel',
    });

    if (!result.isConfirmed) return;

    loading.value = true;

    const response = await axios.post('/backup/db');

    if (response.status === 200) {
      Swal.fire({
        title: 'Backup Started',
        text: response.data.message,
        icon: 'info',
      });
      startPolling(); // Start polling to check for the new file
    }
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Backup failed.', 'error');
    loading.value = false;
  }
};

// Function to download a backup file
const downloadFile = (fileName) => {
  const downloadUrl = route('backup.download', fileName);
  window.location.href = downloadUrl;
};

// Fetch files on component mount
onMounted(() => {
  fetchFiles();
});
</script>




<template>
  <Head title="Backup" />
  <AuthenticatedLayout>
    <div class="mt-4 mx-4">
      <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">


        <div>
  <!-- Header Section -->
  <div class="flex justify-between items-center mb-4">
    <div class="flex justify-between items-center mb-4">
    <h5 class="text-lg font-semibold m-4">Backups</h5>
        <!-- Backup Button -->
        <button
      @click="handleBackup"
      class="bg-blue-600 text-white py-1 px-3 rounded inline-flex items-center"
    >
      <FolderArrowDownIcon class="h-4 w-4 mr-1" />
      Back up now
    </button>
    </div>

        <!-- Restore Section -->
        <div class="flex items-center space-x-2">
      <label for="restore-file" class="text-sm font-medium text-gray-700">
        Upload SQL File:
      </label>
      <input 
        id="restore-file" 
        type="file" 
        accept=".sql" 
        @change="handleFileUpload" 
        class="border border-gray-300 rounded px-2 py-1 text-sm"
      />
      <button 
        @click="handleRestore" 
        class="bg-red-600 text-white py-1 px-3 rounded"
      >
        Restore Database
      </button>
    </div>
  </div>
</div>





        <div>
          <strong>Server file Path Location:</strong>
          <span>{{ filePath || 'No backups yet' }}</span>
        </div>


        <div v-if="loading" class="text-center my-4">
  <div class="loader"></div> <!-- Add a CSS spinner or loading animation -->
  <p>Processing backup... Please wait.</p>
</div>

        <!-- Display the list of files -->
        <div v-if="loading" class="text-center my-4">Loading files...</div>
        <table v-if="files.length" class="table-auto w-full border-collapse border border-gray-300 mt-4">
          <thead>
            <tr>
              <th class="border border-gray-300 px-4 py-2">#</th>
              <th class="border border-gray-300 px-4 py-2">File Name</th>
              <th class="border border-gray-300 px-4 py-2">Created At</th>
              <th class="border border-gray-300 px-4 py-2">Action</th>

            </tr>
          </thead>
          <tbody>
            <tr v-for="(file, index) in files" :key="file.name">
              <td class="border border-gray-300 px-4 py-2">{{ index + 1 }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ file.name }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ file.created_at }}</td>
              <td class="border border-gray-300 px-4 py-2">
  <button
    @click="downloadFile(file.name)"
    class="bg-green-600 text-white py-1 px-3 rounded"
  >
    Download
  </button>
</td>


            </tr>
          </tbody>
        </table>
        <p v-else class="text-center mt-4">No backup files available.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style setup>
.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

</style>