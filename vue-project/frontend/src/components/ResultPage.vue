<template>
    <!-- ✦ Kawaii Particle Animation -->
  <div v-if="!loading" class="kawaii-particles">
    <span v-for="n in 20" :key="n" class="kawaii-particle">🎀</span>
  </div>
  <div class="container py-5 result-wrapper">

    <div
      v-if="!loading && percentage >= 70"
      class="result-confetti-bg"
      :style="{ backgroundImage: `url(${confetti})` }"
    ></div>

    <div
      v-if="!loading && percentage < 70"
      class="result-sadrain-bg"
      :style="{ backgroundImage: `url(${rain})` }"
    ></div>

    <div v-if="loading" class="text-center">
      <h3>Laddar resultat...</h3>
    </div>

    <div
      v-else
      class="result-card-box result-pop"
      :class="percentage >= 70 ? 'result-pass-box' : 'result-fail-box'"
    >
      <h1 class="result-title">Ditt resultat</h1>

      <div class="result-score">
        Du fick {{ result.correct }} / {{ result.total }} rätt!
      </div>

      <div class="result-percentage mb-4">
        ({{ percentage }}%)
      </div>

      <div
        :class="[
          percentage >= 70 ? 'result-pass-text result-bounce' : 'result-fail-text result-wobble'
        ]"
      >
        {{ percentage >= 70 ? 'Godkänt! 🎉' : 'Ej godkänt 😢' }}
      </div>

      <div class="mt-3 d-flex justify-content-center gap-3">

        <button class="result-back-btn kitty-btn" @click="goBack">
          Tillbaka till Dashboard
        </button>

        <router-link
          v-if="percentage < 70"
          :to="`/quiz/${result.quiz_id}`"
          class="btn tryagain-btn kitty-btn"
        >
          Försök igen 🔁
        </router-link>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import confetti from "/../frontend/public/assets/animations/confetti.gif"
import rain from "/../frontend/public/assets/animations/rain.gif"

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const result = ref({ correct: 0, total: 0, quiz_id: null });

async function loadResult() {
  const res = await fetch(
    `http://localhost/yrkesprov/vue-project/backend/api/get-result.php?id=${route.params.id}`,
    { credentials: "include" }
  );
  const data = await res.json();

  if (data.success) {
    result.value = data.result;
  }

  loading.value = false;
}

const percentage = computed(() => {
  if (!result.value.total) return 0;
  return Math.round((result.value.correct / result.value.total) * 100);
});

function goBack() {
  router.push("/dashboard");
}

onMounted(loadResult);
</script>
