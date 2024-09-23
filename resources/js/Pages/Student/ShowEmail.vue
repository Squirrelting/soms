<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    errors: Object,
    student: Object,
    submittedminorOffenses: Array,
    submittedmajorOffenses: Array,
});

const studentId = ref("");
const getId = (id) => {
    studentId.value = id;
};

const SendEmail = (id) => {
    router.get(route("send.email", id), {
        onSuccess: () => {
            alert("success");
        },
        onError: () => console.error("Failed to send email"),
    });
};
</script>

<template>
    <Head title="Show Student" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Student</h5>
                <Link
                    :href="route('dashboard')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                >
                    Back
                </Link>
            </div>

            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        {{ student.lrn }}, {{ student.name }},
                        {{ student.sex }},
                        {{ student.grade }}
                    </div>
                </div>
            </div>

            <!-- Conditionally display Minor Offense table -->
            <div v-if="submittedminorOffenses.length" class="mt-4 mx-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Minor Offense</h5>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">
                                Offense Committed
                            </th>
                            <th class="py-2 px-4 text-left border">Penalty</th>
                            <th class="py-2 px-4 text-left border">
                                Date Committed
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="offense in submittedminorOffenses"
                            :key="offense.id"
                        >
                            <td class="py-2 px-4 border">
                                {{ offense.minor_offense.minor_offenses }}
                            </td>
                            <td class="py-2 px-4 border">
                                {{ offense.minor_penalty.minor_penalties }}
                            </td>
                            <td class="py-2 px-4 border">
                                {{ offense.created_at }}
                            </td>
                            <td class="py-2 px-4 border"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Conditionally display Major Offense table -->
            <div v-if="submittedmajorOffenses.length" class="mt-4 mx-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Major Offense</h5>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">
                                Offense Committed
                            </th>
                            <th class="py-2 px-4 text-left border">Penalty</th>
                            <th class="py-2 px-4 text-left border">
                                Date Committed
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="offense in submittedmajorOffenses"
                            :key="offense.id"
                        >
                            <td class="py-2 px-4 border">
                                {{ offense.major_offense.major_offenses }}
                            </td>
                            <td class="py-2 px-4 border">
                                {{ offense.major_penalty.major_penalties }}
                            </td>
                            <td class="py-2 px-4 border">
                                {{ offense.created_at }}
                            </td>
                            <td class="py-2 px-4 border"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button
                @click="getId(student.id)"
                onclick="my_modal_1.showModal()"
                class="px-2 py-1 text-sm bg-blue-600 text-white p-3 rounded me-2 inline-block"
            >
                Send Email
            </button>
        </div>

        <dialog id="my_modal_1" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Hello!</h3>
                <p class="py-4">
                    Are you sure you want to send this data to parents Email?.
                </p>
                <div class="modal-action">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn">Cancel</button>
                        <button
                            @click="SendEmail(studentId)"
                            class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-600 focus:outline-none hover:bg-red-500"
                        >
                            Send Email
                        </button>
                    </form>
                </div>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
