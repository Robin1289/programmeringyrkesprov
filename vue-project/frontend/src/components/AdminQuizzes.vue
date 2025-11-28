<template>
  <div class="admin-quizzes-container">

    <!-- Create button -->
    <div class="text-end mb-3">
      <button class="btn btn-pink" @click="openCreateModal">
        + Skapa nytt quiz
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center mt-4">
      <div class="spinner-border text-pink"></div>
      <p class="mt-3 text-muted">Hämtar quiz...</p>
    </div>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger text-center mt-3">
      {{ error }}
    </div>

    <!-- Quiz list -->
    <div class="row">
      <div
        class="col-md-6 col-lg-4 mb-4"
        v-for="quiz in quizzes"
        :key="quiz.quiz_id"
      >
        <div class="quiz-card shadow-sm">

          <h3 class="quiz-title">{{ quiz.quiz_name }}</h3>
          <p class="quiz-description text-muted">{{ quiz.quiz_description }}</p>

          <p class="quiz-meta">
            Kategori: <strong>{{ quiz.quiz_category }}</strong><br />
            Miniminivå: <strong>{{ quiz.quiz_min_level_fk }}</strong>
          </p>

          <div class="d-flex justify-content-between mt-3">

            <!-- Edit -->
            <button class="btn btn-sm btn-outline-pink" @click="editQuiz(quiz.quiz_id)">
              Redigera
            </button>

            <!-- Manage questions -->
            <router-link
              :to="`/admin-quizzes/${quiz.quiz_id}`"
              class="btn btn-sm btn-outline-secondary"
            >
              Frågor
            </router-link>

            <!-- Delete -->
            <button class="btn btn-sm btn-outline-danger" @click="deleteQuiz(quiz.quiz_id)">
              Ta bort
            </button>

          </div>

        </div>
      </div>
    </div>

    <!-- create/edit modal kommer senare -->
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import router from '@/router'

const quizzes = ref([])
const loading = ref(true)
const error = ref(null)

// fetch quiz list from backend
async function loadQuizzes() {
  try {
    const res = await fetch(
      'http://localhost/yrkesprov/vue-project/backend/api/admin_get_quizzes.php',
      { credentials: 'include' }
    )

    const data = await res.json()

    if (!data.success)
      throw new Error(data.message || "Quiz kunde inte hämtas.")

    quizzes.value = data.quizzes

  } catch (err) {
    console.error(err)
    error.value = err.message
  } finally {
    loading.value = false
  }
}

// navigate to edit view
function editQuiz(id) {
  router.push(`/admin-quizzes-edit/${id}`)
}

// delete – backend coming later
function deleteQuiz(id) {
  if (!confirm("Är du säker på att du vill ta bort detta quiz?")) return
}

// create new quiz view
function openCreateModal() {
  router.push('/admin-quizzes-create')
}

onMounted(loadQuizzes)
</script>
