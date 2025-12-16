<template>
  <div class="admin-container">

    <h1 class="page-title">Skapa nytt quiz</h1>

    <BackButton :to="`/admin-quizzes7`" />

    <QuizForm title="Nytt quiz" @save="createQuiz" />

  </div>
</template>

<script setup>
import BackButton from "@/components/BackButton.vue"
import QuizForm from "../components/QuizForm.vue"
import router from "@/router"

async function createQuiz(quiz) {

  const res = await fetch(
    "https://yrkesprov.fwh.is/backend/api/admin_quizzes.php?action=create",
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

  router.push(`/admin-quizzes/edit/${data.quiz_id}`)
}
</script>
