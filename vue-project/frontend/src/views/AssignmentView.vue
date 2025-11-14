<template>
  <div class="dashboard-container">

    <!-- Header -->
    <div class="assignments-header-wrapper">
      <h1 class="assignments-header">Assignments</h1>
      <p class="assignments-subtitle">
        Your current level: <span class="user-level">{{ realLevel }}</span>
      </p>
    </div>

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
        <AssignmentCard :quiz="quiz" :user-level="realLevel" />
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
const realLevel = ref(1)

async function fetchAssignments() {
  try {
    const response = await fetch(
      'http://localhost/yrkesprov/vue-project/backend/api/assignment.php',
      { credentials: 'include' }
    )

    const data = await response.json()

    if (data.success) {
      // Real 1–10 level from backend
      realLevel.value = data.user_real_level || 1

      // Sort quizzes so recommended appear first
      assignments.value = data.quizzes.sort((a, b) => {
        const diffA = Math.abs(a.quiz_min_level_fk - realLevel.value)
        const diffB = Math.abs(b.quiz_min_level_fk - realLevel.value)
        return diffA - diffB
      })
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

onMounted(fetchAssignments)
</script>
