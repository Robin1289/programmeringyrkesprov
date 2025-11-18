<template>
  <div class="container py-5 quiz-view">

    <!-- Loading -->
    <div v-if="loading" class="text-center my-5">
      <h3>Laddar quiz...</h3>
    </div>

    <!-- Quiz Not Found -->
    <div v-else-if="!quiz">
      <h3 class="text-center text-danger">Quiz hittades inte.</h3>
    </div>

    <!-- Quiz Intro Screen -->
    <div v-else-if="!started">
      <div class="hello-kitty-card p-4">
        <h1 class="mb-4 text-center">{{ quiz.quiz_name }}</h1>

        <p class="quiz-description">{{ quiz.quiz_description }}</p>

        <div class="text-center mt-4">
          <button class="btn btn-lg btn-primary kitty-btn" @click="startQuiz">
            Starta quiz 💖
          </button>
        </div>
      </div>
    </div>

    <!-- Quiz Questions -->
    <div v-else>

      <!-- Guard to protect against undefined questions -->
      <div v-if="questions && questions.length > 0">

        <h2 class="mb-4">
          Fråga {{ currentIndex + 1 }} / {{ questions.length }}
        </h2>

        <QuestionForm
          v-if="questions[currentIndex]"
          :question="questions[currentIndex]"
          @answer="handleAnswer"
        />

        <div class="mt-4 d-flex justify-content-between">
          <button
            class="btn btn-secondary"
            :disabled="currentIndex === 0"
            @click="prevQuestion"
          >
            Föregående
          </button>

          <button
            class="btn btn-primary kitty-btn"
            @click="nextQuestion"
            v-if="currentIndex < questions.length - 1"
          >
            Nästa
          </button>

          <button
            class="btn btn-success kitty-btn"
            v-else
            @click="finishQuiz"
          >
            Slutför quiz 🎀
          </button>
        </div>

      </div>

      <!-- If no questions for some reason -->
      <div v-else class="text-center">
        <h3 class="text-danger">Inga frågor hittades i detta quiz.</h3>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import QuestionForm from "../components/QuestionForm.vue";

const route = useRoute();
const router = useRouter();

const quiz = ref(null);
const questions = ref([]);
const loading = ref(true);
const started = ref(false);

const currentIndex = ref(0);

// Fetch quiz + questions

async function loadQuiz() {
  loading.value = true;

  try {
    const res = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/get-quiz.php?id=${route.params.id}`
    );

    const data = await res.json();

    if (data.success) {
      quiz.value = data.quiz;
      questions.value = data.questions || [];
    } else {
      quiz.value = null;
    }
  } catch (err) {
    console.error("Quiz loading failed", err);
    quiz.value = null;
  } finally {
    loading.value = false;
  }
}

// Quiz Flow

function startQuiz() {
  started.value = true;
}

function nextQuestion() {
  if (questions.value && currentIndex.value < questions.value.length - 1) {
    currentIndex.value++;
  }
}

function prevQuestion() {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  }
}

async function finishQuiz() {
  const userStore = useUserStore();

  const payload = {
    quiz_id: quiz.value.quiz_id,
    student_id: userStore.id,
    answers: answersGiven.value   // We'll store every answer here
  };

  try {
    const res = await fetch(
      "http://localhost/yrkesprov/vue-project/backend/api/submit-quiz.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }
    );

    const data = await res.json();

    if (!data.success) {
      alert("Fel vid rättning: " + data.message);
      return;
    }

    // Redirect to results page
    router.push(`/results/${data.result_id}`);

  } catch (err) {
    console.error(err);
    alert("Nätverksfel.");
  }
}

const answersGiven = ref([]);

function handleAnswer(answer) {
  const idx = answersGiven.value.findIndex(a => a.q_id === answer.q_id);
  if (idx >= 0) answersGiven.value[idx] = answer;
  else answersGiven.value.push(answer);
}


onMounted(() => {
  loadQuiz();
});
</script>
