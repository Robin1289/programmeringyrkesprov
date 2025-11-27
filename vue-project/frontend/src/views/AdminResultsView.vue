<template>
  <div class="container mt-4">

    <h2 class="text-pink mb-4">📊 Användarresultat</h2>

    <AdminResults
      :results="results"
      :loading="loading"
      :error="error"
      v-if="!loading"
    />

    <div v-if="loading" class="text-center mt-5">
      <div class="spinner-border text-pink" role="status"></div>
      <p class="mt-3 text-muted">Hämtar resultat...</p>
    </div>

    <div v-if="error" class="alert alert-danger text-center mt-4">
      {{ error }}
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import AdminResults from "../components/AdminResults.vue"

const results = ref([])
const loading = ref(true)
const error = ref(null)

async function fetchResults() {
  try {
    const response = await fetch(
      "http://localhost/yrkesprov/vue-project/backend/api/admin_get_results.php",
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
