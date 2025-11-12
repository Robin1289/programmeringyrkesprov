<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()
const levels = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    const response = await fetch('http://localhost/yrkesprov/vue-project/backend/api/levels.php', {
      credentials: 'include'
    })
    const data = await response.json()

    if (data.success && data.levels) {
      levels.value = data.levels
    } else {
      error.value = data.message || 'Failed to fetch levels'
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
})

const currentLevel = computed(() => {
  if (!levels.value.length) return null
  return levels.value.find(l => l.l_id === userStore.level)
})

const nextLevel = computed(() => {
  if (!levels.value.length) return null
  return levels.value.find(l => l.l_id === userStore.level + 1)
})
</script>

<template>
  <div class="dashboard-container">
    <div v-if="loading">Loading...</div>
    <div v-else-if="error">{{ error }}</div>
    <div v-else>
      <div v-if="currentLevel">
        <h2>Current Level: {{ currentLevel.l_name }} ({{ userStore.points }} / {{ currentLevel.l_min_points }})</h2>
      </div>
      <div v-if="nextLevel">
        <h3>Next Level: {{ nextLevel.l_name }} ({{ nextLevel.l_min_points }} points)</h3>
        <div class="progress">
          <div
            class="progress-bar"
            :style="{ width: Math.min((userStore.points / nextLevel.l_min_points) * 100, 100) + '%' }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>
