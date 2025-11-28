<template>
  <footer class="app-footer mt-5">
    <div class="footer-content">

      <!-- Left: branding -->
      <div class="footer-section">
        <h3 class="footer-title">Yrkesprov Plattform</h3>
        <p class="footer-text">
          Ett verktyg för läsförståelse, övningar och lärande.
        </p>
      </div>

      <!-- Middle: dynamic links -->
      <div class="footer-section">
        <h4 class="footer-subtitle">Länkar</h4>
        <ul class="footer-links">
          <li v-for="link in activeLinks" :key="link.name">
            <router-link :to="link.to">
              {{ link.name }}
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Right: socials -->
      <div class="footer-section">
        <h4 class="footer-subtitle">Följ oss</h4>
        <div class="footer-socials">
          <a href="#" aria-label="Instagram">📸</a>
          <a href="#" aria-label="Twitter">🐦</a>
          <a href="#" aria-label="YouTube">▶️</a>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <p>Tillverkad med ❤️ i Finland — © 2025</p>
    </div>
  </footer>
</template>

<script setup>
import { computed } from "vue";
import { useUserStore } from "../store/userstore.js";

const userStore = useUserStore();

// ----- Student Links -----
const studentLinks = [
  { name: "Hemsida", to: "/dashboard" },
  { name: "Uppgifter", to: "/assignments" },
  { name: "Resultat", to: "/results" },
  { name: "Nivå", to: "/level" }
];

// ----- Admin / Teacher Links -----
const adminLinks = [
  { name: "Admin Hemsida", to: "/admin-dashboard" },
  { name: " Hantera prov", to: "/admin-quizzes" },
  { name: "Hantera resultat", to: "/admin-results" },
  { name: "Hantera användare", to: "/admin-users" }
];

// ----- Choose which list to show -----
const activeLinks = computed(() => {
  if (!userStore.isLoggedIn) return []; // hide links for logged-out users
  if (userStore.role === 2 || userStore.role === 3) return adminLinks;
  return studentLinks;
});
</script>
