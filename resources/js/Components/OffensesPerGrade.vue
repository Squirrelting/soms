<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    offensesPerGrade: Array
});
</script>

<template>
    <div class="text-center">
        <h2 class="text-lg font-semibold mb-4">Offenders</h2>
        <ul>
            <li 
                v-for="gradeData in offensesPerGrade" 
                :key="gradeData.grade_id" 
                class="mb-2 p-3 bg-white rounded-lg shadow text-base text-center"
            >
                Grade {{ gradeData.grade_id }} <br>
                <span class="font-bold text-xl text-red-500">{{ gradeData.offense_count }}</span>
            </li>
        </ul>
    </div>
</template>
