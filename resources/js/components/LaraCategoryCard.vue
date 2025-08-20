<template>
  <div class="border rounded-lg p-1 w-86" :class="{
    'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800': isUnderBudget,
    'border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800': isOverBudget,
    'border-gray-200 dark:border-gray-700': !isUnderBudget && !isOverBudget
  }">

    <div class="flex items-center justify-between">
      <h3 class="font-medium text-gray-700 dark:text-gray-300">
        <slot name="title">{{ category.name }}</slot>
      </h3>
<Dialog>
  <DialogTrigger as-child>
<div class="flex justify-end">
  <Button class="flex items-center bg-red-50 hover:bg-red-300 text-red-600 hover:text-red-800 px-2 py-0.5 rounded-md shadow-sm transition-colors">
      <TrashIcon class="w-4 h-4 mr-0.02"/>
    </Button>
</div>
</DialogTrigger>
<DialogContent class="sm:max-w-[500px]">
          <DialogHeader>
            <DialogTitle>Are you sure about deleting {{ category.name }}?</DialogTitle>
            <DialogDescription>
              This action cannot be reversed
            </DialogDescription>
          </DialogHeader>
            <Button class="flex items-center bg-red-50 hover:bg-red-300 text-red-600 hover:text-red-800 px-2 py-0.5 rounded-md shadow-sm transition-colors" @click="handleCategoryDelete" :disabled="!category?.id">
      <TrashIcon class="w-4 h-4 mr-0.02"/>
    </Button>
        </DialogContent>
</Dialog>
    </div>

    <div class="mt-3 space-y-2">
      <div class="flex justify-between text-sm">
        <span class="text-muted-foreground">Budgeted</span>
        <span>Rf {{ category.budgeted_amount.toFixed(2) }}</span>
      </div>
      
      <div class="flex justify-between text-sm">
        <span class="text-muted-foreground">Spent</span>
        <span>Rf {{ category.current_spent.toFixed(2) }}</span>
      </div>
      
      <div class="flex justify-between text-sm font-medium">
        <span>Remaining</span>
        <span :class="{
          'text-green-600 dark:text-green-400': isUnderBudget,
          'text-red-600 dark:text-red-400': isOverBudget
        }">
          Rf {{ remainingAmount.toFixed(2) }}
        </span>
      </div>
    </div>

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
<Dialog>
      <DialogTrigger as-child>
        <Button class="w-full">
          <Paperclip class="w-4 h-4 mr-2" />
          View Purchases ({{ filteredPurchases.length }})
        </Button>
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Receipts for {{ category.name }}</DialogTitle>
          <DialogDescription v-if="filteredPurchases.length === 0">
            No receipts available
          </DialogDescription>
        </DialogHeader>
        
        <div class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto">
          <div v-for="purchase in recentPurchases" :key="purchase.id" 
               class="p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <div class="flex justify-between items-center">
              <div>
                <p class="font-medium">Rf {{ purchase.amount }}</p>
                <p class="text-sm text-muted-foreground">
                  {{ purchase.description || 'No description' }}
                </p>
                <p class="text-xs text-muted-foreground mt-1">
                  {{ formatDate(purchase.transaction_date || purchase.created_at) }}
                </p>
              </div>
              <Button 
                v-if="purchase.receipt_path" 
                variant="outline" 
                size="sm"
                @click="viewReceipt(purchase)"
              >
                <EyeIcon class="h-4 w-4 mr-1" />
                View
              </Button>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
    <Dialog v-model:open="isReceiptDialogOpen">
    <DialogContent class="sm:max-w-[90vw] max-h-[90vh]">
      <div class="flex justify-between items-center mb-4">
        <DialogTitle>Receipt</DialogTitle>
        <Button variant="outline" size="sm" @click="downloadReceipt(currentReceipt)">
          <DownloadIcon class="h-4 w-4 mr-1" />
          Download
        </Button>
      </div>
      
      <div class="flex justify-center overflow-auto">
        <iframe
          v-if="currentReceipt && currentReceipt.endsWith('.pdf')"
          :src="currentReceipt"
          class="w-full h-[70vh] border"
          frameborder="0"
        />

        <img 
          v-else-if="currentReceipt"
          :src="currentReceipt"
          class="max-w-full max-h-[70vh] object-contain"
          alt="Receipt image"
        />
      </div>
    </DialogContent>
  </Dialog>

    <div class="mt-4">
      <Dialog>
        <DialogTrigger as-child>
          <Button class="w-full">
            <PlusIcon class="w-4 h-4 mr-2 " />
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
          
          <LaraPurchaseForm 
            :category-id="category.id" 
            @purchase-created="handlePurchaseCreated"
          />
        </DialogContent>
      </Dialog>
    </div>

