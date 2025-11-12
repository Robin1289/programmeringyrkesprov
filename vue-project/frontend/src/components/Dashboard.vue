<template>
  <div class="dashboard-container mt-5">
    <!-- Header -->
    <div class="dashboard-header d-flex align-items-center mb-5">
      <div class="profile-pic-wrapper me-3">
        <img
          src="/../frontend/public/assets/img/userphp.jpg"
          alt="Profile Picture"
          class="profile-pic"
        />
      </div>
      <div>
        <h1 class="dashboard-title">Hej, {{ userStore.name }}!</h1>
        <div class="progress-wrapper mt-3" v-if="userStore.level === 1">
          <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 40%;"></div>
          </div>
          <div class="progress-label mt-1">Level 1 → 2</div>
        </div>
      </div>
    </div>

<!-- Cards Section -->
<div class="row g-4">
  <div class="col-md-4">
    <router-link to="/results" class="text-decoration-none">
      <div class="dashboard-card text-center p-4">
        <h3>Results</h3>
        <p>Your quiz and course performance overview.</p>
      </div>
    </router-link>
  </div>

  <div class="col-md-4">
    <router-link to="/level" class="text-decoration-none">
      <div class="dashboard-card text-center p-4">
        <h3>Level</h3>
        <p>Track your learning progress and achievements.</p>
      </div>
    </router-link>
  </div>

  <div class="col-md-4">
    <router-link to="/assignments" class="text-decoration-none">
      <div class="dashboard-card text-center p-4">
        <h3>Assignments</h3>
        <p>View and manage your active and past assignments.</p>
      </div>
    </router-link>
  </div>
</div>
  </div>
</template>

<script setup>
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()
</script>
