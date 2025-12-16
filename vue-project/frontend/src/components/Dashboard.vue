<template>
  <div class="dashboard-container mt-5">

    <!-- HEADER -->
    <div class="dashboard-header d-flex align-items-center mb-5">
      <div class="profile-pic-wrapper me-3">
        <img
          src="/../frontend/public/assets/img/userphp.jpg"
          alt="Profile Picture"
          class="profile-pic"
        />
      </div>

      <div class="d-flex flex-column w-100">
        <h1 class="dashboard-title">Hello, {{ userStore.name }}!</h1>

        <div class="mt-0 w-100" v-if="currentLevel && nextLevel">
          <div class="progress-wrapper">
            <div class="progress">
              <div
                class="progress-bar"
                :style="{ width: progressPercentage + '%' }"
              ></div>
            </div>
            <div class="progress-label mt-1">
              Points: {{ userPoints }} / {{ nextLevel?.l_min_points ?? 0 }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ⭐ RANK SECTION -->
    <section v-if="rank !== null" class="rank-banner mt-4 mb-4">
      <h2 class="rank-title">Din ranking</h2>

      <p class="rank-position">
        Du ligger på <strong>{{ rank }}:e plats</strong>.
      </p>

      <p class="rank-percentage">
        Du är i <strong>topp {{ percentage }}%</strong> av alla elever.
      </p>

      <p class="rank-message">{{ rankMessage }}</p>
    </section>


    <!-- Cards Section -->
    <div class="row g-4 pt-3">
      <div class="col-md-4">
        <router-link to="/results" class="text-decoration-none">
          <div class="dashboard-card text-center p-5">
            <h3>Resultat</h3>
            <p>Översikt över dina resultat i frågesporter och kurser.</p>
          </div>
        </router-link>
      </div>

      <div class="col-md-4">
        <router-link to="/level" class="text-decoration-none">
          <div class="dashboard-card text-center p-5">
            <h3>Nivå</h3>
            <p>
              Nästa nivå: {{ nextLevel?.l_name ?? 'Loading...' }}
              ({{ nextLevel?.l_min_points ?? 0 }} Poäng som behövs)
            </p>
          </div>
        </router-link>
      </div>

      <div class="col-md-4">
        <router-link to="/assignments" class="text-decoration-none">
          <div class="dashboard-card text-center p-5">
            <h3>Uppgifter</h3>
            <p>Visa och hantera dina aktiva och tidigare uppgifter</p>
          </div>
        </router-link>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useUserStore } from "../store/userstore.js";

const userStore = useUserStore();

const currentLevel = ref(null);
const nextLevel = ref(null);
const userPoints = ref(0);

const rank = ref(null);
const percentage = ref(0);
const totalUsers = ref(0);

const progressPercentage = computed(() => {
  if (!currentLevel.value || !nextLevel.value) return 0;

  const minPoints = currentLevel.value.l_min_points;
  const maxPoints = nextLevel.value.l_min_points;
  const progress = userPoints.value - minPoints;
  const total = maxPoints - minPoints;

  return Math.min(Math.max((progress / total) * 100, 0), 100);
});

const rankMessage = computed(() => {
  if (rank.value === 1) return "🔥 Du är i absolut topp! Fantastiskt!";
  if (percentage.value <= 20) return "💪 Du ligger riktigt högt – starkt jobbat!";
  if (percentage.value <= 60) return "📈 Du är på god väg – fortsätt så!";
  return "🚀 Alla börjar någonstans. Du klättrar uppåt!";
});


async function fetchLevels() {
  const response = await fetch(
    `https://yrkesprov.fwh.is/backend/api/levels.php?user_id=${userStore.id}`,
    { credentials: "include" }
  );
  const data = await response.json();
  if (data.success) {
    currentLevel.value = data.currentLevel;
    nextLevel.value = data.nextLevel;
    userPoints.value = data.userPoints;
  }
}

async function fetchRank() {
  const res = await fetch(
    "https://yrkesprov.fwh.is/backend/api/user_rank.php",
    { credentials: "include" }
  );
  const data = await res.json();

  if (data.success) {
    rank.value = data.rank;
    // New: top % calculation
      if (data.rank && data.total) {
        percentage.value = Math.round((data.rank / data.total) * 100);
      }
    totalUsers.value = data.total;
  }
}

onMounted(() => {
  fetchLevels();
  fetchRank();
});
</script>
