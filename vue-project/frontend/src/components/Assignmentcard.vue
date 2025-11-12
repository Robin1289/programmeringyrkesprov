<template>
  <div class="dashboard-card p-3">
    <h3>{{ quiz.quiz_name }}</h3>
    <p>{{ quiz.quiz_description }}</p>
    <router-link :to="`/quiz/${quiz.quiz_id}`" class="btn btn-primary w-100">
      Start Quiz
    </router-link>
  </div>
</template>

<script setup>
defineProps({
  quiz: {
    type: Object,
    required: true
  }
})
</script>
