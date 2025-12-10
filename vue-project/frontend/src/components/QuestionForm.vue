<template>
  <div class="card quiz-question-card p-4 hello-kitty-card">
    <h3 class="mb-3">{{ question.q_name }}</h3>

    <!-- SINGLE CHOICE -->
    <div v-if="question.q_type === 'single'">
      <div v-for="ans in question.answers" :key="ans.a_id" class="form-check mb-2">
        <input
          class="form-check-input"
          type="radio"
          :id="`q${question.q_id}-a${ans.a_id}`"
          :value="ans.a_id"
          v-model="selectedSingle"
        />
        <label class="form-check-label" :for="`q${question.q_id}-a${ans.a_id}`">
          {{ ans.a_name }}
        </label>
      </div>
    </div>

    <!-- MULTIPLE CHOICE -->
    <div v-else-if="question.q_type === 'multiple'">
      <div v-for="ans in question.answers" :key="ans.a_id" class="form-check mb-2">
        <input
          class="form-check-input"
          type="radio"
          :name="`q${question.q_id}`"
          :id="`q${question.q_id}-a${ans.a_id}`"
          :value="ans.a_id"
          v-model="selectedSingle"
        />
        <label class="form-check-label" :for="`q${question.q_id}-a${ans.a_id}`">
          {{ ans.a_name }}
        </label>
      </div>
    </div>

    <!-- TEXT ANSWER -->
    <div v-else-if="question.q_type === 'text'">
      <textarea
        class="form-control kitty-textarea"
        rows="4"
        v-model="textAnswer"
        placeholder="Skriv ditt svar här..."
      ></textarea>
    </div>

    <!-- SORT QUESTION -->
    <div v-else-if="question.q_type === 'sort'">
      <p class="small text-muted mb-2">Dra raderna för att sätta dem i rätt ordning.</p>

      <ul ref="sortList" class="sort-list list-unstyled">
        <li
          v-for="item in sortItems"
          :key="item.id"
          class="sort-item kitty-sort-item"
          :data-id="item.id"
        >
          <span class="kitty-sort-handle me-2">⇅</span>
          {{ item.text }}
        </li>
      </ul>
    </div>

    <!-- MATCH QUESTION -->
    <div v-else-if="question.q_type === 'match'">
      <p class="small text-muted mb-3">Matcha vänster kolumn med rätt alternativ.</p>

      <div class="match-grid">
        <div v-for="pair in question.match_pairs" :key="pair.mp_id" class="match-row">

          <!-- LEFT TEXT -->
          <div class="match-left-box">
            {{ pair.mp_left_text }}
          </div>

          <!-- RIGHT SELECT -->
          <div class="match-right-box">
            <select
              class="kitty-select match-select"
              v-model="matchSelections[pair.mp_id]"
              @change="emitAnswer"
            >
              <option disabled value="">Välj...</option>
              <option
                v-for="opt in shuffledRightSide"
                :key="opt.value"
                :value="opt.value"
              >
                {{ opt.label }}
              </option>
            </select>
          </div>

        </div>
      </div>
    </div>

    <!-- FALLBACK -->
    <div v-else>
      <p class="text-danger">Okänd frågetyp: {{ question.q_type }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, nextTick } from "vue";
import Sortable from "sortablejs";

const props = defineProps({
  question: { type: Object, required: true },
  initialAnswer: { type: Object, default: null }
});

const emit = defineEmits(["answer"]);

const selectedSingle = ref(null);
const textAnswer = ref("");
const sortItems = ref([]);
const matchSelections = ref({});
const sortList = ref(null);
let sortableInstance = null;

/* MATCH OPTIONS */
const shuffledRightSide = computed(() => {
  if (!props.question?.match_pairs) return [];
  const arr = props.question.match_pairs.map(p => ({
    value: p.mp_id,
    label: p.mp_right_text
  }));
  // shuffle
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
});

/* RESET */
function resetState() {
  selectedSingle.value = null;
  textAnswer.value = "";
  matchSelections.value = {};

  if (sortableInstance) sortableInstance.destroy();
  sortableInstance = null;
}



/* INIT SORT */
function initSort(q) {
  if (q.answers?.length) {
    sortItems.value = q.answers.map(a => ({ id: a.a_id, text: a.a_name }));
  } else if (q.q_correct_text) {
    sortItems.value = q.q_correct_text.split(",").map((txt, i) => ({
      id: i + 1,
      text: txt.trim()
    }));
  }

  nextTick(() => {
    if (!sortList.value) return;

    sortableInstance = Sortable.create(sortList.value, {
      animation: 150,
      handle: ".kitty-sort-handle",
      onEnd() {
        const newOrder = [...sortList.value.children].map(li => {
          const id = Number(li.getAttribute("data-id"));
          return sortItems.value.find(i => i.id === id);
        });

        sortItems.value = newOrder;
        emitAnswer();
      }
    });
  });
}

/* BUILD ANSWER */
function buildAnswer() {
  const q = props.question;

  if (q.q_type === "single")
    return { q_id: q.q_id, type: "single", answer_ids: selectedSingle.value ? [selectedSingle.value] : [] };

  if (q.q_type === "multiple")
  return { q_id: q.q_id, type: "multiple", answer_ids: selectedSingle.value ? [selectedSingle.value] : [] };

  if (q.q_type === "text")
    return { q_id: q.q_id, type: "text", text: textAnswer.value };

  if (q.q_type === "sort")
    return { q_id: q.q_id, type: "sort", order: sortItems.value.map(i => i.text) };

  if (q.q_type === "match")
    return { q_id: q.q_id, type: "match", matches: matchSelections.value };

  return { q_id: q.q_id, type: q.q_type };
}

function emitAnswer() {
  emit("answer", buildAnswer());
}

/* WATCH QUESTION CHANGES */
watch(
  () => props.question,
  q => {
    resetState();
    if (!q) return;

    if (q.q_type === "sort") initSort(q);
    emitAnswer();
  },
  { immediate: true }
);

/* WATCH ANSWERS */
watch([selectedSingle, textAnswer, matchSelections, sortItems], emitAnswer, { deep: true });
</script>
