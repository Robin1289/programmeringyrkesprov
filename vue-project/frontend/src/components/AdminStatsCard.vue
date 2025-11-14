<template>
  <div class="admin-stats-card p-4 text-center">
    <div class="admin-stats-icon">{{ icon }}</div>
    <h3 class="admin-stats-title">{{ title }}</h3>
    <div class="admin-stats-value">{{ value }}</div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  icon: String,
  value: Number,
});
</script>
