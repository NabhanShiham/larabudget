<template>
  <Head title="Budget Profile" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="relative min-h-[50vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
          <div class="bg-card rounded-lg p-4 shadow">
            <h3 class="text-sm font-medium text-muted-foreground">Main Budget</h3>
            <p class="text-2xl font-bold">MVR {{ mainBudget.toFixed(2) }}</p>
          </div>
          <div class="bg-card rounded-lg p-4 shadow">
            <h3 class="text-sm font-medium text-muted-foreground">Current Spending</h3>
            <p class="text-2xl font-bold">MVR {{ currentSpent.toFixed(2) }}</p>
          </div>
          <div class="bg-card rounded-lg p-4 shadow">
            <h3 class="text-sm font-medium text-muted-foreground">Remaining</h3>
            <p class="text-2xl font-bold" :class="remainingBudget < 0 ? 'text-destructive' : 'text-success'">
              MVR {{ remainingBudget.toFixed(2) }}
            </p>
          </div>
        </div>
        <div class="px-6 pb-6">
          <div class="h-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div 
              class="h-full transition-all duration-500" 
              :class="budgetPercentage > 80 ? 'bg-destructive' : budgetPercentage > 50 ? 'bg-warning' : 'bg-success'"
              :style="`width: ${Math.min(budgetPercentage, 100)}%`"
            ></div>
          </div>
          <p class="text-sm text-muted-foreground mt-2 text-center">
            {{ Math.round(budgetPercentage) }}% of budget used
          </p>
        </div>
        <div class="flex justify-center p-6">
          <Dialog>
            <DialogTrigger as-child>
              <Button variant="default" size="lg">
                <PencilIcon class="w-4 h-4 mr-2" />
                Edit Budget
              </Button>
              
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
              <LaraProfileEditForm 
                :initial-budget="mainBudget"
                :initial-spent="currentSpent"
                @saved="refreshProfile"
                @vue:unmounted="refreshProfile"
              />
            </DialogContent>
          </Dialog>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, defineProps, onMounted, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { PencilIcon } from 'lucide-vue-next'
import {
  Dialog,
  DialogContent,
  DialogTrigger,
} from '@/components/ui/dialog'
import LaraProfileEditForm from '@/components/LaraProfileEditForm.vue'
import axios from 'axios';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Budget Profile', href: route('larabudget.show') },
]

const profile = ref({ mainbudget: 0, currentspent: 0 });

onMounted(async () => {
  try{
  const response = await axios.get('larabudget/profile');
  profile.value = response.data.profile;
  }catch (error){
    console.error('Error getting yo profile cuh:', error);
  }
});

const mainBudget = computed(() => Number(profile.value.mainbudget) || 0);
const currentSpent = computed(() => Number(profile.value.currentspent) || 0);
const remainingBudget = computed(() => mainBudget.value - currentSpent.value);
const budgetPercentage = computed(() => {
  return mainBudget.value > 0 ? (currentSpent.value / mainBudget.value) * 100 : 0;
});

const refreshProfile = () => {
  window.location.reload();
}

</script>