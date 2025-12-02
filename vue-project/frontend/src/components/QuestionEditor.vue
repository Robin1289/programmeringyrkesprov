<template>
  <div class="questions-card">

    <h3 class="section-title">Frågor</h3>

    <div
      v-for="(q, qIndex) in questions"
      :key="q.q_id ?? q.temp_id"
      class="question-row"
    >

      <!-- Question text -->
      <input
        v-model="q.q_name"
        class="form-control mb-2"
        placeholder="Frågetext"
      />

      <!-- Type -->
      <select
        v-model="q.q_type"
        class="form-select mb-2"
      >
        <option value="multiple">Flervalsfråga</option>
        <option value="text">Text</option>
        <option value="single">Sant/Falskt</option>
        <option value="match">Matcha</option>
        <option value="sort">Ordning</option>
      </select>

      <!-- TEXT / SINGLE -->
      <textarea
        v-if="q.q_type !== 'multiple' && q.q_type !== 'sort'"
        v-model="q.q_correct_text"
        class="form-control mb-2"
        placeholder="Rätt svar"
      ></textarea>

      <!-- SORT EDITOR -->
      <div v-if="q.q_type === 'sort'" class="mb-3">

        <p class="small text-muted">Dra för att ändra ordning:</p>

        <ul
          class="sort-list"
          :ref="el => initSort(qIndex, el)"
        >
          <li
            v-for="(a, i) in q.answers"
            :key="a.a_id ?? a.temp_id"
            class="sort-item d-flex align-items-center gap-2 mb-2"
          >
            <span class="kitty-sort-handle">⇅</span>

            <input
              v-model="a.a_name"
              class="form-control"
              placeholder="Moment"
            />

            <button
              class="btn btn-sm btn-outline-danger"
              @click="q.answers.splice(i, 1)"
            >
              ✕
            </button>

          </li>
        </ul>

        <button
          class="btn btn-sm btn-outline-pink mt-2"
          @click="addSortItem(q)"
        >
          + Lägg till moment
        </button>

      </div>


      <!-- MULTIPLE CHOICE -->
      <div v-if="q.q_type === 'multiple'" class="mc-block mb-3">

        <div
          v-for="(a, i) in q.answers"
          :key="a.a_id ?? a.temp_id"
          class="d-flex align-items-center gap-2 mb-2"
        >

          <input
            type="radio"
            :name="`correct_${q.q_id ?? q.temp_id}`"
            :checked="a.a_iscorrect == 1"
            @change="setCorrectAnswer(q, a)"
          />

          <input
            v-model="a.a_name"
            class="form-control"
            placeholder="Svarsalternativ"
          />

          <button
            class="btn btn-sm btn-outline-danger"
            @click="q.answers.splice(i,1)"
          >
            ✕
          </button>

        </div>

        <button
          class="btn btn-sm btn-outline-pink"
          @click="addAnswer(q)"
        >
          + Lägg till svar
        </button>

      </div>


      <!-- DELETE QUESTION -->
      <button
        class="btn btn-sm btn-outline-danger mt-2"
        @click="$emit('delete-question', q.q_id)"
      >
        Ta bort fråga
      </button>

    </div>


    <!-- ADD QUESTION -->
    <button
      class="btn btn-outline-pink mt-4"
      @click="$emit('new-question')"
    >
      + Lägg till fråga
    </button>

  </div>
</template>


<script setup>
import Sortable from "sortablejs"

defineProps({ questions: Array })


function initSort(index, el) {
  if (!el || el.sortable) return

  el.sortable = Sortable.create(el, {
    animation: 150,
    handle: ".kitty-sort-handle",
    onEnd(evt) {
      const q = window.vueQuestions[index]
      const moved = q.answers.splice(evt.oldIndex, 1)[0]
      q.answers.splice(evt.newIndex, 0, moved)
    }
  })
}


function addSortItem(q) {
  if (!q.answers) q.answers = []

  q.answers.push({
    temp_id: crypto.randomUUID(),
    a_name: ""
  })
}


/* MULTIPLE CHOICE HELPERS */

function addAnswer(q) {
  if (!q.answers) q.answers = []

  q.answers.push({
    temp_id: crypto.randomUUID(),
    a_name: "",
    a_iscorrect: 0
  })
}


function setCorrectAnswer(q, a) {
  q.answers.forEach(x => x.a_iscorrect = 0)
  a.a_iscorrect = 1
}


window.vueQuestions = null
</script>
