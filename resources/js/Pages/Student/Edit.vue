<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

const props = defineProps({ 
    errors: Object,
    student: Object,
 });

const form = useForm({
    lrn: props.student.lrn,
    name:props.student.name,
    sex: props.student.sex,
    grade:props.student.grade,
    email:props.student.email,
});

const updateStudent = () => {
    
    const res = form.put(route('students.update', props.student.id));
    if(res){
        form.reset();
    }
};
</script>

<template>
    
    <Head title="Edit Student" />

    <AuthenticatedLayout>

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
      
        <div class="mt-4 mx-4">
        <div class="flex justify-between">
            <h5 class="m-4">Edit Student</h5>
            <Link :href="route('students.index')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
        </div>

        <form @submit.prevent="updateStudent()">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        <label>Student ID</label>
                        <input type="text" v-model="form.lrn" class="py-1 w-full" />
                        <div v-if="errors.lrn" class="text-red-500">{{ errors.lrn }}</div>
                    </div>

                    <div class="mb-3">
                        <label>Student Name</label>
                        <input type="text" v-model="form.name" class="py-1 w-full" />
                        <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                    </div>

                    <div class="mb-3">
                        <label>Student sex</label>
                        <select v-model="form.sex" class="py-1 w-full">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <div v-if="errors.sex" class="text-red-500">{{ errors.sex }}</div>
                    </div>

                    <div class="mb-3">
                        <label>Student's Grade</label>
                        <input type="text" v-model="form.grade" class="py-1 w-full" />
                        <div v-if="errors.grade" class="text-red-500">{{ errors.grade }}</div>

                    </div>

                    <div class="mb-3">
                        <label>Parent's Email</label>
                        <input type="email" v-model="form.email" class="py-1 w-full" />
                        <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>

                    </div>

                    <div class="mb-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                        >
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update</span>
                        </button>
                    </div>
                </div>
            </div>  
        </form>

    </div>
    </AuthenticatedLayout>

</template>

