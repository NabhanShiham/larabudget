<template>
  <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
    <div 
      class="bg-blue-600 h-2.5 rounded-full" 
      :style="`width: ${value}%`"
      :class="{
        'bg-red-500': value > 80,
        'bg-yellow-500': value > 50 && value <= 80
      }"
    ></div>
  </div>
</template>

<script setup>
defineProps({
  value: {
    type: Number,
    required: true,
    validator: value => value >= 0 && value <= 100
  }
})
</script>