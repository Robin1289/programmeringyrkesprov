<template>
  <div class="result-card p-4 hello-kitty-card">
    <h3 class="result-card-title">{{ result.quiz_name }}</h3>

    <p class="result-card-score">
      {{ result.score }} / {{ result.max_score }} poäng
    </p>

    <p class="result-card-date">
      {{ new Date(result.completed_at).toLocaleDateString() }}
    </p>

    <router-link
      :to="`/results/${result.result_id}`"
      class="kitty-btn-small mt-3"
    >
      Visa resultat
    </router-link>
  </div>
</template>

<script setup>
defineProps({
  result: Object
})
</script>
