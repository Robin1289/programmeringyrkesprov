import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import '../public/assets/styles.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// ✔️ Wait for user session AFTER app + pinia are ready
import { useUserStore } from './store/userstore.js'

async function init() {
  const userStore = useUserStore()
  await userStore.fetchUser()

  app.mount('#app')
}

init()
