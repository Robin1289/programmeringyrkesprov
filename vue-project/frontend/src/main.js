import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useUserStore } from './store/userstore.js'
import '../public/assets/styles.css'

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)

// Restore session before mounting
const userStore = useUserStore()
await userStore.fetchUser()  // ensures isLoggedIn is correct

app.use(router)
app.mount('#app')



