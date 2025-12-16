<template>
  <div class="container mt-4">

    <!-- Page Title -->
    <h1 class="admin-page-title mb-4">📊 Användarresultat</h1>

    <!-- Results List -->
    <div v-if="!loading" class="admin-card mt-3">
      <AdminResults
        :results="results"
        :loading="loading"
        :error="error"
      />
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center mt-5">
      <div class="spinner-border text-pink" role="status"></div>
      <p class="mt-3 text-muted">Hämtar resultat...</p>
    </div>

    <!-- Error State -->
    <div v-if="error" class="alert alert-danger text-center mt-4">
      {{ error }}
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRoute } from 'vue-router'
import AdminResults from "../components/AdminResults.vue"

const route = useRoute()
const results = ref([])
const loading = ref(true)
const error = ref(null)
const userId = route.params.id

async function fetchResults() {
  try {
    const response = await fetch(
      `https://yrkesprov.fwh.is/backend/api/admin_data.php?action=get-results`,
      { credentials: "include" }
    )

    const data = await response.json()

    if (!data.success) {
      throw new Error(data.message || "Okänt fel")
    }

    results.value = data.results
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchResults()
})
</script>
