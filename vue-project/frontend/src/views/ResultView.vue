<template>
  <div class="container py-5 results-page">

    <h1 class="results-title text-center mb-4">Dina resultat</h1>

    <div v-if="loading" class="text-center text-muted">
      Laddar resultat...
    </div>

    <div v-else-if="results.length === 0" class="text-center text-muted">
      Du har inte slutfört några quiz ännu.
    </div>

    <div v-else>

      <!-- Sort dropdown + button -->
      <div class="sort-bar mb-4 d-flex align-items-end gap-3">

        <div class="flex-grow-1">
          <label for="sort" class="sort-label">Sortera efter:</label>
          <select
            id="sort"
            v-model="sortBy"
            class="form-select sort-select"
          >
            <option value="date_desc">Senaste först</option>
            <option value="date_asc">Äldsta först</option>
            <option value="score_desc">Högsta poäng</option>
            <option value="score_asc">Lägsta poäng</option>
            <option value="name_asc">Namn A-Ö</option>
            <option value="name_desc">Namn Ö-A</option>
          </select>
        </div>

        <button class="btn btn-primary kitty-btn" @click="applySorting">
          Uppdatera sortering
        </button>

      </div>

      <!-- Results table -->
      <div class="table-responsive">
        <table class="table kitty-table">
          <thead>
            <tr>
              <th>Quiz</th>
              <th>Poäng</th>
              <th>Datum</th>
              <th></th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="r in sortedResults" :key="r.result_id">
              <td>{{ r.quiz_name }}</td>
              <td>{{ r.score }} / {{ r.max_score }}</td>
              <td>{{ new Date(r.completed_at).toLocaleDateString() }}</td>
              <td>
                <router-link
                  :to="`/results/${r.result_id}`"
                  class="kitty-btn-small"
                >
                  Visa
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"

const results = ref([])
const sortedResults = ref([])
const loading = ref(true)
const sortBy = ref("date_desc")

async function loadResults() {
  try {
    const res = await fetch(
      "http://localhost/yrkesprov/vue-project/backend/api/user_results.php",
      { credentials: "include" }
    )

    const data = await res.json()
    if (data.success) {
      results.value = data.results || []
      sortedResults.value = [...results.value] // initial render
    }

  } catch (err) {
    console.error("Failed loading results:", err)
  }

  loading.value = false
}

function applySorting() {
  const list = [...results.value]

  switch (sortBy.value) {
    case "date_desc":
      list.sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at))
      break

    case "date_asc":
      list.sort((a, b) => new Date(a.completed_at) - new Date(b.completed_at))
      break

    case "score_desc":
      list.sort((a, b) => b.score - a.score)
      break

    case "score_asc":
      list.sort((a, b) => a.score - b.score)
      break

    case "name_asc":
      list.sort((a, b) => a.quiz_name.localeCompare(b.quiz_name))
      break

    case "name_desc":
      list.sort((a, b) => b.quiz_name.localeCompare(a.quiz_name))
      break
  }

  sortedResults.value = list
}

onMounted(loadResults)
</script>
