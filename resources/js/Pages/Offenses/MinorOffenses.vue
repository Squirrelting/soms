<script setup>
import CustomModal from '@/Components/CustomModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

const props = defineProps({ 
    errors: Object,
    student: Object,
    minorOffenses: Array,
    submittedminorOffenses: Array,
 });

// Initialize the form object
const form = useForm({
    minor_offense_id: '',
    lrn: props.student.lrn, 
    student_name: props.student.name, 
    student_grade: props.student.grade 
});

 // Function to save a minor offense
const saveMinorOffense = () => {
    form.post(route('minor.store'), {
        onSuccess: () => {
            alert('Offense added successfully');
            form.reset();
        },
        onError: () => console.error('Failed to Save Minor Offense'),
    });

};
</script>

    
    <Head title="Show Student" />

    <template>
    <Head title="Show Student" />

    <AuthenticatedLayout>
     <!-- message from StudentsController-->
    <div v-if="$page.props.flash.message" class="alert bg-green-200 mt-4 mx-5 px-4 py-2">
        {{ $page.props.flash.message }}
      </div>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Student</h5>
                <Link :href="route('dashboard')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
            </div>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        {{ student.lrn }},
                        {{ student.name }},
                        {{ student.sex }},
                        {{ student.grade }}
                    </div>
                </div>
            </div>

            <div class="mt-4 mx-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Minor Offense</h5>
                    <Link :href="route('students.create')" class="bg-blue-500 text-white p-3 rounded mb-4">Add Student</Link>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">Offense Committed</th>
                            <th class="py-2 px-4 text-left border">Date Committed</th>
                            <th class="py-2 px-4 text-left border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="offense in submittedminorOffenses" :key="offense.id">
                            <td class="py-2 px-4 border">{{ offense.minor_offense.minor_offenses }}</td>
                            <td class="py-2 px-4 border">{{ offense.created_at }}</td>
                            <td class="py-2 px-4 border">
                                <button 
                                @click="openModal(student.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block"
                                >
                                Delete
                            </button>
                            <CustomModal v-if="showModal" @close="showModal=false">
                                <p class="text-gray-800">
                                     Are you sure you want to delete all this Student data? This action cannot be undone.
                                </p>
                                <div class="text-right mt-4">
                                    <button @click="showModal = false" class="px-4 py-2 text-sm text-black-800 focus:outline-none hover:underline">Cancel</button>
                                    <button @click="DeleteStudent(passId)" class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-600 focus:outline-none hover:bg-red-500">Delete</button>
                                </div>
                            </CustomModal>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form @submit.prevent="saveMinorOffense()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Select Minor Offenses</label>
                            <select v-model="form.minor_offense_id" class="py-1 w-full">
                                <option value="">Select Minor Offense</option>
                                <!-- Loop through the minorOffenses prop to populate the select -->
                                <option v-for="offense in minorOffenses" :key="offense.id" :value="offense.id">
                                    {{ offense.minor_offenses }}
                                </option>
                            </select>
                            <div v-if="errors.minor_offense_id" class="text-red-500">{{ errors.minor_offense_id }}</div>
                        </div>
                        <div class="mb-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>


