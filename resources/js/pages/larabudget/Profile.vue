<template>
  <Head title="Profile" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3"></div>

      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
        <h1 class="text-center">Main Budget</h1>
        <br />
        <p class="text-center">Current Spending</p>
        <br />
        <p class="text-center">Categories</p>
        <br />
        <div class="flex justify-center">
          <Dialog>
            <DialogTrigger as-child>
              <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                Edit Profile
              </button>
            </DialogTrigger>
            <DialogContent>
              <div class="fixed inset-0 bg-black/50" @click="closeModal"></div>
              <div class="relative bg-blue dark:bg-black-900 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto">
                <LaraProfileEditForm :mustVerifyEmail="mustVerifyEmail" :status="status" />
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import LaraProfileEditForm from '@/components/LaraProfileEditForm.vue'
import { type BreadcrumbItem } from '@/types'
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

defineProps(['mustVerifyEmail', 'status'])

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Profile Information',
    href: '/profile',
  },
]

const closeModal = () => {
  // 
}
</script>