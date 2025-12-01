<template>
  <div class="admin-container">

    <h1 class="page-title">Skapa nytt quiz</h1>

    <QuizForm title="Nytt quiz" @save="createQuiz" />

  </div>
</template>

<script setup>
import QuizForm from "../components/QuizForm.vue"
import router from "@/router"

async function createQuiz(quiz) {

  const res = await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=create",
    {
      method: "POST",
      credentials: "include",
      body: JSON.stringify(quiz)
    }
  )

  const data = await res.json()

  if (!data.success) {
    alert(data.message)
    return
  }

  router.push(`/admin-quizzes/${data.quiz_id}`)
}
</script>
