<template>
  <button class="btn btn-outline-secondary mb-3" @click="goBack">
    ← Back
  </button>
</template>

<script>
export default {
  name: "BackButton",
  props: {
    to: {
      type: [String, Object],
      required: true
    }
  },
  methods: {
    goBack() {
      this.$router.push(this.to);
    }
  }
};
</script>
