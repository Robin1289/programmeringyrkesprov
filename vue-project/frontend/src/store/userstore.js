import { defineStore } from 'pinia'
import router from '../router'
import BadgePopup from '@/components/BadgePopup.vue';

export const useUserStore = defineStore('user', {
  state: () => ({
    id: null,
    name: '',
    email: '',
    points: 0,
    level: 1,
    role: null,       // IMPORTANT: no default admin role
    badges: [],
    BadgePopup: null,
    isLoggedIn: false,
    ready: false
  }),

  getters: {
    // Allow router/user code to access "user.role"
    user(state) {
      return {
        id: state.id,
        name: state.name,
        email: state.email,
        points: state.points,
        level: state.level,
        role: state.role,
        badges: state.badges
      }
    }
  },

  actions: {
 
    // LOGIN

    async login(credentials) {
      try {
        const response = await fetch(
          "http://localhost/yrkesprov/vue-project/backend/api/login.php",
          {
            method: "POST",
            credentials: "include",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              email: credentials.email,
              password: credentials.password
            })
          }
        );

        const data = await response.json();

        if (!data.success) {
          throw new Error(data.message);
        }

        this.id    = data.user.id;
        this.name  = data.user.name;
        this.email = data.user.email;
        this.points = data.user.points;
        this.level  = data.user.level;
        this.role   = data.user.role;
        this.badges = data.user.badges || [];

        this.isLoggedIn = true;
        return { success: true };

      } catch (err) {
        console.error("Login error:", err);
        throw err;
      }
    },


    // REGISTER

    async register(name, email, password) {
      try {
        const response = await fetch(
          'http://localhost/yrkesprov/vue-project/backend/api/register.php',
          {
            method: 'POST',
            credentials: 'include',
            body: new URLSearchParams({ name, email, password })
          }
        );
        return await response.json();
      } catch (err) {
        console.error(err);
        return { success: false, message: 'Nätverksfel. Försök igen.' };
      }
    },


    // LOGOUT

    async logout() {
      try {
        await fetch(
          'http://localhost/yrkesprov/vue-project/backend/api/users.php',
          {
            method: 'POST',
            credentials: 'include',
            body: new URLSearchParams({ action: 'logout' })
          }
        );
      } catch (err) {
        console.error(err);
      } finally {
        this.$reset();
        this.isLoggedIn = false;
        router.push('/login');
      }
    },

    // FETCH USER

    async fetchUser() {
      this.ready = false;

      try {
        const response = await fetch(
          'http://localhost/yrkesprov/vue-project/backend/api/users.php?action=getUser',
          { credentials: 'include' }
        );

        const data = await response.json();

        if (data.success && data.user) {
          this.id = data.user.id;
          this.name = data.user.name;
          this.email = data.user.email;
          this.points = data.user.points;
          this.level = data.user.level;
          this.role = data.user.role;
          this.badges = data.user.badges || [];

          this.isLoggedIn = true;
        } else {
          this.isLoggedIn = false;
        }

      } catch (err) {
        console.error(err);
        this.isLoggedIn = false;
      } finally {
        this.ready = true;
      }
    },

    // HELPERS

    updatePoints(points) {
      this.points = points;
    },

    updateLevel(level) {
      this.level = level;
    },

    addBadge(badge) {
      if (!this.badges.includes(badge)) {
        this.badges.push(badge);
      }
    }
    showBadge(badge) {
      this.badgePopup = badge;
      setTimeout(() => {
        this.badgePopup = null;
      }, 3500);
    },
  }
})
