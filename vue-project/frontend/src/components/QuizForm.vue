<template>
  <div class="quiz-form-card">

    <h3 class="form-title">{{ title }}</h3>

    <div class="mb-3">
      <label class="form-label fw-semibold">Quiz-namn</label>
      <input
        v-model="local.quiz_name"
        class="form-control"
        type="text"
        required
      />
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Beskrivning</label>
      <textarea
        v-model="local.quiz_description"
        class="form-control"
        rows="4"
      ></textarea>
    </div>

    <div class="row">
      <div class="col">
        <label class="form-label fw-semibold">Kategori</label>
        <input
          v-model="local.quiz_category"
          class="form-control"
          type="text"
          required
        />
      </div>

      <div class="col">
        <label class="form-label fw-semibold">Miniminivå</label>
        <input
          v-model="local.quiz_min_level_fk"
          class="form-control"
          type="number"
          min="1"
          required
        />
      </div>
    </div>

    <button class="btn btn-pink mt-4" @click="$emit('save', local)">
      💾 Spara quiz
    </button>

  </div>
</template>

<script setup>
import { reactive, watch } from "vue"

const props = defineProps({
  quiz: Object,
  title: String
})

const local = reactive({
  quiz_name: "",
  quiz_description: "",
  quiz_category: "",
  quiz_min_level_fk: 1
})

// Sync incoming quiz data
watch(() => props.quiz, v => {
  if (!v) return
  Object.assign(local, v)
}, { immediate: true })
</script>
