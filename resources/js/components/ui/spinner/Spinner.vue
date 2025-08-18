<template>
  <div class="spinner" :style="{ width: size + 'px', height: size + 'px' }">
    <div
      class="spinner-sector"
      v-for="(color, index) in colors"
      :key="index"
      :style="{
        'border-color': color,
        'border-width': borderWidth + 'px',
        'animation-delay': index * delay + 's',
      }"
    ></div>
  </div>
</template>

<script setup>
const props = defineProps({
  size: {
    type: Number,
    default: 40,
  },
  borderWidth: {
    type: Number,
    default: 4,
  },
  colors: {
    type: Array,
    default: () => ["#3b82f6", "#e5e7eb", "#e5e7eb"], // blue + gray
  },
  delay: {
    type: Number,
    default: 0.1, 
  },
});
</script>

<style scoped>
.spinner {
  position: relative;
  display: inline-block;
}

.spinner-sector {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border-style: solid;
  border-top-color: transparent;
  border-left-color: transparent;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>