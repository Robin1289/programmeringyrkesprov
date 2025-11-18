<template>
  <div class="card quiz-question-card p-4 hello-kitty-card">
    <h3 class="mb-3">
      {{ question.q_name }}
    </h3>

    <!-- SINGLE CHOICE (radio) -->
    <div v-if="question.q_type === 'single'">
      <div
        v-for="ans in question.answers"
        :key="ans.a_id"
        class="form-check mb-2"
      >
        <input
          class="form-check-input"
          type="radio"
          :id="`q${question.q_id}-a${ans.a_id}`"
          :value="ans.a_id"
          v-model="selectedSingle"
        />
        <label
          class="form-check-label"
          :for="`q${question.q_id}-a${ans.a_id}`"
        >
          {{ ans.a_name }}
        </label>
      </div>
    </div>

    <!-- MULTIPLE CHOICE (checkbox) -->
    <div v-else-if="question.q_type === 'multiple'">
      <div
        v-for="ans in question.answers"
        :key="ans.a_id"
        class="form-check mb-2"
      >
        <input
          class="form-check-input"
          type="checkbox"
          :id="`q${question.q_id}-a${ans.a_id}`"
          :value="ans.a_id"
          v-model="selectedMultiple"
        />
        <label
          class="form-check-label"
          :for="`q${question.q_id}-a${ans.a_id}`"
        >
          {{ ans.a_name }}
        </label>
      </div>
    </div>

    <!-- FREE TEXT -->
    <div v-else-if="question.q_type === 'text'">
      <textarea
        class="form-control kitty-textarea"
        rows="4"
        v-model="textAnswer"
        placeholder="Skriv ditt svar här..."
      ></textarea>
    </div>

    <!-- SORT (drag och släpp i rätt ordning) -->
    <div v-else-if="question.q_type === 'sort'">
      <p class="small text-muted mb-2">
        Dra raderna för att sätta dem i rätt ordning.
      </p>
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

    <!-- MATCH (vänster/höger) -->
    <div v-else-if="question.q_type === 'match'">
      <p class="small text-muted mb-2">
        Matcha vänster kolumn med rätt alternativ i listan.
      </p>
      <div class="row">
        <div class="col-md-6">
          <ul class="list-unstyled">
            <li
              v-for="pair in question.match_pairs"
              :key="pair.mp_id"
              class="mb-2"
            >
              <strong>{{ pair.mp_left_text }}</strong>
            </li>
          </ul>
        </div>
        <div class="col-md-6">
          <div
            v-for="pair in question.match_pairs"
            :key="pair.mp_id"
            class="mb-2"
          >
            <select
              class="form-select kitty-select"
              :value="matchSelections[pair.mp_id] ?? ''"
              @change="onMatchChange(pair.mp_id, $event.target.value)"
            >
              <option disabled value="">Välj matchning...</option>
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

    <!-- fallback (ifall q_type är konstigt) -->
    <div v-else>
      <p class="text-danger">
        Okänd frågetyp: {{ question.q_type }}
      </p>
    </div>
  </div>
</template>

<script setup>
import {
  ref,
  watch,
  computed,
  nextTick,
} from "vue";
import Sortable from "sortablejs";

const props = defineProps({
  question: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["answer"]);

// --- LOCAL STATE -----------------------------

const selectedSingle = ref(null);
const selectedMultiple = ref([]);
const textAnswer = ref("");
const sortList = ref(null);
const sortItems = ref([]);
const matchSelections = ref({});
const sortableInstance = ref(null);

// --- UTILS -----------------------------------

function resetState() {
  selectedSingle.value = null;
  selectedMultiple.value = [];
  textAnswer.value = "";
  sortItems.value = [];
  matchSelections.value = {};
  if (sortableInstance.value) {
    sortableInstance.value.destroy();
    sortableInstance.value = null;
  }
}

// Bygg sortItems för sort-frågor
function initSortItems(q) {
  const items = [];

  if (Array.isArray(q.answers) && q.answers.length > 0) {
    // Om du senare väljer att lägga sort-alternativ i answer-tabellen
    q.answers.forEach((a) => {
      items.push({
        id: a.a_id,
        text: a.a_name,
      });
    });
  } else if (q.q_correct_text) {
    // Just nu: plocka från q_correct_text (komma-separerad)
    q.q_correct_text
      .split(",")
      .map((s) => s.trim())
      .filter((s) => s.length > 0)
      .forEach((text, idx) => {
        items.push({
          id: idx + 1,
          text,
        });
      });
  }

  sortItems.value = items;
}

// Synca sortItems efter drag & drop
function updateSortOrderFromDom() {
  if (!sortList.value) return;
  const children = Array.from(sortList.value.children);
  const newOrder = [];

  children.forEach((li) => {
    const id = Number(li.getAttribute("data-id"));
    const found = sortItems.value.find((i) => i.id === id);
    if (found) {
      newOrder.push(found);
    }
  });

  if (newOrder.length === sortItems.value.length) {
    sortItems.value = newOrder;
  }
  emitAnswer();
}

// MATCH – högerkolumn (shufflad lista med texter)
const shuffledRightSide = computed(() => {
  if (!props.question || !Array.isArray(props.question.match_pairs)) {
    return [];
  }
  const base = props.question.match_pairs.map((p) => ({
    value: p.mp_id,
    label: p.mp_right_text,
  }));
  // enkel shuffle (Fisher–Yates)
  const arr = [...base];
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
});

function onMatchChange(leftId, rightId) {
  matchSelections.value = {
    ...matchSelections.value,
    [leftId]: Number(rightId),
  };
  emitAnswer();
}

// Bygg payload som skickas till QuizView
function buildAnswerPayload() {
  const q = props.question;
  const base = {
    q_id: q.q_id,
    type: q.q_type,
  };

  if (q.q_type === "single") {
    return {
      ...base,
      answer_ids: selectedSingle.value ? [Number(selectedSingle.value)] : [],
    };
  }

  if (q.q_type === "multiple") {
    return {
      ...base,
      answer_ids: selectedMultiple.value.map((id) => Number(id)),
    };
  }

  if (q.q_type === "text") {
    return {
      ...base,
      text: textAnswer.value,
    };
  }

  if (q.q_type === "sort") {
    return {
      ...base,
      order: sortItems.value.map((item) => item.text),
    };
  }

  if (q.q_type === "match") {
    return {
      ...base,
      matches: matchSelections.value,
    };
  }

  return base;
}

function emitAnswer() {
  emit("answer", buildAnswerPayload());
}

// --- WATCH QUESTION --------------------------

watch(
  () => props.question,
  async (newQ) => {
    resetState();

    if (!newQ) return;

    // Sort-förberedelse
    if (newQ.q_type === "sort") {
      initSortItems(newQ);
      await nextTick();
      if (sortList.value) {
        sortableInstance.value = Sortable.create(sortList.value, {
          animation: 150,
          handle: ".sort-item",
          onEnd() {
            updateSortOrderFromDom();
          },
        });
      }
    }

    // Match-förberedelse (töm val)
    if (newQ.q_type === "match") {
      matchSelections.value = {};
    }

    // Emitta tomt svar initialt så QuizView vet att frågan är laddad
    emitAnswer();
  },
  { immediate: true }
);

// --- WATCHERS FÖR ATT EMITTA VID ÄNDRING -----

watch(selectedSingle, emitAnswer);
watch(selectedMultiple, emitAnswer, { deep: true });
watch(textAnswer, emitAnswer);
watch(sortItems, emitAnswer, { deep: true });
watch(
  () => matchSelections.value,
  emitAnswer,
  { deep: true }
);
</script>
