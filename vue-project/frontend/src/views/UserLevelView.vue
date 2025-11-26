<template>
  <div class="dashboard-container">

    <!-- Level Page Header -->
    <div class="level-header d-flex gap-3 align-items-center mt-5 p-3">
      <div class="profile-pic-wrapper level-pic">
        <img src="/public/assets/img/userphp.jpg" alt="Profile" class="profile-pic" />
      </div>

      <div class="d-flex flex-column justify-content-center w-100">
        <div class="level-title mb-1">
          Hello, {{ userName }}! Your Progress Towards the Next Level.
        </div>

        <LevelProgress
          :current-level="currentLevel"
          :next-level="nextLevel"
          :user-points="userPoints"
        />
      </div>
    </div>

        <section class="motivation-card mt-5 p-4 mb-0">
          <h2 class="motivation-title">✨ Keep Going!</h2>
          <p class="motivation-message">{{ motivationMessage }}</p>
        </section>


        <!-- Badge Section -->
        <section class="badge-section mt-5">
          <h2 class="badge-header">🏅 Dina Badges</h2>

          <div v-if="badges.length === 0" class="text-muted">
            Du har inga badges ännu — fortsätt göra quiz! 💖
          </div>

          <div class="row mt-3 g-3">
            <div class="col-md-4" v-for="badge in badges" :key="badge.b_id">
              <BadgeCard :badge="badge" />
            </div>
          </div>
        </section>


  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useUserStore } from "../store/userstore.js";
import LevelProgress from "../components/LevelProgress.vue";

// Store
const userStore = useUserStore();
const userName = userStore.name;

// Data
const currentLevel = ref(null);
const nextLevel = ref(null);
const userPoints = ref(0);
const loading = ref(true);

// Fetch Levels
async function fetchLevels() {
  try {
    const response = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/levels.php?user_id=${userStore.id}`,
      { credentials: "include" }
    );

    const data = await response.json();

    if (data.success) {
      currentLevel.value = data.currentLevel;
      nextLevel.value = data.nextLevel;
      userPoints.value = data.userPoints;
    }
  } catch (err) {
    console.error("Error fetching levels:", err);
  } finally {
    loading.value = false;
  }
}

// ⭐ Motivational logic based on progress
const motivationMessage = computed(() => {
  if (!currentLevel.value || !nextLevel.value) return "";

  const min = currentLevel.value.l_min_points;
  const max = nextLevel.value.l_min_points;
  const progress = ((userPoints.value - min) / (max - min)) * 100;

  if (progress < 20) {
    return "You’ve just started this level — great things begin with small steps!";
  }
  if (progress < 40) {
    return "Nice work! You’re building solid momentum. Keep pushing forward!";
  }
  if (progress < 60) {
    return "You're halfway there! Stay focused — you’re doing great!";
  }
  if (progress < 80) {
    return "Awesome progress! The next level is getting closer!";
  }
  if (progress < 95) {
    return "You're so close! One last push and you’ll level up!";
  }
  return "✨ LEVEL-UP incoming — you're right at the finish line!";
});
import BadgeCard from "../components/BadgeCard.vue"

const badges = ref([])

async function fetchBadges() {
  try {
    const res = await fetch(
      "http://localhost/yrkesprov/vue-project/backend/api/badges.php",
      { credentials: "include" }
    )

    const data = await res.json()

    if (data.success) {
      badges.value = data.badges
    }
  } catch (err) {
    console.error("Badge fetch failed", err)
  }
}

onMounted(() => {
  fetchLevels()
  fetchBadges()
})

</script>
