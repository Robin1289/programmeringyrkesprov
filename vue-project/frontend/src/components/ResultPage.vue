<template>
  <div class="container py-5">
    <h1>Resultat</h1>

    <div v-if="loading">Laddar resultat...</div>

    <div v-else class="card p-4 hello-kitty-card">
      <h2>Du fick {{ result.correct }} / {{ result.total }} rätt!</h2>
      <p class="mt-3">Quiz ID: {{ result.quiz_id }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const loading = ref(true);
const result = ref({});

async function loadResult() {
  const res = await fetch(
    `http://localhost/yrkesprov/vue-project/backend/api/get-result.php?id=${route.params.id}`
  );
  const data = await res.json();
  result.value = data;
  loading.value = false;
}

onMounted(loadResult);
</script>
