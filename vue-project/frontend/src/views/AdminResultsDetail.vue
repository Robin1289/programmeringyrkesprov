<template>
  <div class="result-page-container">
    <div v-if="loading" class="text-center mt-5">
      <strong>Laddar...</strong>
    </div>

    <div v-else-if="error" class="alert alert-danger text-center">
      {{ error }}
    </div>

    <div v-else class="result-details-card">

      <!-- Summary -->
      <h2 class="mb-3 text-center">Resultatdetaljer</h2>

      <table class="table result-summary-table">
        <tbody>
          <tr><td><strong>Elev:</strong></td><td>{{ details.student_name }}</td></tr>
          <tr><td><strong>E-post:</strong></td><td>{{ details.student_email }}</td></tr>
          <tr><td><strong>Quiz:</strong></td><td>{{ details.quiz_name }}</td></tr>
          <tr><td><strong>Kategori:</strong></td><td>{{ details.quiz_category }}</td></tr>
          <tr><td><strong>Datum:</strong></td><td>{{ formatDate(details.sq_date) }}</td></tr>
          <tr><td><strong>Poäng:</strong></td><td>{{ details.sq_correct }} / {{ details.sq_total }}</td></tr>
          <tr><td><strong>Score:</strong></td><td>{{ details.sq_score }}</td></tr>
          <tr>
            <td><strong>Godkänd:</strong></td>
            <td>
              <span v-if="details.sq_passed" class="correct-badge">Ja ✓</span>
              <span v-else class="incorrect-badge">Nej ✗</span>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Answers table -->
      <h4 class="mt-4 mb-3">Svar per fråga</h4>

      <table class="table result-answer-table">
        <thead>
          <tr>
            <th>Fråga</th>
            <th>Ditt svar</th>
            <th>Korrekt svar</th>
            <th>Rätt?</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="a in details.answers" :key="a.q_id">
            <td>{{ a.q_name }}</td>
            <td>{{ formatAnswer(a.student_answer) }}</td>
            <td>{{ a.correct_answer }}</td>
            <td>
              <span v-if="a.correct" class="correct-badge">✓</span>
              <span v-else class="incorrect-badge">✗</span>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Delete button -->
      <div class="text-center mt-4">
        <button class="delete-result-btn" @click="deleteResult">
          Ta bort detta resultat
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const error = ref(null);
const details = ref(null);

function formatDate(date) {
  return new Date(date).toLocaleString("sv-FI");
}

// Student answers are sometimes JSON → we need to clean it
function formatAnswer(value) {
  try {
    const parsed = JSON.parse(value);
    if (parsed.text) return parsed.text;
    if (parsed.answer_ids) return parsed.answer_ids.join(", ");
    if (Array.isArray(parsed.order)) return parsed.order.join(" → ");
    if (parsed.matches) return JSON.stringify(parsed.matches);
  } catch {}
  return value;
}

async function fetchDetails() {
  const id = route.params.id;

  try {
    const res = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/admin_get_result_details.php?id=${id}`,
      { credentials: "include" }
    );

    const data = await res.json();

    if (!data.success) {
      error.value = data.message || "Kunde inte hämta detaljer.";
      return;
    }

    details.value = data.details;
  } catch (e) {
    error.value = "Serverfel.";
  } finally {
    loading.value = false;
  }
}

async function deleteResult() {
  if (!confirm("Är du säker på att du vill ta bort detta resultat?")) return;

  const id = route.params.id;

  const res = await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/admin_delete_result.php",
    {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    }
  );

  const data = await res.json();

  if (data.success) {
    alert("Resultatet har raderats.");
    router.push("/admin-results");
  } else {
    alert("Kunde inte ta bort resultatet.");
  }
}

onMounted(fetchDetails);
</script>
