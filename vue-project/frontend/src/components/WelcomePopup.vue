<template>
  <div class="welcome-overlay">
    <div class="welcome-popup">
      <h2>Välkommen tillbaka, {{ username }}! 🎉</h2>

      <video
        autoplay
        muted
        class="welcome-video"
      >
        <source :src="videoSrc" type="video/mp4" />
        Din webbläsare stöder inte videotaggen.
      </video>

      <audio autoplay>
        <source :src="soundSrc" type="audio/mpeg" />
      </audio>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import confetti from 'canvas-confetti'

const emit = defineEmits(['close'])
const props = defineProps({
  username: String
})

// Use **relative paths** pointing to where your media folder is relative to index.html
const videoSrc = './media/cutevid.mp4'
const soundSrc = './media/uwu.mp3'

onMounted(() => {
  runConfetti()

  setTimeout(() => {
    emit('close')
  }, 3000)
})

function runConfetti() {
  const duration = 1.5 * 1000
  const animationEnd = Date.now() + duration
  const defaults = { startVelocity: 25, spread: 360, ticks: 60, zIndex: 3000 }

  function randomInRange(min, max) {
    return Math.random() * (max - min) + min
  }

  const interval = setInterval(function () {
    const timeLeft = animationEnd - Date.now()
    if (timeLeft <= 0) return clearInterval(interval)

    const particleCount = 60 * (timeLeft / duration)
    confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } })
    confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } })
  }, 250)
}
</script>

<style scoped>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
