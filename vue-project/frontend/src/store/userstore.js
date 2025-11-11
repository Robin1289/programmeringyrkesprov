import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    id: null,
    name: '',
    email: '',
    points: 0,
    level: 1,
    badges: [],
    isLoggedIn: false,
  }),

  actions: {
    // Logga in användaren
    async login(email, password) {
      try {
        const response = await fetch('http://localhost/backend/api/users.php', {
          method: 'POST',
          body: new URLSearchParams({
            action: 'login',
            email: email.trim(),
            password
          })
        })
        const data = await response.json()
        if (data.success) {
          this.id = data.user.id
          this.name = data.user.name
          this.email = data.user.email
          this.points = data.user.points || 0
          this.level = data.user.level || 1
          this.badges = data.user.badges || []
          this.isLoggedIn = true
          return true
        } else {
          return false
        }
      } catch (error) {
        console.error(error)
        return false
      }
    },

    // Registrera användare
    async register(name, email, password) {
      try {
        const response = await fetch('http://localhost/backend/api/users.php', {
          method: 'POST',
          body: new URLSearchParams({
            action: 'register',
            name: name.trim(),
            email: email.trim(),
            password
          })
        })
        const data = await response.json()
        return data
      } catch (error) {
        console.error(error)
        return { success: false, message: 'Något gick fel' }
      }
    },

    // Logga ut användaren
    async logout() {
      try {
        await fetch('http://localhost/backend/api/users.php', {
          method: 'POST',
          body: new URLSearchParams({ action: 'logout' })
        })
        this.id = null
        this.name = ''
        this.email = ''
        this.points = 0
        this.level = 1
        this.badges = []
        this.isLoggedIn = false
      } catch (error) {
        console.error(error)
      }
    },

    // Hämta aktuell användardata från servern
    async fetchUser() {
      if (!this.id) return
      try {
        const response = await fetch(`http://localhost/backend/api/users.php?action=getUser&id=${this.id}`)
        const data = await response.json()
        if (data.success) {
          this.name = data.user.name
          this.email = data.user.email
          this.points = data.user.points || 0
          this.level = data.user.level || 1
          this.badges = data.user.badges || []
          this.isLoggedIn = true
        } else {
          // Om användaren inte hittas, logga ut
          this.logout()
        }
      } catch (error) {
        console.error(error)
      }
    },

    // Uppdatera poäng
    updatePoints(points) {
      this.points = points
    },

    // Uppdatera level
    updateLevel(level) {
      this.level = level
    },

    // Lägg till badge
    addBadge(badge) {
      if (!this.badges.includes(badge)) {
        this.badges.push(badge)
      }
    }
  }
})