<div class="mt-4 space-y-2" v-if="recentPurchases.length > 0">
      <h4 class="text-sm font-medium">Recent Activity</h4>
      <div v-for="purchase in recentPurchases.slice(0, 2)" :key="purchase.id" class="text-sm">
        <div class="flex justify-between items-center py-1">
          <div class="flex items-center">
            <span class="font-medium">Rf {{ purchase.amount }}</span>
            <span class="text-muted-foreground ml-2 truncate max-w-[100px]">
              {{ purchase.description || 'No description' }}
            </span>
          </div>
          <span class="text-xs text-muted-foreground whitespace-nowrap">
            {{ formatDate(purchase.transaction_date || purchase.created_at) }}
          </span>
        </div>
      </div>
      <p v-if="recentPurchases.length > 2" class="text-xs text-muted-foreground text-center mt-1">
        +{{ recentPurchases.length - 2 }} more
      </p>
    </div>
  </div>


</template>

<script setup lang="ts">
import { Paperclip, PlusIcon, TrashIcon, EyeIcon, DownloadIcon } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import LaraPurchaseForm from './LaraPurchaseForm.vue';
import axios from 'axios';

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
  recentPurchases: {
    type: Array as () => Array<{
      id: number
      amount: number
      description: string
      transaction_date: string
      created_at: string
      receipt_path: string | null
    }>,
    default: () => []
  }
})

const isReceiptDialogOpen = ref(false)
const currentReceipt = ref('')
const isAddPurchaseDialogOpen = ref(false)
const showAllPurchases = ref(false)

const filteredPurchases = computed(() => {
  return props.recentPurchases || []
})

const visiblePurchases = computed(() => {
  return showAllPurchases.value 
    ? filteredPurchases.value 
    : filteredPurchases.value.slice(0, 2)
})

const hasMorePurchases = computed(() => {
  return filteredPurchases.value.length > 2
})

const hiddenPurchasesCount = computed(() => {
  return filteredPurchases.value.length - 2
})

const getFilename = (path: string): string => {
  return path.split('/').pop() || ''
}

const viewReceipt = (purchase: Object) => {
  // console.log(purchase)
  // const url = route(`purchases.receipt.view`, receipt_path)
  // const resp = router.visit('/get-purchase-receipt/' + receipt_path)
  // console.log(resp)
  // currentReceipt.value = route(`purchases.receipt.view`, receipt_path)
  // console.log(currentReceipt.value)
  axios.get('/get-receipt/' + purchase.id)
  .then(response => {
    // console.log(response.data)
    currentReceipt.value = response.data.url
    console.log(currentReceipt.value)
  })

  isReceiptDialogOpen.value = true;
}

const downloadReceipt = (receiptPath: string) => {
  const link = document.createElement('a')
  link.href = receiptPath
  link.download = receiptPath.split('/').pop() || 'receipt'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const emit = defineEmits(['purchase-created', 'deleted'])

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
}

const handleCategoryDelete = () => {
        router.delete(route('category.delete', { category: props.category.id }), {
            onSuccess: () => {
                emit('deleted');
                refreshProfile();
            },
            onError: (errors) => {
                console.error('Delete failed:', errors);
            }
        });
};

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