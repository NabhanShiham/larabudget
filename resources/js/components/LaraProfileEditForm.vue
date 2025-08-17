<template>
  <div class="space-y-6">
    <HeadingSmall title="Edit Profile" description="Update your budget settings and personal info" />

    <form @submit.prevent="submitProfile" class="space-y-4">
      <!-- <div class="grid gap-2">
        <Label for="name">Name</Label>
        <Input id="name" v-model="profileForm.name" placeholder="Your name" />
        <InputError :message="profileForm.errors.name" />
      </div> -->

      <div class="grid gap-2">
        <Label for="mainbudget">Main Budget</Label>
        <Input id="mainbudget" type="number" v-model="profileForm.mainbudget" placeholder="Main budget amount" />
        <InputError :message="profileForm.errors.mainbudget" />
      </div>

      <div class="grid gap-2">
        <Label for="spending">Current Spending</Label>
        <Input id="spending" type="number" v-model="profileForm.spending" placeholder="Current spending" />
        <InputError :message="profileForm.errors.spending" />
      </div>

      <Button type="submit" :disabled="profileForm.processing">Save Changes</Button>
    </form>

    <Dialog>
      <DialogTrigger as-child>
        <Button variant="outline">Add Category</Button>
      </DialogTrigger>

      <DialogContent class="sm:max-w-[425px] mx-auto">
        <form @submit.prevent="submitCategory" class="space-y-6">
          <DialogHeader class="space-y-3 text-center">
            <DialogTitle>Add New Category</DialogTitle>
            <DialogDescription>Define a new category for your budget tracking.</DialogDescription>
          </DialogHeader>

          <div class="grid gap-2 px-4">
            <Label for="category">Category Name</Label>
            <Input id="category" v-model="categoryForm.name" placeholder="e.g. Groceries, Travel" />
            <InputError :message="categoryForm.errors.name" />
          </div>

          <div class="grid gap-2 px-4">
            <Label for="budgetedAmount">Budgeted Amount</Label>
            <Input id="budgetedAmount" v-model="categoryForm.budgeted_amount" placeholder="Allowance" />
            <InputError :message="categoryForm.errors.name" />
          </div>

          <DialogFooter class="gap-2 sm:justify-center">
            <DialogClose as-child>
              <Button variant="secondary" @click="closeCategoryModal, refreshProfile">Cancel</Button>
            </DialogClose>
            <Button type="submit" :disabled="categoryForm.processing">Add Category</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import HeadingSmall from '@/components/HeadingSmall.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps({
    profile: {
        type: Object,
        required: true,
        default: () => ({
            mainbudget: 0,
            spending: 0
        })
    }
})

const profileForm = useForm({
  mainbudget: props.profile.mainbudget,
    spending: props.profile.spending
})

const submitProfile = () => {
    profileForm.patch(route('larabudget.update'), {
        preserveScroll: true,
        onSuccess: () => {
            refreshProfile();
        }
      });
}

const categoryForm = useForm({
  name: '',
  budgeted_amount: ''
})

const submitCategory = () => {
  categoryForm.post(route('category.store'), {
    preserveScroll: true,
    onFinish: () => categoryForm.reset(),
  })
}

const closeCategoryModal = () => {
  categoryForm.clearErrors()
  categoryForm.reset()
}
const refreshProfile = () => {
  window.location.reload();
}

</script>

