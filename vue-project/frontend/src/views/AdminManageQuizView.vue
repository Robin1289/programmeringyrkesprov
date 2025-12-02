<template>
  <div class="admin-container">

    <h1 class="page-title">Redigera quiz</h1>

    <!-- Loading -->
    <p v-if="loading" class="text-center">Hämtar quiz...</p>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger text-center">
      {{ error }}
    </div>

    <!-- Quiz settings -->
    <QuizForm
      v-if="quiz"
      :quiz="quiz"
      title="Quiz-inställningar"
      @save="saveQuiz"
    />

    <!-- Questions -->
    <QuestionEditor
      v-if="!loading && quiz"
      :questions="questions"
      @new-question="addQuestion"
      @delete-question="deleteQuestion"
      @save-question="saveQuestion"
    />

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRoute } from "vue-router"

import QuizForm from "../components/QuizForm.vue"
import QuestionEditor from "../components/QuestionEditor.vue"

const route = useRoute()
const quizId = route.params.id

const quiz = ref(null)
const questions = ref([])
const loading = ref(true)
const error = ref(null)



/* ---------------------------
   LOAD QUIZ + QUESTIONS
-----------------------------*/
async function loadQuiz() {

  try {

    const res = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=get&quiz_id=${quizId}`,
      { credentials: "include" }
    )

    const data = await res.json()

    if (!data.success)
      throw new Error(data.message || "Kunde inte hämta quiz.")

    quiz.value = data.quiz
    questions.value = data.questions

  } catch (err) {
    console.error(err)
    error.value = err.message

  } finally {
    loading.value = false
  }

}



/* ---------------------------
   SAVE QUIZ SETTINGS
-----------------------------*/
async function saveQuiz(updated) {

  await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=update",
    {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        ...updated,
        quiz_id: quizId
      })
    }
  )

}



/* ---------------------------
   ADD DRAFT QUESTION
-----------------------------*/
function addQuestion() {

  questions.value.push({
    q_id: null,
    q_name: "",
    q_type: "text",
    q_points: 1,
    q_correct_text: "",
    answers: []
  })

}



/* ---------------------------
   SAVE QUESTION + ANSWERS
-----------------------------*/
async function saveQuestion(question) {

  const action = question.q_id ? "update_question" : "create_question"

  const res = await fetch(
    `http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=${action}`,
    {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(question)
    }
  )

  const data = await res.json()

  if (!data.success)
    throw new Error("Kunde inte spara frågan")

  if (!question.q_id)
    question.q_id = data.q_id


  /* Save multiple choice answers */
/* Save multiple & sort answers */
if (question.q_type === "multiple" || question.q_type === "sort") {

  await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=save_answers",
    {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        q_id: question.q_id,
        answers: question.answers
      })
    }
  );
}

}



/* ---------------------------
   DELETE QUESTION
-----------------------------*/
async function deleteQuestion(questionId) {

  const ok = confirm("Ta bort denna fråga?")
  if (!ok) return

  await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/admin_quizzes.php?action=delete_question&q_id=" + questionId,
    {
      method: "POST",
      credentials: "include"
    }
  )

  questions.value = questions.value.filter(q => q.q_id !== questionId)

}



onMounted(loadQuiz)
</script>
