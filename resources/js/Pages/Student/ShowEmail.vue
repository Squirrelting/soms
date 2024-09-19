<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

defineProps({ 
    errors: Object,
    student: Object,
 });

</script>

<template>
    
    <Head title="Show Student" />

    <AuthenticatedLayout>
      
        <div class="mt-4 mx-4">
        <div class="flex justify-between">
            <h5 class="m-4">Show Student</h5>
            <Link :href="route('dashboard')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
        </div>

            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        <label>Student ID</label>
                        <p>
                            {{ student.lrn }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label>Student Name</label>
                        <p>
                            {{ student.name }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label>Student sex</label>
                        <p>
                            {{ student.sex }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <label>Student's Grade</label>
                        <p>
                            {{ student.grade }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <label>Parent's Email</label>
                        <p>
                            {{ student.email }}
                        </p>
                    </div>
                </div>
            </div>  

    </div>
    </AuthenticatedLayout>

</template>

