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
    ready: false
  }),

  actions: {
    async login(email, password) {
      try {
        const response = await fetch(
          'http://localhost/yrkesprov/vue-project/backend/api/login.php',
          {
            method: 'POST',
            credentials: 'include',
            body: new URLSearchParams({ email, password })
          }
        )
        const data = await response.json()

        if (data.success) {
          this.id = data.user.id
          this.name = data.user.name
          this.email = data.user.email
          this.points = data.user.points || 0
          this.level = data.user.level || 1
          this.badges = data.user.badges || []
          this.isLoggedIn = true
        }

        return data
      } catch (err) {
        console.error(err)
        return { success: false, message: 'Något gick fel, försök igen.' }
      }
    },

    async register(name, email, password) {
      try {
        const response = await fetch(
          'http://localhost/yrkesprov/vue-project/backend/api/register.php',
          {
            method: 'POST',
            credentials: 'include',
            body: new URLSearchParams({ name, email, password })
          }
        )
        return await response.json()
      } catch (err) {
        console.error(err)
        return { success: false, message: 'Nätverksfel. Försök igen.' }
      }
    },

    async logout() {
      try {
        await fetch('http://localhost/yrkesprov/vue-project/backend/api/users.php', {
          method: 'POST',
          credentials: 'include',
          body: new URLSearchParams({ action: 'logout' })
        })
      } catch (err) {
        console.error(err)
      } finally {
        // reset state immediately
        this.id = null
        this.name = ''
        this.email = ''
        this.points = 0
        this.level = 1
        this.badges = []
        this.isLoggedIn = false
      }
    },

    async fetchUser() {
      this.ready = false
      try {
        const response = await fetch(
          'http://localhost/yrkesprov/vue-project/backend/api/users.php?action=getUser',
          { credentials: 'include' }
        )
        const data = await response.json()

        if (data.success && data.user) {
          this.id = data.user.id
          this.name = data.user.name
          this.email = data.user.email
          this.points = data.user.points || 0
          this.level = data.user.level || 1
          this.badges = data.user.badges || []
          this.isLoggedIn = true
        } else {
          this.isLoggedIn = false
        }
      } catch (err) {
        console.error(err)
        this.isLoggedIn = false
      } finally {
        this.ready = true
      }
    },

    updatePoints(points) {
      this.points = points
    },

    updateLevel(level) {
      this.level = level
    },

    addBadge(badge) {
      if (!this.badges.includes(badge)) this.badges.push(badge)
    }
  }
})
