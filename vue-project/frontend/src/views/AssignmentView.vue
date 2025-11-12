<template>
  <div class="dashboard-container">
    <h1 class="dashboard-title mb-4">Assignments</h1>

    <div v-if="loading" class="text-center">Loading assignments...</div>
    <div v-else-if="assignments.length === 0" class="text-center">No assignments available for your level.</div>
    
    <div class="row">
      <div class="col-md-6 col-lg-4 mb-3" v-for="quiz in assignments" :key="quiz.quiz_id">
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
    const response = await fetch('http://localhost/yrkesprov/vue-project/backend/api/assignment.php', {
      credentials: 'include'
    })
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
