<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

defineProps({
    students: Array,
});

const form = useForm({});

const deleteStudent = (studentId) => {
    if(confirm("Are you sure you want to delete this data?")){
        form.delete(route('students.destroy', studentId))
    }
};
</script>

<template>
    
    <Head title="Students" />

    <AuthenticatedLayout>
        
        <div v-if="$page.props.flash.message" class="alert bg-green-200 mt-4 mx-5 px-4 py-2">
        {{ $page.props.flash.message }}
      </div>

        <div class="mt-4 mx-4">
        <div class="flex justify-between">
            <h5 class="m-4">Student List</h5>
            <Link :href="route('students.create')" class="bg-blue-500 text-white p-3 rounded mb-4">Add Student</Link>
        </div>
        <table class="w-full bg-white border border-gray-200 shadow">
            <thead>
                <tr>
                    <th class="py-2 px-4 text-left border">Id</th>
                    <th class="py-2 px-4 text-left border">Student ID</th>
                    <th class="py-2 px-4 text-left border">Name</th>
                    <th class="py-2 px-4 text-left border">Sex</th>
                    <th class="py-2 px-4 text-left border">Grade</th>
                    <th class="py-2 px-4 text-left border">Parent's Email</th>
                    <th class="py-2 px-4 text-left border">Actions</th>

                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in students"
                    :key="index"
                >
                    <td class="py-2 px-4 border">{{ item.id }}</td>
                    <td class="py-2 px-4 border">{{ item.student_id }}</td>
                    <td class="py-2 px-4 border">{{ item.name }}</td>
                    <td class="py-2 px-4 border">{{ item.sex }}</td>
                    <td class="py-2 px-4 border">{{ item.grade }}</td>
                    <td class="py-2 px-4 border">{{ item.email }}</td>
                    <td>
                        <Link 
                            :href="route('students.show', item.id)" 
                            class="px-2 py-1 text-sm bg-blue-300 text-dark p-3 rounded me-2 inline-block"
                        >
                            Show
                        </Link>
                        <Link 
                            :href="route('students.edit', item.id)" 
                            class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded me-2 inline-block"
                        >
                            Edit
                        </Link>
                        <button 
                            type="submit" 
                            class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block"
                            @click="deleteStudent(item.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </AuthenticatedLayout>

</template>

