<template>
  <button class="kitty-back-btn mb-3" @click="goBack">
    ← Tillbaka
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
