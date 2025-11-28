<template>
  <div>
    <!-- Controls: search + sort -->
    <div class="row g-3 mb-3 align-items-end admin-results-controls">

      <!-- Search by user -->
      <div class="col-md-4">
        <label class="form-label fw-semibold">Sök elev</label>
        <input
          v-model="searchUser"
          type="text"
          class="form-control"
          placeholder="Namn eller e-post"
        />
      </div>

      <!-- Sort select -->
      <div class="col-md-4">
        <label class="form-label fw-semibold">Sortera efter</label>
        <select v-model="selectedSort" class="form-select">
          <option value="">Ingen sortering</option>
          <option value="latest">Senaste först</option>
          <option value="oldest">Äldsta först</option>
          <option value="best">Högsta poäng först</option>
          <option value="worst">Lägsta poäng först</option>
          <option value="studentAZ">Elev (A–Ö)</option>
          <option value="studentZA">Elev (Ö–A)</option>
          <option value="quizAZ">Prov (A–Ö)</option>
          <option value="quizZA">Prov (Ö–A)</option>
        </select>
      </div>

      <!-- Sort button -->
      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-pink" @click="applySort">Sortera</button>
        <button class="btn btn-gray" @click="resetSort">Återställ</button>
      </div>

    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover results-table">
        <thead>
          <tr>
            <th>Elev</th>
            <th>E-post</th>
            <th>Prov</th>
            <th>Rätt / Totalt</th>
            <th>Status</th>
            <th>Datum</th>
            <th>Mer</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="r in processedResults" :key="r.sq_id">
            <td class="fw-semibold">{{ r.student_name }}</td>
            <td>{{ r.student_email }}</td>
            <td>{{ r.quiz_name }}</td>
            <td>{{ r.sq_correct }} / {{ r.sq_total }}</td>

            <td>
              <span
                :class="[
                  'badge rounded-pill',
                  r.sq_passed == 1 ? 'bg-success' : 'bg-danger'
                ]"
              >
                {{ r.sq_passed == 1 ? "Godkänd" : "Underkänd" }}
              </span>
            </td>

            <td>{{ formatDate(r.sq_date) }}</td>

            <td>
                <router-link
                  :to="`/admin-results/${r.sq_id}`"
                  class="btn btn-sm btn-outline-pink"
                >
                  Visa
                </router-link>
              </td>
          </tr>

          <tr v-if="processedResults.length === 0">
            <td colspan="8" class="text-center text-muted py-4">
              Inga resultat matchar din filtrering…
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from "vue"

const props = defineProps({
  results: {
    type: Array,
    default: () => []
  },
  loading: Boolean,
  error: String
})

// search term for user (name or email)
const searchUser = ref("")

// sort state
const selectedSort = ref("")
const currentSort = ref("") // what is actually applied

function applySort() {
  currentSort.value = selectedSort.value
}

function resetSort() {
  selectedSort.value = ""
  currentSort.value = ""
  searchUser.value = ""
}

const processedResults = computed(() => {
  if (!props.results) return [];

  let data = [...props.results];

  // --- FIXED SEARCH ---
  if (searchUser.value.trim() !== "") {
    const term = searchUser.value.toLowerCase();
    data = data.filter(r => {
      const n = r.student_name ? r.student_name.toLowerCase() : "";
      const e = r.student_email ? r.student_email.toLowerCase() : "";
      return n.includes(term) || e.includes(term);
    });
  }

  // --- SORTING ---
  switch (currentSort.value) {
    case "latest":
      data.sort((a, b) => new Date(b.sq_date) - new Date(a.sq_date));
      break;
    case "oldest":
      data.sort((a, b) => new Date(a.sq_date) - new Date(b.sq_date));
      break;
    case "best":
      data.sort((a, b) => b.sq_score - a.sq_score);
      break;
    case "worst":
      data.sort((a, b) => a.sq_score - b.sq_score);
      break;
    case "studentAZ":
      data.sort((a, b) =>
        (a.student_name || "").localeCompare(b.student_name || "")
      );
      break;
    case "studentZA":
      data.sort((a, b) =>
        (b.student_name || "").localeCompare(a.student_name || "")
      );
      break;
    case "quizAZ":
      data.sort((a, b) =>
        (a.quiz_name || "").localeCompare(b.quiz_name || "")
      );
      break;
    case "quizZA":
      data.sort((a, b) =>
        (b.quiz_name || "").localeCompare(a.quiz_name || "")
      );
      break;
  }

  return data;
});


function formatDate(d) {
  if (!d) return ""
  return new Date(d).toLocaleString("fi-FI")
}

function openDetails(resultItem) {
  alert("Details page coming soon! (sq_id = " + resultItem.sq_id + ")")
}
</script>
