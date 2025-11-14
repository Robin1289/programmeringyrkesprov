<template>
  <div class="assignment-card shadow-sm rounded-2 mb-3">

    <!-- QUIZ TITLE -->
    <div class="assignment-header">
      <h3 class="assignment-title mb-1">{{ quiz.quiz_name }}</h3>

      <!-- Difficulty badge -->
      <span
        class="difficulty-badge"
        :class="difficultyClass"
      >
        {{ difficultyText }}
      </span>
    </div>

    <!-- Automatically generated teaser -->
    <p class="assignment-teaser text-muted mb-3">{{ teaser }}</p>

    <!-- Start button -->
    <router-link
      :to="`/quiz/${quiz.quiz_id}`"
      class="btn btn-primary w-100"
    >
      Start Quiz
    </router-link>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  quiz: { type: Object, required: true },
  userLevel: { type: Number, required: true }
})

const teaser = computed(() => {
  const desc = props.quiz.quiz_description || ""
  if (desc.length <= 150) return desc

  let text = desc.slice(0, 150)
  const lastSpace = text.lastIndexOf(" ")
  text = text.slice(0, lastSpace)
  return text + "..."
})


const difficultyLevel = computed(() => {
  const quizLevel = props.quiz.quiz_min_level_fk
  const user = props.userLevel

  if (quizLevel === user) return "recommended"
  if (quizLevel < user) return "easy"
  return "hard"
})

const difficultyText = computed(() => {
  switch (difficultyLevel.value) {
    case "recommended": return "Rekommenderad"
    case "easy": return "Lättare"
    case "hard": return "Utmanande"
  }
})

const difficultyClass = computed(() => {
  return "diff-" + difficultyLevel.value
})
</script>
