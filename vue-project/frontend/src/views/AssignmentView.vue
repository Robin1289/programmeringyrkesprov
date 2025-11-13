<template>
  <div class="dashboard-container">

    <!-- Header -->
    <h1 class="assignments-header">Assignments</h1>

    <!-- Loading -->
    <div v-if="loading" class="text-center assignments-loading">
      Loading assignments...
    </div>

    <!-- No assignments -->
    <div v-else-if="assignments.length === 0" class="text-center assignments-empty">
      No assignments available for your level.
    </div>

    <!-- Assignment Cards Grid -->
    <div class="row assignments-grid">
      <div
        class="col-md-6 col-lg-4 mb-3"
        v-for="quiz in assignments"
        :key="quiz.quiz_id"
      >
        <AssignmentCard :quiz="quiz" />
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '../store/userstore.js'
import AssignmentCard from '../components/AssignmentCard.vue'

const userStore = useUserStore()
const assignments = ref([])
const loading = ref(true)

async function fetchAssignments() {
  try {
    const response = await fetch(
      'http://localhost/yrkesprov/vue-project/backend/api/assignment.php',
      { credentials: 'include' }
    )
    const data = await response.json()

    if (data.success && Array.isArray(data.quizzes)) {
      assignments.value = data.quizzes
    } else {
      assignments.value = []
    }
  } catch (err) {
    console.error(err)
    assignments.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchAssignments()
})
</script>
