<template>
  <div class="questions-card">

    <h3 class="section-title">Frågor</h3>

    <div
      v-for="q in questions"
      :key="q.q_id"
      class="question-row"
    >

      <input
        v-model="q.q_name"
        class="form-control mb-2"
        placeholder="Frågetext"
      />

      <select
        class="form-select mb-2"
        v-model="q.q_type"
      >
        <option value="multiple">Flervalsfråga</option>
        <option value="text">Text</option>
        <option value="single">Sant/Falskt</option>
        <option value="match">Ordning</option>
        <option value="multiple">Matchning</option>
      </select>

      <textarea
        v-model="q.q_correct_text"
        class="form-control mb-2"
        placeholder="Rätt svar"
      ></textarea>

      <button
        class="btn btn-sm btn-outline-danger"
        @click="$emit('delete-question', q.q_id)"
      >
        Ta bort fråga
      </button>

    </div>

    <button
      class="btn btn-outline-pink mt-3"
      @click="$emit('new-question')"
    >
      + Lägg till fråga
    </button>

  </div>
</template>

<script setup>
defineProps({
  questions: Array
})
</script>
