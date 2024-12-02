<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FolderArrowDownIcon } from "@heroicons/vue/24/solid";
import axios from "axios";

const files = ref([]); // To store the list of backup files
const loading = ref(false); // For showing a loading state
const filePath = ref(''); // File path location

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

    const loadingSwal = Swal.fire({
      title: 'Backing up...',
      text: 'Your backup is being processed in the background.',
      icon: 'info',
      showConfirmButton: false,
      didOpen: () => Swal.showLoading(),
    });

    const response = await axios.post('/backup/db');

    loadingSwal.close();

    if (response.data.file) {
      Swal.fire({
        title: 'Backup Successful!',
        text: 'Your database backup is ready.',
        icon: 'success',
        confirmButtonText: 'Download Backup',
        showCancelButton: true,
        cancelButtonText: 'Close',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = response.data.file; // Trigger file download
        }
        fetchFiles(); // Refresh file list
      });
    } else {
      Swal.fire({
        title: 'Backup Started!',
        text: response.data.message,
        icon: 'info',
      });
      fetchFiles(); // Refresh file list
    }
  } catch (error) {
    Swal.close();
    Swal.fire('Error', error.response?.data?.message || 'Backup failed.', 'error');
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
        <div class="flex justify-between">
          <h5 class="m-4">Backups</h5>
          <Link
            :href="route('signatory.index')"
            class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
          >Back</Link>
        </div>

        <!-- Button for triggering the backup -->
        <div class="flex items-center space-x-2">
          <button
            @click="handleBackup"
            class="bg-blue-600 text-white py-2 px-4 rounded inline-flex items-center"
          >
            <FolderArrowDownIcon class="h-6 w-6 mr-4" />
            Back up now
          </button>
        </div>
        <div>
          <strong>Server file Path Location:</strong>
          <span>{{ filePath || 'No backups yet' }}</span>
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
