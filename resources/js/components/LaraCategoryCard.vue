<template>
  <div class="border rounded-lg p-4" :class="{
    'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800': isUnderBudget,
    'border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800': isOverBudget,
    'border-gray-200 dark:border-gray-700': !isUnderBudget && !isOverBudget
  }">
    <!-- Header Slot -->
    <div class="flex items-center justify-between">
      <h3 class="font-medium text-gray-700 dark:text-gray-300">
        <slot name="title">{{ category.name }}</slot>
      </h3>
      <Icon 
        :name="icon" 
        :class="[
          'w-5 h-5',
          isUnderBudget ? 'text-green-500 dark:text-green-400' : '',
          isOverBudget ? 'text-red-500 dark:text-red-400' : '',
          (!isUnderBudget && !isOverBudget) ? 'text-gray-400' : ''
        ].filter(Boolean).join(' ')" 
      />
    </div>

    <!-- Budget Info -->
    <div class="mt-3 space-y-2">
      <div class="flex justify-between text-sm">
        <span class="text-muted-foreground">Budgeted</span>
        <span>${{ category.budgeted_amount.toFixed(2) }}</span>
      </div>
      
      <div class="flex justify-between text-sm">
        <span class="text-muted-foreground">Spent</span>
        <span>${{ category.current_spent.toFixed(2) }}</span>
      </div>
      
      <div class="flex justify-between text-sm font-medium">
        <span>Remaining</span>
        <span :class="{
          'text-green-600 dark:text-green-400': isUnderBudget,
          'text-red-600 dark:text-red-400': isOverBudget
        }">
          ${{ remainingAmount.toFixed(2) }}
        </span>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="mt-4">
      <div class="relative h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
        <div
          class="h-full rounded-full"
          :class="{
            'bg-green-500': isUnderBudget,
            'bg-red-500': isOverBudget,
            'bg-primary': !isUnderBudget && !isOverBudget
          }"
          :style="{ width: `${spentPercentage}%` }"
        />
      </div>
      <p class="mt-1 text-right text-xs text-muted-foreground">
        {{ spentPercentage.toFixed(1) }}%
      </p>
    </div>

    <!-- Add Purchase Button -->
    <div class="mt-4">
      <Dialog>
        <DialogTrigger as-child>
          <Button class="w-full">
            <PlusIcon class="w-4 h-4 mr-2" />
            Add Purchase
          </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[500px]">
          <DialogHeader>
            <DialogTitle>New Purchase for {{ category.name }}</DialogTitle>
            <DialogDescription>
              Record a new purchase and upload receipt
            </DialogDescription>
          </DialogHeader>
          
          <PurchaseForm 
            :category-id="category.id" 
            @purchase-created="handlePurchaseCreated"
          />
        </DialogContent>
      </Dialog>
    </div>

    <!-- Recent Purchases -->
    <div class="mt-4 space-y-2" v-if="recentPurchases.length > 0">
      <h4 class="text-sm font-medium">Recent Purchases</h4>
      <div v-for="purchase in recentPurchases" :key="purchase.id" class="text-sm">
        <div class="flex justify-between items-center py-1">
          <div>
            <span class="font-medium">${{ purchase.amount.toFixed(2) }}</span>
            <span class="text-muted-foreground ml-2">{{ purchase.description || 'No description' }}</span>
          </div>
          <span class="text-xs text-muted-foreground">
            {{ formatDate(purchase.transaction_date || purchase.created_at) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { PlusIcon } from 'lucide-vue-next'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import PurchaseForm from '@/components/LaraPurchaseForm.vue'
import Icon from '@/components/Icon.vue'
import { computed } from 'vue'

const props = defineProps({
  category: {
    type: Object as () => {
      id: number
      name: string
      budgeted_amount: number
      current_spent: number
    },
    required: true
  },
  icon: {
    type: String,
    default: 'tag'
  },
  recentPurchases: {
    type: Array as () => Array<{
      id: number
      amount: number
      description: string
      transaction_date: string
      created_at: string
    }>,
    default: () => []
  }
})

const emit = defineEmits(['purchase-created'])

const remainingAmount = computed(() => {
  return props.category.budgeted_amount - props.category.current_spent
})

const spentPercentage = computed(() => {
  return Math.min(100, 
    (props.category.current_spent / props.category.budgeted_amount) * 100
)})

const isUnderBudget = computed(() => {
  return spentPercentage.value < 70
})

const isOverBudget = computed(() => {
  return props.category.current_spent > props.category.budgeted_amount
})

const handlePurchaseCreated = (updatedCategory: typeof props.category) => {
  emit('purchase-created', updatedCategory)
  refreshProfile
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const refreshProfile = () => {
  window.location.reload();
}
</script>