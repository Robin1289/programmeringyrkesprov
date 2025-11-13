<template>
  <div class="dashboard-card p-4 shadow-sm rounded-2 mb-3">
    <h3 class="mb-2">{{ quiz.quiz_name }}</h3>

    <!-- Automatically generated teaser -->
    <p class="text-muted mb-3">{{ teaser }}</p>

    <router-link :to="`/quiz/${quiz.quiz_id}`" class="btn btn-primary w-100">
      Start Quiz
    </router-link>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Correctly get props
const props = defineProps({
  quiz: {
    type: Object,
    required: true
  }
})

// Generate teaser from first part of description
const teaser = computed(() => {
  if (!props.quiz.quiz_description) return ''
  
  // Take first 150 characters
  let text = props.quiz.quiz_description.slice(0, 150)
  
  // Ensure it doesn't cut mid-word
  const lastSpace = text.lastIndexOf(' ')
  text = text.slice(0, lastSpace)

  // Add ellipsis if the description is longer
  if (props.quiz.quiz_description.length > 150) text += '...'
  
  return text
})
</script>

<style>
.dashboard-card {
  background-color: #fff;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.dashboard-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}
</style>
