<template>
  <div class="container admin-page-wrapper mt-4">

    <!-- Page Title -->
    <h2 class="text-pink mb-4 text-center page-title">
      Hantera quiz
    </h2>

    <!-- Admin panel -->
    <div class="admin-panel">

      <!-- Top bar -->
      <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-pink" @click="openCreate">
          + Skapa nytt quiz
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center mt-4">
        <div class="spinner-border text-pink"></div>
        <p class="mt-3 text-muted">Hämtar quiz...</p>
      </div>

      <!-- Error -->
      <div v-if="error" class="alert alert-danger text-center">
        {{ error }}
      </div>

      <!-- Table -->
      <div v-if="!loading" class="table-responsive">

        <table class="table table-hover admin-table">

          <thead>
            <tr>
              <th>ID</th>
              <th>Namn</th>
              <th>Kategori</th>
              <th>Miniminivå</th>
              <th>Frågor</th>
              <th>Redigera</th>
              <th class="text-end">Ta bort</th>
            </tr>
          </thead>

          <tbody>

            <tr v-for="quiz in quizzes" :key="quiz.quiz_id">

              <td>{{ quiz.quiz_id }}</td>
              <td class="fw-semibold">{{ quiz.quiz_name }}</td>
              <td>{{ quiz.quiz_category }}</td>
              <td>{{ quiz.quiz_min_level_fk }}</td>
              <td>{{ quiz.question_count }}</td>
              <td>
                <button
                  class="btn btn-sm btn-outline-pink me-1"
                  @click="editQuiz(quiz.quiz_id)"
                >
                  Redigera
                </button>
              </td>
                <td class="text-end">
                <button
                  class="btn btn-sm btn-outline-danger"
                  @click="deleteQuiz(quiz.quiz_id)"
                >
                  Ta bort
                </button>
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
import router from "@/router"

const quizzes = ref([])
const loading = ref(true)
const error = ref(null)

async function loadQuizzes() {
  try {
    const res = await fetch(
      "http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=list",
      { credentials: "include" }
    )

    const data = await res.json()

    if (!data.success)
      throw new Error(data.message || "Kunde inte hämta quiz.")

    quizzes.value = data.quizzes

  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function openCreate() {
  router.push("/admin-quizzes/create")
}

function editQuiz(id) {
  router.push(`/admin-quizzes/edit/${id}`)
}

function deleteQuiz(id) {
  if (!confirm("Är du säker på att du vill ta bort detta quiz?")) return

  fetch(
    `http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=delete&quiz_id=${id}`,
    {
      method: "POST",
      credentials: "include"
    }
  ).then(loadQuizzes)
}

onMounted(loadQuizzes)
</script>
