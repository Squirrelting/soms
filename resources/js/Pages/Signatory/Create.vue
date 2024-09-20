<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

defineProps({ 
    errors: Object 
});

const form = useForm({
    name: '',
    position:''
});

const saveSignatory = () => {
    const res = form.post(route('signatory.store'), {
        onSuccess: () => {
            alert('success');
        },
        onError: () => console.error('Failed to Save signatory'),
    });

    if(res){
        form.reset();
    }
};
</script>

<template>
    
    <Head title="Input Signatory" />

    <AuthenticatedLayout>
      
        <div class="mt-4 mx-4">
        <div class="flex justify-between">
            <h5 class="m-4">Input Signatory</h5>
            <Link :href="route('signatorypage')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
        </div>

        <form @submit.prevent="saveSignatory()">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        <label>Signatory</label>
                        <input type="text" v-model="form.name" class="py-1 w-full" />
                        <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                    </div>

                    <div class="mb-3">
                        <label>Position</label>
                        <input type="text" v-model="form.position" class="py-1 w-full" />
                        <div v-if="errors.position" class="text-red-500">{{ errors.position }}</div>
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

