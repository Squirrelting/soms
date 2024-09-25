<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    signatory: {
        type:Array,
    }
});

const form = useForm({});

const signatoryId = ref('');
const getId = (id) => {
    signatoryId.value = id;
};


const DeleteSignatory = (id) => {
    router.delete(route('signatory.destroy', id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
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
        <div
        v-if="$page.props.flash.message"
        role="alert"
        class="alert alert-info mt-4 mx-5 px-4 py-2"
    >
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 shrink-0 stroke-current"
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
    <span>{{ $page.props.flash.message }}</span>
    </div>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Signatory Page</h2>
        </template>


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
                                @click="getId(signatory.id)"
                                onclick="my_modal_1.showModal()"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dialog id="my_modal_1" class="modal">
                                <div class="modal-box">
                                    <h3 class="text-lg font-bold">Hello!</h3>
                                    <p class="py-4">
                                        Are you sure you want to delete all this
                                        Signatory data? This action cannot be
                                        undone.
                                    </p>
                                    <div class="modal-action">
                                        <form method="dialog">
                                            <!-- if there is a button in form, it will close the modal -->
                                            <button class="btn">Cancel</button>
                                            <button
                                                @click="DeleteSignatory(signatoryId)"
                                                class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-600 focus:outline-none hover:bg-red-500"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
    </AuthenticatedLayout>
</template>
