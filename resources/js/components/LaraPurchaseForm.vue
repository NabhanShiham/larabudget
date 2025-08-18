<template class="sm:max-w-[425px] mx-auto">
  <form @submit.prevent="submitPurchase" class="space-y-6">
    <div>
      <Label for="amount" class="space-y-3 text-center">Amount</Label>
      <Input
        id="amount"
        v-model="form.amount"
        type="number"
        step="0.01"
        min="0.01"
        required
      />
    </div>

    <div>
      <Label for="description" class="space-y-3 text-center">Description</Label>
      <Input
        id="description"
        v-model="form.description"
        placeholder="What was this purchase for?"
      />
    </div>

    <div>
      <Label for="date" class="space-y-3 text-center">Transaction Date</Label>
      <Input
        id="date"
        v-model="form.transaction_date"
        type="date"
        :max="new Date().toISOString().split('T')[0]"
      />
    </div>

    <div>
      <Label for="receipt" class="space-y-3 text-center">Receipt (Optional)</Label>
      <Input
        id="receipt"
        type="file"
        accept="image/*,.pdf"
        @change="handleFileUpload"
      />
      <p class="text-xs text-muted-foreground mt-1">
        Upload a photo or PDF of your receipt
      </p>
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <Button
        type="button"
        variant="outline"
        @click="$emit('cancel'), refreshProfile"
      >
        Cancel
      </Button>
      <Button type="submit" :disabled="form.processing">
        <Spinner v-if="form.processing" class="mr-2 h-4 w-4" />
        Save Purchase
      </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Label } from 'reka-ui'
import {Input } from '@/components/ui/input'
import {Button} from '@/components/ui/button'
import Spinner from './ui/spinner/Spinner.vue'

const props = defineProps({
  categoryId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['purchase-created', 'cancel'])

const form = useForm({
  amount: 0,
  description: '',
  transaction_date: new Date().toISOString().split('T')[0],
  receipt: null as File | null,
  category_id: props.categoryId
})

const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement
  if (input.files && input.files[0]) {
    form.receipt = input.files[0]
  }
}

const submitPurchase = () => {
  form.post(route('purchases.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      emit('purchase-created')
    refreshProfile();
    }
  })
}

const refreshProfile = () => {
  window.location.reload();
}
</script>