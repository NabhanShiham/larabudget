<template>
  <div class="border rounded-lg p-4" :class="{
    'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800': isRemaining,
    'border-gray-200 dark:border-gray-700': !isRemaining
  }">
    <div class="flex items-center justify-between">
      <h3 class="font-medium text-gray-700 dark:text-gray-300">{{ title }}</h3>
      <Icon :name="icon" class="w-5 h-5 text-gray-400" />
    </div>
    <p class="text-2xl font-bold mt-2" :class="{
      'text-green-600 dark:text-green-400': isRemaining,
      'text-gray-900 dark:text-white': !isRemaining
    }">
      ${{ amount.toFixed(2) }}
    </p>
  </div>
</template>

<script setup>
import Icon from '@/components/Icon.vue'

defineProps({
  title: String,
  amount: {
    type: Number,
    default: 0
  },
  icon: {
    type: String,
    default: 'dollar'
  },
  isRemaining: {
    type: Boolean,
    default: false
  }
})
</script>