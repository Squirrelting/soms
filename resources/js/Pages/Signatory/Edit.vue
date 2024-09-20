<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; 

const props = defineProps({ 
    errors: Object,
    signatory: Object,
 });

const form = useForm({
    name: props.signatory.name,
    position:props.signatory.position,
});

const updateSignatory = () => {
    
    const res = form.put(route('signatory.update', props.signatory.id));
    if(res){
        form.reset();
    }
};
</script>

<template>
    
    <Head title="Edit Signatory" />

    <AuthenticatedLayout>

        <div v-if="$page.props.flash.message" class="alert">
        {{ $page.props.flash.message }}
      </div>
      
        <div class="mt-4 mx-4">
        <div class="flex justify-between">
            <h5 class="m-4">Edit Signatory</h5>
            <Link :href="route('signatorypage')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
        </div>

        <form @submit.prevent="updateSignatory()">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        <label>Signatory Name</label>
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

