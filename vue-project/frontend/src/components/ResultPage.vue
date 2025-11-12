<template>
  <div class="dashboard-container mt-5">
    <h1 class="dashboard-title mb-4">Your Results</h1>

    <!-- Show a friendly message if no results -->
    <div v-if="!results || results.length === 0" class="p-4 bg-pink-100 rounded">
      No results found yet. Complete some quizzes to see your results here!
    </div>

    <!-- List of results -->
    <div v-else class="row g-4">
      <div class="col-md-6" v-for="result in sortedResults" :key="result.r_id">
        <ResultCard :result="result" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ResultCard from '../components/ResultCard.vue'
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()
const results = ref([])

// Sort results by most recent first
const sortedResults = computed(() => {
  return results.value.slice().sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at))
})

async function fetchResults() {
  try {
    const res = await fetch('http://localhost/yrkesprov/vue-project/backend/api/results.php', {
      credentials: 'include'
    })
    const data = await res.json()
    if (data.success) {
      results.value = data.results || []
    } else {
      console.error('Failed to fetch results:', data.message)
      results.value = []
    }
  } catch (err) {
    console.error('Error fetching results:', err)
    results.value = []
  }
}

onMounted(() => {
  fetchResults()
})
</script>
