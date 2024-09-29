<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 
import Swal from 'sweetalert2'; 
import axios from 'axios';

const props = defineProps({ 
    errors: Object,
    student: Object,
    majorOffenses: Array,
    submittedmajorOffenses: Array,
 });

// Initialize the form object
const form = useForm({
    major_offense_id: '',
    lrn: props.student.lrn, 
    student_name: props.student.name,
    student_sex: props.student.sex,  
    student_grade: props.student.grade 
});

const Cleanse = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to mark this offense as acted?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cleanse it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(route('major.sanction', id))
                .then(response => {
                    Swal.fire(
                        'Updated!',
                        'The offense has been marked as acted.',
                        'success'
                    );
                    // Update the local data to reflect the change
                    const offenseIndex = props.submittedmajorOffenses.findIndex(offense => offense.id === id);
                    if (offenseIndex !== -1) {
                        props.submittedmajorOffenses[offenseIndex].sanction = 1;
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'There was a problem updating the offense.',
                        'error'
                    );
                });
        }
    });
};

 // Function to save a major offense
 const saveMajorOffense = () => {
    if(form.major_offense_id === ''){
        form.post(route('major.store'));
    } else {
        Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to save this major offense?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            // If user confirms, proceed with form submission
            form.post(route('major.store'), {
                onSuccess: () => {
                    Swal.fire(
                        'Saved!',
                        'The offense has been added successfully.',
                        'success'
                    );
                    form.reset(); // Optionally reset the form after success
                },
                onError: () => {
                    Swal.fire(
                        'Error!',
                        'There was a problem saving the offense.',
                        'error'
                    );
                },
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // If user cancels
            Swal.fire(
                'Cancelled',
                'The offense was not saved.',
                'error'
            );
        }
    });
    }
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
                <Link :href="route('students.index')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
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
                    <h5 class="m-4">Major Offense</h5>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">Offense Committed</th>
                            <th class="py-2 px-4 text-left border">Penalty</th>
                            <th class="py-2 px-4 text-left border">Date Committed</th>
                            <th class="py-2 px-4 text-left border">Sanction</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="offense in submittedmajorOffenses" :key="offense.id">
                            <td class="py-2 px-4 border">{{ offense.major_offense.major_offenses }}</td>
                            <td class="py-2 px-4 border">{{ offense.major_penalty.major_penalties }}</td>
                            <td class="py-2 px-4 border">{{ offense.formatted_date }}</td>
                            <td class="py-2 px-4 border">
                                <button
                                v-if="offense.sanction === 0"
                                @click="Cleanse(offense.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded"
                            >
                                Cleanse
                            </button>
                            <button
                                v-else
                                class="px-2 py-1 text-sm bg-green-600 text-white p-3 rounded"
                                disabled
                            >
                                Acted
                            </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form @submit.prevent="saveMajorOffense()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Select Major Offenses</label>
                            <select v-model="form.major_offense_id" class="py-1 w-full">
                                <option value="">Select Major Offense</option>
                                <!-- Loop through the majorOffenses prop to populate the select -->
                                <option v-for="offense in majorOffenses" :key="offense.id" :value="offense.id">
                                    {{ offense.major_offenses }}
                                </option>
                            </select>
                            <div v-if="errors.major_offense_id" class="text-red-500">{{ errors.major_offense_id }}</div>
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


