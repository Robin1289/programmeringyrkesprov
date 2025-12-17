<template>
  <div class="logout-overlay">
    <div class="logout-popup">
      <h2>Farväl, {{ username }}! ❤️</h2>

      <video autoplay muted class="logout-video">
        <source src="/media/logout.mp4" type="video/mp4" />
      </video>

      <audio autoplay>
        <source src="/media/tf_nemesis.mp3" type="audio/mpeg" />
      </audio>

      <div class="hearts-container">
        <span
          v-for="n in 20"
          :key="n"
          class="heart"
          :style="{
            left: Math.random() * 100 + '%',
            animationDelay: Math.random() * 1.5 + 's',
            fontSize: 12 + Math.random() * 18 + 'px'
          }"
        >❤️</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

const emit = defineEmits(['close'])

defineProps({
  username: String
})

onMounted(() => {
  setTimeout(() => {
    emit('close')
  }, 2500)
})
</script>

<style scoped>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes floatHearts {
  from { transform: translateY(0) scale(1); opacity: 1; }
  to { transform: translateY(-150px) scale(1.5); opacity: 0; }
}

.heart {
  position: absolute;
  bottom: 0;
  animation: floatHearts 2s ease-out forwards;
}
</style>
