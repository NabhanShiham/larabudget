<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import BudgetCard from '@/components/LaraBudgetCard.vue'
import Echo from 'laravel-echo';
import LaraNotificationBell from '@/components/LaraNotificationBell.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const profile = ref({ mainbudget: 0, currentspent: 0 });

onMounted(async () => {
  try {
    const response = await axios.get('larabudget/profile');
    profile.value = response.data.profile;
  } catch (error) {
    console.error('Error getting yo profile cuh:', error);
  }
  window.Echo.private(`user.${userId}`)
    .listen('FriendRequestSent', (e) => {
      console.log('Friend Request Received: ', e.message)
    })
});

const mainBudget = computed(() => {
  return new Intl.NumberFormat('en-MV', {
    style: 'currency',
    currency: 'MVR'
  }).format(Number(profile.value.mainbudget) || 0);
});

const currentSpent = computed(() => {
  return new Intl.NumberFormat('en-MV', {
    style: 'currency',
    currency: 'MVR'
  }).format(Number(profile.value.currentspent) || 0);
});

const remainingBudget = computed(() => {
  const remaining = (Number(profile.value.mainbudget) || 0) - (Number(profile.value.currentspent) || 0);
  return new Intl.NumberFormat('en-MV', {
    style: 'currency',
    currency: 'MVR'
  }).format(remaining);
});

const budgetPercentage = computed(() => {
  const main = Number(profile.value.mainbudget) || 0;
  const spent = Number(profile.value.currentspent) || 0;
  return main > 0 ? (spent / main) * 100 : 0;
});


const refreshProfile = () => {
  window.location.reload();
}
</script>

<template>

  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div
          class="grid relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 md:grid-cols-1 dark:border-sidebar-border">
          <center>
            <p>User Info</p>
          </center>
          <div class="flex flex-wrap justify-center gap-4 p-4">
            <BudgetCard class="custom-dashboard-card" title="Main Budget" :amount="mainBudget" icon="dollar" />
            <BudgetCard class="custom-dashboard-card" title="Current Spending" :amount="currentSpent"
              icon="shopping-cart" />
            <BudgetCard class="custom-dashboard-card" title="Remaining" :amount="remainingBudget" :is-remaining="true"
              icon="wallet" />
          </div>
        </div>
        <div
          class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
          <center>
            <p>Spending</p>
          </center>
        </div>
        <div
          class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
          <center>
            <p>Collaborate</p>
          </center>
        </div>
      </div>
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
        <PlaceholderPattern />
        <center>
          <h1>Categories</h1>
        </center>
        <br>
      </div>
    </div>
  </AppLayout>
</template>


<style scoped>
.user-info-card-wrapper {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
}

::v-deep(.custom-dashboard-card) {
  width: 180px;
  height: 120px;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  width: 160px;
  height: 100px;
}

::v-deep(.custom-dashboard-card:hover) {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

::v-deep(.custom-dashboard-card .card-title) {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

::v-deep(.custom-dashboard-card .card-amount) {
  font-size: 1.25rem;
  font-weight: bold;
  color: #059669;
}
</style>
