<template>
  <div class="mt-0 w-100" v-if="currentLevel && nextLevel">
    <div class="d-flex flex-column justify-content-center w-100">
      <div class="progress-wrapper">
        <div class="progress">
          <div class="progress-bar" :style="{ width: progressPercentage + '%' }"></div>
        </div>
        <div class="progress-label mt-1">
          Points: {{ userPoints }} / {{ nextLevel?.l_min_points ?? 0 }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentLevel: { type: Object, default: null },
  nextLevel: { type: Object, default: null },
  userPoints: { type: Number, default: 0 }
})

const progressPercentage = computed(() => {
  if (!props.currentLevel || !props.nextLevel) return 0
  const minPoints = props.currentLevel.l_min_points ?? 0
  const maxPoints = props.nextLevel.l_min_points ?? 1 // avoid divide by zero
  const progress = props.userPoints - minPoints
  const total = maxPoints - minPoints
  return Math.min(Math.max((progress / total) * 100, 0), 100)
})
</script>
