<template>
  <div class="dashboard-container">

    <!-- HEADER -->
    <div class="dashboard-header d-flex gap-3 align-items-center mt-5">
      <div class="profile-pic-wrapper">
        <img
          src="/public/assets/img/userphp.jpg"
          alt="Admin Profile"
          class="profile-pic"
        />
      </div>

      <div class="d-flex flex-column justify-content-center w-100">
        <div class="dashboard-title mb-0">
          Adminpanel – Välkommen {{ userStore.name }}!
        </div>
        <p class="admin-subtitle">Översikt & hantering</p>
      </div>
    </div>

    <!-- STATS ROW (CLICKABLE) -->
    <div class="row g-4 mt-4">

      <div class="col-md-4">
        <router-link to="/admin-users" class="stat-card-link">
          <AdminStatsCard title="Antal användare" icon="👥" :value="stats.users" />
        </router-link>
      </div>

      <div class="col-md-4">
        <router-link to="/admin-quizzes" class="stat-card-link">
          <AdminStatsCard title="Antal quiz" icon="📝" :value="stats.quizzes" />
        </router-link>
      </div>

      <div class="col-md-4">
        <router-link to="/admin-results" class="stat-card-link">
          <AdminStatsCard title="Genomförda resultat" icon="🏆" :value="stats.results" />
        </router-link>
      </div>

    </div>

<section class="level-heatmap-section mt-5">
  <h2 class="admin-section-title">Nivåfördelning</h2>
  <p class="admin-section-subtitle">
    En överblick på hur många användare som ligger på varje nivå.
  </p>

  <div class="level-heatmap">
    <div 
      v-for="level in levelData" 
      :key="level.display_level"
      class="heatmap-row"
    >
      <!-- Nivå 1, Nivå 2, ... -->
      <div class="heatmap-label">Nivå {{ level.display_level }}</div>

      <!-- BAR WIDTH FIXED TO USER COUNT -->
      <div class="heatmap-bar-wrapper">
        <div 
          class="heatmap-bar"
          :style="{ width: Math.max(level.users * 12, 5) + 'px' }"
        ></div>
      </div>

      <!-- THIS WAS WRONG BEFORE -->
      <div class="heatmap-count">{{ level.users }} st</div>
    </div>
  </div>
</section>


  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useUserStore } from "../store/userstore.js";
import AdminStatsCard from "../components/AdminStatsCard.vue";

const userStore = useUserStore();

const stats = ref({
  users: 0,
  quizzes: 0,
  results: 0,
});

const levelData = ref([]);

// LOAD STATS + HEATMAP
onMounted(async () => {
  await fetchStats();
  await fetchLevelHeatmap();
});

async function fetchStats() {
  const response = await fetch(
    "http://localhost/yrkesprov/vue-project/backend/api/admin_get_stats.php",
    { credentials: "include" }
  );

  const data = await response.json();

  if (data.success) {
    stats.value.users   = data.stats.users;
    stats.value.quizzes = data.stats.quizzes;
    stats.value.results = data.stats.results;
  }
}


// Fetch data for heatmap
async function fetchLevelHeatmap() {
  try {
    const res = await fetch(
      "http://localhost/yrkesprov/vue-project/backend/api/admin_get_levels.php",
      { credentials: "include" }
    );

    const data = await res.json();

    if (data.success) {
      levelData.value = data.levels;
    }
  } catch (err) {
    console.error("Heatmap fetch error:", err);
  }
}
</script>

<style scoped>
.stat-card-link {
  text-decoration: none;
  display: block;
}

.stat-card-link:hover {
  opacity: 0.9;
}
</style>
