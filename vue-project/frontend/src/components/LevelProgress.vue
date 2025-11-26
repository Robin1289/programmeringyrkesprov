<template>
  <div class="level-progress-container" v-if="currentLevel && nextLevel">

    <h3 class="level-progress-title">
      {{ currentLevel.l_name }} → {{ nextLevel.l_name }}
    </h3>

    <div class="level-progress-bar">
      <div class="level-progress-fill" :style="{ width: progressPercentage + '%' }"></div>
    </div>

    <p class="level-progress-label">
      {{ userPoints }} / {{ nextLevel.l_min_points }} XP
    </p>

  </div>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  currentLevel: Object,
  nextLevel: Object,
  userPoints: Number
})

const progressPercentage = computed(() => {
  if (!props.currentLevel || !props.nextLevel) return 0
  const min = props.currentLevel.l_min_points ?? 0
  const max = props.nextLevel.l_min_points ?? 1
  const gained = props.userPoints - min
  return Math.min(Math.max((gained / (max - min)) * 100, 0), 100)
})
</script>
