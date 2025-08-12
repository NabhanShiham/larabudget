<template>
  <div class="space-y-6">
    <HeadingSmall title="Your Budget Profile" />
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
      <!-- Budget Summary -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <BudgetCard 
          title="Main Budget" 
          :amount="profile.mainbudget" 
          icon="dollar"
        />
        <BudgetCard 
          title="Current Spending" 
          :amount="profile.currentspent" 
          icon="shopping-cart"
        />
        <BudgetCard 
          title="Remaining" 
          :amount="profile.remaining" 
          :is-remaining="true"
          icon="wallet"
        />
      </div>

      <!-- Progress Bar -->
      <div class="mb-6">
        <h3 class="text-lg font-medium mb-2">Budget Usage</h3>
        <ProgressBar 
          :value="(profile.currentspent / profile.mainbudget) * 100"
        />
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          {{ Math.round((profile.currentspent / profile.mainbudget) * 100) }}% of budget used
        </p>
      </div>

      <!-- Last Updated -->
      <div class="text-sm text-gray-500 dark:text-gray-400">
        Last updated: {{ formatDate(profile.updated_at) }}
      </div>
    </div>

    <Link 
      :href="route('larabudget.edit')" 
      class="inline-flex items-center text-blue-600 hover:text-blue-800"
    >
      Edit Profile
    </Link>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import HeadingSmall from '@/components/HeadingSmall.vue'
import BudgetCard from '@/components/LaraBudgetCard.vue'
import ProgressBar from '@/components/LaraProgressBar.vue'

const props = defineProps({
  profile: {
    type: Object,
    required: true,
    default: () => ({
      mainbudget: 0,
      currentspent: 0,
      remaining: 0,
      updated_at: null
    })
  }
})

const formatDate = (dateString) => {
  if (!dateString) return 'Never'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>