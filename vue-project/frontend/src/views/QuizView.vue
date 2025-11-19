<template>
  <div class="container py-5 quiz-view">

    <div v-if="loading" class="text-center my-5">
      <h3>Laddar quiz...</h3>
    </div>

    <div v-else-if="!quiz">
      <h3 class="text-center text-danger">Quiz hittades inte.</h3>
    </div>

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

    <div v-else>
      
      <div v-if="questions.length > 0">
        <h2 class="mb-4">Fråga {{ currentIndex + 1 }} / {{ questions.length }}</h2>

        <QuestionForm
          v-if="questions[currentIndex]"
          :question="questions[currentIndex]"
          :initialAnswer="getPreviousAnswer(questions[currentIndex])"
          @answer="handleAnswer"
        />

        <div class="mt-4 d-flex justify-content-between">

          <!-- PREVIOUS -->
          <button
            class="btn btn-secondary"
            :disabled="currentIndex === 0"
            @click="currentIndex--"
          >
            Föregående
          </button>

          <!-- NEXT -->
          <button
            v-if="currentIndex < questions.length - 1"
            class="btn btn-primary kitty-btn"
            :disabled="!canProceed"
            @click="nextQuestion"
          >
            Nästa
          </button>

          <!-- FINISH -->
          <button
            v-else
            class="btn btn-success kitty-btn"
            :disabled="!canProceed"
            @click="finishQuiz"
          >
            Slutför quiz 🎀
          </button>

        </div>
      </div>

      <div v-else class="text-center">
        <h3 class="text-danger">Inga frågor hittades i detta quiz.</h3>
      </div>

    </div>

  </div>
  <!-- Kitty Warning Popup -->
<div v-if="showWarning" class="kitty-warning-overlay">
  <div class="kitty-warning-box">
    <h3>💖 Vänta lite! 💖</h3>
    <p>Du måste välja ett svar innan du kan gå vidare.</p>
    <button class="kitty-btn mt-3" @click="showWarning = false">Okej!</button>
  </div>
</div>

</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import QuestionForm from "../components/QuestionForm.vue";
import { useUserStore } from "../store/userstore.js";

const userStore = useUserStore();
const route = useRoute();
const router = useRouter();

const quiz = ref(null);
const questions = ref([]);
const loading = ref(true);
const started = ref(false);

const currentIndex = ref(0);
const answersGiven = ref([]);

const showWarning = ref(false);


/* -----------------------------------------
   Soft Refresh Warning (Option B)
------------------------------------------ */
function refreshWarning(e) {
  e.preventDefault();
  e.returnValue = "Lämnar du sidan försvinner dina svar!";
  return "Lämnar du sidan försvinner dina svar!";
}

onBeforeUnmount(() => {
  window.removeEventListener("beforeunload", refreshWarning);
});

function startQuiz() {
  started.value = true;
  window.addEventListener("beforeunload", refreshWarning);
}

/* -----------------------------------------
   Load quiz
------------------------------------------ */
async function loadQuiz() {
  loading.value = true;

  try {
    const res = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/get-quiz.php?id=${route.params.id}`
    );
    const data = await res.json();

    if (data.success) {
      quiz.value = data.quiz;
      questions.value = data.questions;
    }
  } catch (e) {
    console.error("Quiz load failed", e);
  }

  loading.value = false;
}

/* -----------------------------------------
   Answer management
------------------------------------------ */

function getPreviousAnswer(q) {
  return answersGiven.value.find(a => a.q_id === q.q_id) || null;
}

function handleAnswer(ans) {
  const idx = answersGiven.value.findIndex(a => a.q_id === ans.q_id);
  if (idx >= 0) answersGiven.value[idx] = ans;
  else answersGiven.value.push(ans);
}

/* -----------------------------------------
   Validation – MUST be answered
------------------------------------------ */

const canProceed = computed(() => {
  const q = questions.value[currentIndex.value];
  if (!q) return false;

  const a = answersGiven.value.find(x => x.q_id === q.q_id);
  if (!a) return false;

  if (q.q_type === "single") return a.answer_ids?.length === 1;
  if (q.q_type === "multiple") return a.answer_ids?.length > 0;
  if (q.q_type === "text") return a.text?.trim().length > 0;
  if (q.q_type === "match") {
    return Object.values(a.matches || {}).every(v => v !== "");
  }
  if (q.q_type === "sort") return true; // always allowed

  return false;
});

/* -----------------------------------------
   Navigation
------------------------------------------ */

function nextQuestion() {
  if (!canProceed.value) {
    showWarning.value = true;
    return;
  }
  currentIndex.value++;
}


/* -----------------------------------------
   Submit quiz
------------------------------------------ */

async function finishQuiz() {
  if (!canProceed.value) {
    showWarning.value = true;
    return;
  }



  const payload = {
    quiz_id: quiz.value.quiz_id,
    student_id: userStore.id,
    answers: answersGiven.value
  };

  const res = await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/submit-quiz.php",
    {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    }
  );

  const data = await res.json();
  console.log("SUBMIT:", data);

  if (!data.success) {
    alert("Fel: " + data.message);
    return;
  }

  router.push(`/results/${data.result_id}`);
}

/* -----------------------------------------
   Init
------------------------------------------ */
onMounted(async () => {
  await userStore.fetchUser();
  await loadQuiz();
  await fetch("http://localhost/yrkesprov/vue-project/backend/api/level_up.php", {
    method: "POST",
    credentials: "include"
  });
});
</script>
