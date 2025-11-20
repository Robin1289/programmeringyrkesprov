<template>
  <div class="assignment-card shadow-sm rounded-2 mb-3">

    <div class="assignment-header">
      <h3 class="assignment-title mb-1">{{ quiz.quiz_name }}</h3>

      <span class="difficulty-badge" :class="difficultyClass">
        {{ difficultyText }}
      </span>
    </div>

    <p class="assignment-teaser text-muted mb-3">{{ teaser }}</p>

    <!-- Badge if completed -->
    <div v-if="quiz.completed" class="mb-3 text-center">
      <span class="badge bg-success">Avklarad ✔</span>
    </div>

      <!-- Not done -->
      <router-link
        v-if="!quiz.completed && !quiz.failed"
        :to="`/quiz/${quiz.quiz_id}`"
        class="btn btn-primary w-100"
      >
        Starta Quiz
      </router-link>

      <!-- Failed -->
      <router-link
        v-else-if="quiz.failed"
        :to="`/quiz/${quiz.quiz_id}`"
        class="btn btn-danger w-100"
      >
        Gör om quiz ❌
      </router-link>

      <!-- Completed -->
      <router-link
        v-else
        :to="`/results/${quiz.result_id}`"
        class="btn btn-success w-100"
      >
        Visa resultat
      </router-link>

  </div>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  quiz: { type: Object, required: true },
  userLevel: { type: Number, required: true }
})

const teaser = computed(() => {
  const desc = props.quiz.quiz_description || ""
  if (desc.length <= 150) return desc
  return desc.slice(0, desc.lastIndexOf(" ", 150)) + "..."
})

const difficultyLevel = computed(() => {
  const ql = props.quiz.quiz_min_level_fk
  const ul = props.userLevel
  if (ql === ul) return "recommended"
  if (ql < ul) return "easy"
  return "hard"
})

const difficultyText = computed(() => ({
  recommended: "Rekommenderad",
  easy: "Lättare",
  hard: "Utmanande"
}[difficultyLevel.value]))

const difficultyClass = computed(() => "diff-" + difficultyLevel.value)
</script>
