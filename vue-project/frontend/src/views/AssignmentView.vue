  <template>
    <div class="dashboard-container">

      <div class="assignments-header-wrapper">
        <h1 class="assignments-header">Assignments</h1>
        <p class="assignments-subtitle">
          Your current level: <span class="user-level">{{ realLevel }}</span>
        </p>
      </div>

      <div v-if="loading" class="text-center assignments-loading">
        Loading assignments...
      </div>

      <div v-else>

        <!-- ✦ INCOMPLETE QUIZZES -->
        <h2 class="mb-3">Uppgifter du inte gjort</h2>

        <div v-if="incomplete.length === 0" class="text-muted mb-4">
          Du har gjort alla uppgifter på din nivå! 🎉
        </div>

        <div class="row assignments-grid">
          <div
            class="col-md-6 col-lg-4 mb-3"
            v-for="quiz in incomplete"
            :key="quiz.quiz_id"
          >
            <AssignmentCard :quiz="quiz" :user-level="realLevel" />
          </div>
        </div>

        <hr class="my-4" />

        <hr class="my-4" />

        <h2 class="mb-3 text-danger">Misslyckade uppgifter ❌</h2>

        <div v-if="failed.length === 0" class="text-muted">
          Inga misslyckade uppgifter just nu.
        </div>

        <div class="row assignments-grid">
          <div
            class="col-md-6 col-lg-4 mb-3"
            v-for="quiz in failed"
            :key="quiz.quiz_id"
          >
            <AssignmentCard :quiz="quiz" :user-level="realLevel" />
          </div>
        </div>

        <hr class="my-4" />


        <!-- ✦ COMPLETED QUIZZES -->
        <h2 class="mb-3">Avklarade uppgifter</h2>

        <div v-if="completed.length === 0" class="text-muted">
          Du har inte gjort några uppgifter ännu.
        </div>

        <div class="row assignments-grid">
          <div
            class="col-md-6 col-lg-4 mb-3"
            v-for="quiz in completed"
            :key="quiz.quiz_id"
          >
            <AssignmentCard :quiz="quiz" :user-level="realLevel" />
          </div>
        </div>

      </div>

    </div>
  </template>

  <script setup>
  import { ref, onMounted } from "vue"
  import AssignmentCard from "../components/Assignmentcard.vue"
  import { useUserStore } from "../store/userstore.js"

  const userStore = useUserStore()

  const assignments = ref([])
  const incomplete = ref([])
  const completed = ref([])
  const failed = ref([])
  const loading = ref(true)
  const realLevel = ref(1)

  async function fetchAssignments() {
    try {
      const res = await fetch(
        "http://localhost/yrkesprov/vue-project/backend/api/assignment.php",
        { credentials: "include" }
      )

      const data = await res.json()

      if (data.success) {
        realLevel.value = data.user_real_level

        incomplete.value = data.quizzes.filter(q =>
          !q.completed && !q.failed
        )

        failed.value = data.quizzes.filter(q =>
          q.failed
        )

        completed.value = data.quizzes.filter(q =>
          q.completed
        )

      }

    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }





  onMounted(async () => {
    await fetch("http://localhost/yrkesprov/vue-project/backend/api/level_up.php", {
      method: "POST",
      credentials: "include"
    })

    if (!userStore.isLoggedIn) {
      await userStore.fetchUser()
    }

    await fetchAssignments()
  })
  </script>
