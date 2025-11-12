<template>
  <div class="dashboard-card p-4" v-if="result">
    <h3>{{ result.quiz_name || 'Unknown Quiz' }}</h3>
    <p>Score: {{ result.score ?? 0 }} / {{ result.max_score ?? 0 }}</p>
    <p>Completed on: {{ result.completed_at ? new Date(result.completed_at).toLocaleDateString() : 'Unknown' }}</p>
  </div>
</template>

<script setup>
defineProps({
  result: {
    type: Object,
    required: false, // Make it optional to avoid warnings
    default: null
  }
})
</script>
