<template>
  <div v-if="badge" class="badge-toast">
    <img :src="`/assets/badges/${badge.b_image}`" />
    <div>
      <strong>{{ badge.b_name }}</strong>
      <p>{{ badge.b_description }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  badge: Object
});
</script>
