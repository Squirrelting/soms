<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    offensesPerGrade: Array
});
</script>

<template>
    <h2 class="text-sm font-semibold mb-2">Offenses per Grade</h2>
    <ul>
        <li v-for="gradeData in offensesPerGrade" :key="gradeData.grade_id" class="mb-1 p-1 bg-white rounded-lg shadow text-sm">
            Grade {{ gradeData.grade_id }} - {{ gradeData.offense_count }} Offenders
        </li>
    </ul>
</template>
