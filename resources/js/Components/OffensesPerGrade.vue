<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    offendersPerGrade: Array
});
</script>

<template>
    <div class="text-left">
      <ul>
        <li
          v-for="gradeData in offendersPerGrade"
          :key="gradeData.grade_id"
          class="mb-2 p-2 bg-white rounded-lg shadow-md text-sm"
        >
          <!-- Grade is centered -->
          <div class="text-center mb-1">
            <strong class="text-lg">Grade {{ gradeData.grade_id }}</strong>
          </div>
          <!-- Rest of the details aligned left -->
          <div class="ml-2">
            <span class="block">Offenders: <span class="font-semibold">{{ gradeData.offenders }}</span></span>
            <span class="block">Unresolved: <span class="font-semibold">{{ gradeData.unresolved }}</span></span>
            <span class="block">Resolved: <span class="font-semibold">{{ gradeData.resolved }}</span></span>
            <span class="block">Offenses: <span class="font-semibold">{{ gradeData.offenses }}</span></span>
          </div>
        </li>
      </ul>
    </div>
  </template>
  