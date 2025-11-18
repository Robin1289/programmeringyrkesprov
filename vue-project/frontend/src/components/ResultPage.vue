<template>
  <div class="container py-5 result-wrapper">
    
    <div v-if="loading" class="text-center">
      <h3>Laddar resultat...</h3>
    </div>

    <div v-else class="result-card-box">
      <h1 class="result-title">Ditt resultat</h1>

      <div class="result-score">
        Du fick {{ result.correct }} / {{ result.total }} rätt!
      </div>

      <div class="result-percentage">
        ({{ percentage }}%)
      </div>

      <div
        :class="percentage >= 70 ? 'result-pass' : 'result-fail'"
      >
        {{ percentage >= 70 ? 'Godkänt! 🎉' : 'Ej godkänt 😢' }}
      </div>

      <button class="result-back-btn" @click="goBack">
        Tillbaka till Dashboard
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const result = ref({ correct: 0, total: 0 });

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
