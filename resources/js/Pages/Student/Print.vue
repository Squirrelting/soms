<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

defineProps({
    errors: Object,
    signatory: Object,
    student: Object,
});

const form = useForm({
    signatory: '',
});


</script>

<template>
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Print Certificate of Good Moral</h5>
                <Link :href="route('dashboard')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
            </div>
        </div>

        <form>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        <label>Select Signatory</label>
                        <select v-model="form.signatory" class="py-1 w-full">
                            <option value="">Select Signatory</option>
                            <option v-for="signatory in signatory" :key="signatory.id" :value="signatory.id">
                                {{ signatory.name }}, {{ signatory.position }}
                            </option>
                        </select>
                        <div v-if="errors.signatory" class="text-red-500">{{ errors.signatory }}</div>
                    </div>
                    <div class="mb-3">
                        <a
                            target="_blank"
                            :href="'/students/print-certificate/' + form.signatory + '/' + student.id"
                            :disabled="form.processing"
                            class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                        >
                            <span v-if="form.processing">Printing...</span>
                            <span v-else>Print</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>


