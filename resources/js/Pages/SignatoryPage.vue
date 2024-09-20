<script setup>
import { ref } from 'vue';
import CustomModal from '@/Components/CustomModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    signatory: {
        type:Array,
    }
});

const form = useForm({});

//Modal
const showModal = ref(false);
const passId = ref(null);

function openModal(id){
    passId.value = id;
    showModal.value = true;
}

const DeleteSignatory = (id) => {
    router.delete(route('signatory.destroy', id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            alert('success');
        },
        onError: () => console.error('Failed to delete signatory'),
    });
}


</script>

<template>
    <Head title="Signatory" />

    <AuthenticatedLayout>
        <!-- message from SignatoryController-->
        <div v-if="$page.props.flash.message" class="alert bg-green-200 mt-4 mx-5 px-4 py-2">
        {{ $page.props.flash.message }}
      </div>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Signatory Page</h2>
        </template>

            <!-- <div class="max-w-7xl mx-auto sm:px-3 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div> -->


        <!-- Student List Table -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Signatory List</h5>
                <Link :href="route('signatory.create')" class="bg-blue-500 text-white p-3 rounded mb-4">Add Signatory</Link>
            </div>
            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">Signatory name</th>
                        <th class="py-2 px-4 text-left border">Position</th>
                        <th class="py-2 px-4 text-left border">Action</th>
                    </tr>
                </thead>
                <tbody>
                        <tr v-for="(signatory, index) in signatory" :key="index">
                            <td class="py-2 px-4 border">{{ signatory.name }}</td>
                            <td class="py-2 px-4 border">{{ signatory.position }}</td>
                        <td>
                            <Link 
                                :href="route('signatory.edit', signatory.id)" 
                                class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded me-2 inline-block"
                                >
                                Edit
                            </Link>
                            <button 
                                @click="openModal(signatory.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block"
                                >
                                Delete
                            </button>
                            <CustomModal v-if="showModal" @close="showModal=false">
                                <p class="text-gray-800">
                                     Are you sure you want to delete all this signatory data? This action cannot be undone.
                                </p>
                                <div class="text-right mt-4">
                                    <button @click="showModal = false" class="px-4 py-2 text-sm text-black-800 focus:outline-none hover:underline">Cancel</button>
                                    <button @click="DeleteSignatory(passId)" class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-600 focus:outline-none hover:bg-red-500">Delete</button>
                                </div>
                            </CustomModal>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
