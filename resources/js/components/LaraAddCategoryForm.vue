
<template>
  <div v-if="showForm" class="sm:max-w-[425px] mx-auto">
    <div class="bg-white dark:bg-black rounded-lg shadow-lg w-full max-w-md p-6">
      <form @submit.prevent="submitCategory" class="space-y-6">
        <div class="space-y-3 text-center">
          <h2 class="text-xl font-semibold">Add New Category</h2>
          <p class="text-sm text-gray-500">Define a new category for your budget tracking.</p>
        </div>

        <div class="grid gap-2 px-4">
          <label for="category">Category Name</label>
          <input
            id="category"
            v-model="categoryForm.name"
            placeholder="e.g. Groceries, Travel"
            class="border rounded px-3 py-2 w-full"
          />
          <p class="text-red-500 text-sm">{{ categoryForm.errors.name }}</p>
        </div>

        <div class="grid gap-2 px-4">
          <label for="budgetedAmount">Budgeted Amount</label>
          <input
            id="budgetedAmount"
            v-model="categoryForm.budgeted_amount"
            placeholder="Allowance"
            class="border rounded px-3 py-2 w-full"
          />
          <p class="text-red-500 text-sm">{{ categoryForm.errors.budgeted_amount }}</p>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="closeCategoryModal" class="px-4 py-2 dark:bg-red-400 bg-gray-300 rounded">
            Cancel
          </button>
          <button type="submit" @click="closeCategoryModal" :disabled="categoryForm.processing" class="px-4 py-2 dark:bg-white dark:text-black bg-blue-500 text-white rounded">
            Add Category
          </button>
        </div>
      </form>
    </div>
  </div>
</template>



<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { onMounted, ref, onUnmounted } from 'vue'

const showForm = ref(true)


const categoryForm = useForm({
  name: '',
  budgeted_amount: ''
})

const emit = defineEmits(['closed']);

const showDialog = ref(true); 


onMounted(() => {
  showDialog.value = true;
});

const submitCategory = () => {
  categoryForm.post(route('category.store'), {
    preserveScroll: true,
    onFinish: () => categoryForm.reset(),
  })
}
const closeCategoryModal = () => {
  // categoryForm.clearErrors()
  // categoryForm.reset()
  refreshProfile()
}
const refreshProfile = () => {
  window.location.reload();
}

onUnmounted(() => {
  emit('closed');
});

</script>

