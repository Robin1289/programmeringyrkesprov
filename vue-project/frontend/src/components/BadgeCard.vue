<template>
  <div class="badge-card p-3">

    <div class="badge-img-wrapper">
      <img :src="badgeImage" class="badge-img" alt="Badge" />
    </div>

    <h3 class="badge-title">{{ badge.b_name }}</h3>
    <p class="badge-desc">{{ badge.b_description }}</p>

    <p class="badge-date">
      Förtjänad: {{ formattedDate }}
    </p>

  </div>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  badge: Object
})

const badgeImage = computed(() => {
  return `/assets/badges/${props.badge.b_image}`
})

const formattedDate = computed(() => {
  return new Date(props.badge.sb_date).toLocaleDateString("sv-FI", {
    year: "numeric",
    month: "short",
    day: "numeric"
  })
})
</script>
