<template>
  <div class="logout-overlay">
    <div class="logout-popup">
      <h2>Farväl, {{ username }}! ❤️</h2>

      <!-- Video if provided -->
      <video
        v-if="videoSrc"
        autoplay
        muted
        class="logout-video"
      >
        <source :src="videoSrc" type="video/mp4" />
        Din webbläsare stöder inte videotaggen.
      </video>

      <!-- Sound if provided -->
      <audio v-if="soundSrc" autoplay>
        <source :src="soundSrc" type="audio/mpeg" />
      </audio>

      <!-- Hearts effect -->
      <div class="hearts-container">
        <span
          v-for="n in 20"
          :key="n"
          class="heart"
          :style="{
            left: Math.random() * 100 + '%',
            animationDelay: (Math.random() * 1.5) + 's',
            fontSize: (12 + Math.random() * 18) + 'px'
          }"
        >❤️</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

const props = defineProps({
  username: String,
  videoSrc: String,
  soundSrc: String
})

const emit = defineEmits(['close'])

onMounted(() => {
  setTimeout(() => {
    emit('close')
  }, 2500) // closes automatically after 2.5s
})
</script>

<style scoped>


@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes floatHearts {
  0% {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
  100% {
    transform: translateY(-150px) scale(1.5); /* float upwards */
    opacity: 0;
  }
}
.heart {
  position: absolute;
  bottom: 0; /* start at bottom of popup */
  animation: floatHearts 2s ease-out forwards;
}
</style>
