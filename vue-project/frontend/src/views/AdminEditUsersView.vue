<template>
  <div>
    <h1>Edit User</h1>
    <p>User ID: {{ userId }}</p>

    <!-- Later we'll add user fields here -->
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const userId = route.params.id
</script>
