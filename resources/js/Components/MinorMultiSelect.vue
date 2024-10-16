<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { ChevronDownIcon, ChevronUpIcon, XCircleIcon } from "@heroicons/vue/24/outline";

const props = defineProps<{
    options: Array<{
        id: number;
        minor_offenses: string;
    }>;
    form: any;
}>();

const collapsed = ref(false);
const searchQuery = ref("");
const selectedId = ref<Object[]>([]);

const openDropdown = () => {
    collapsed.value = true
}
const closeDropdown = () => {
    collapsed.value = false
}

const handleDropDown = () => {
    openDropdown()
}

const handleBlur = () => {
    setTimeout(() => {
        closeDropdown()
    }, 100)
}

const filteredOptions = computed(() => {
    return props.options?.filter((option) => {
        return option.minor_offenses
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase());
    });
});

const handleChange = (option: { id: number; minor_offenses: string }) => {
    props.form.minor_offenses.push(option);
    selectedId.value.push(option.id);
    const index = props.options?.findIndex((opt) => opt.id === option.id);
    if (index !== -1) {
        props.options.splice(index, 1);
    }
};

const removeItem = (index: number, item: { id: number; minor_offenses: string }) => {
    props.form.minor_offenses.splice(index, 1);
    props.options.push(item);
};

onMounted(() => {
    for (let i = 0; i < props.form.minor_offenses.length; i++) {
        const option = props.form.minor_offenses[i];
        const index = props.options?.findIndex((opt) => opt.id === option.id);
        if (index !== -1) {
            props.options.splice(index, 1);
        }
    }
});
</script>

<template>
    <div class="w-full flex flex-col items-center bg-white shadow-md p-4 rounded-lg">
        <label class="block text-gray-700 font-semibold mb-1">Add Minor Offenses</label>

        <div class="w-full">
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left border-b border-gray-200">Name</th>
                            <th class="p-3 text-left border-b border-gray-200">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in props.form.minor_offenses" :key="index" class="hover:bg-gray-50">
                            <td class="p-3 border-b border-gray-200">{{ item.minor_offenses }}</td>
                            <td class="p-3 border-b border-gray-200">
                                <button type="button" @click="removeItem(index, item)" class="text-red-500 hover:text-red-700">
                                    <XCircleIcon class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="relative flex items-center mt-4">
                <input
                @focus="handleDropDown()"
                    @blur="handleBlur"
                    v-model="searchQuery"
                    type="text"
                    class="input w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                    placeholder="Search for offenses..."
                />
                <button type="button" @click="collapsed = !collapsed" class="absolute right-2 text-gray-600 hover:text-teal-500">
                    <ChevronDownIcon v-if="!collapsed" class="w-6 h-6" />
                    <ChevronUpIcon v-if="collapsed" class="w-6 h-6" />
                </button>
            </div>

            <div v-if="collapsed" class="mt-2 bg-white border border-gray-200 shadow-md rounded-lg overflow-auto max-h-60">
                <div v-for="(option, index) in filteredOptions" :key="index" class="p-2 hover:bg-teal-100 cursor-pointer border-b border-gray-100" @click="handleChange(option)">
                    {{ option.minor_offenses }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional Styling for Better UI */
.table-auto {
    width: 100%;
    margin: 0;
    border-spacing: 0;
    border-collapse: collapse;
}
th, td {
    padding: 0.75rem;
}
.input {
    transition: all 0.2s ease;
}
</style>
