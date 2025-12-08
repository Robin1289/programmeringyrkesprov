<template>
  <div class="xp-popup-overlay">
    <div class="xp-popup-card">
      <div class="xp-popup-header">
        <span class="xp-kitty-icon">💗</span>
        <h2>XP bonus!</h2>
      </div>

      <p class="xp-popup-text">
        Du fick <strong>{{ xpGained }}</strong> XP från detta quiz.
      </p>

      <div class="xp-bar-wrapper">
        <div class="xp-bar-label">Nivåframsteg</div>
        <div class="xp-bar-track">
          <div
            class="xp-bar-fill"
            :style="{ width: fillWidth + '%' }"
          ></div>
        </div>
        <div class="xp-bar-caption">
          <span v-if="nextLevelPoints">
            {{ currentPoints }} / {{ nextLevelPoints }} XP till nästa nivå
          </span>
          <span v-else>
            Maxnivå nådd
          </span>
        </div>
      </div>

      <div v-if="levelsGained > 0" class="xp-levelup-box">
        <p class="xp-levelup-main">
          Nivå upp! Du steg {{ levelsGained }} nivå<span v-if="levelsGained > 1">er</span>.
        </p>
        <p class="xp-levelup-sub">
          Nuvarande nivå: <strong>{{ newLevelName }}</strong>
        </p>
      </div>

      <button class="btn kitty-btn xp-popup-button" @click="$emit('close')">
        Visa resultat 🎀
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";

const props = defineProps({
  xpGained: { type: Number, required: true },
  levelsGained: { type: Number, required: true },
  newLevelName: { type: String, required: true },
  currentPoints: { type: Number, required: true },
  nextLevelPoints: { type: [Number, null], default: null }
});

const fillWidth = ref(0);

onMounted(async () => {
  await nextTick();
  let target = 100;
  if (props.nextLevelPoints && props.nextLevelPoints > 0) {
    target = Math.min(100, Math.round((props.currentPoints / props.nextLevelPoints) * 100));
  }
  setTimeout(() => {
    fillWidth.value = target;
  }, 80);
});
</script>
