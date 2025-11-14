import { defineStore } from 'pinia'
import router from '../router'   // IMPORTANT: make sure this path is correct

export const useUserStore = defineStore('user', {
  state: () => ({
    id: null,
    name: '',
    email: '',
    points: 0,
    level: 1,
    role: 1,            // <-- You forgot this!
    badges: [],
    isLoggedIn: false,
    ready: false
  }),

  actions: {

    // ------------------------------------------------------
    // LOGIN
    // ------------------------------------------------------
    async login(credentials) {
      try {
        const response = await fetch(
          "http://localhost/yrkesprov/vue-project/backend/api/login.php",
          {
            method: "POST",
            credentials: "include",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              email: credentials.email,   // email OR username
              password: credentials.password
            })
          }
        );

        const data = await response.json();

        if (!data.success) {
          throw new Error(data.message);
        }

        // Backend returns:
        // id, name, email, points, level, role
        this.id    = data.user.id;
        this.name  = data.user.name;
        this.email = data.user.email;
        this.points = data.user.points;
        this.level  = data.user.level;
        this.role   = data.user.role;

        this.isLoggedIn = true;

        // ------------------------------------------------------
        // ROLE-BASED REDIRECT (Vue Router instead of page reload)
        // ------------------------------------------------------
        // Do NOT redirect here.
        // LoginView.vue will handle redirection AFTER popup.
        return { success: true }


      } catch (err) {
        console.error("Login error:", err);
        throw err; // sends error back to login page to display
      }
    },

    // ------------------------------------------------------
    // REGISTER
    // ------------------------------------------------------
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

    // ------------------------------------------------------
    // LOGOUT
    // ------------------------------------------------------
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
        // Reset state
        this.$reset();
        this.isLoggedIn = false;
        router.push('/login');
      }
    },

    // ------------------------------------------------------
    // FETCH USER (SESSION CHECK)
    // ------------------------------------------------------
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

    // ------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------
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
  }
});
