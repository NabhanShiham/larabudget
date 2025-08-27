<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { computed, onMounted, ref, onUnmounted } from 'vue';
import axios from 'axios';
import BudgetCard from '@/components/LaraBudgetCard.vue'
import Echo from 'laravel-echo';
import LaraNotificationBell from '@/components/LaraNotificationBell.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import LaraCategoryCard from '@/components/LaraCategoryCard.vue';
import LaraFriendsList from '@/components/LaraFriendsList.vue';
import LaraAddCategoryForm from '@/components/LaraAddCategoryForm.vue';
import { PlusIcon } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const profile = ref({ mainbudget: 0, currentspent: 0 });
const user_id = ref('');
const showAddCategoryDialog = ref(false); 
const handleDialogClose = () => {
  showAddCategoryDialog.value = false;
  refreshProfile();
};

onMounted(async () => {
  try {
    const response = await axios.get('larabudget/profile');
    profile.value = response.data.profile;
    localStorage.setItem('user_id', String(response.data.profile.user_id));
  } catch (error) {
    console.error('Error getting yo profile cuh:', error);
  }
  window.Echo.private(`user.${user_id}`)
    .listen('FriendRequestSent', (e: { message: any; }) => {
      console.log('Friend Request Received: ', e.message)
    })

    fetchCategoriesWithPurchases();
});

interface Category {
    id: number;
    name: string;
    budgeted_amount: number;
    current_spent: number;
    purchases: Record<number, Purchase[]>
}

interface Purchase {
    id: number;
    category_id: number;
    amount: number;
    description: string;
    transaction_date: string;
    created_at: string;
    receipt_path: string | null;
}

const categories = ref<Category[]>([]);
const purchasesByCategory = ref<Record<number, Purchase[]>>({});
const isLoading = ref(false);
const error = ref(null);

const fetchCategoriesWithPurchases = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(route('purchases.index')); 
    
    categories.value = Array.isArray(response.data.categories) 
      ? response.data.categories 
      : [];
    
    purchasesByCategory.value = {};
    
    categories.value.forEach(category => {
      const categoryPurchases = Array.isArray(category.purchases) 
        ? category.purchases 
        : [];
      
      purchasesByCategory.value[category.id] = categoryPurchases;
    });
    
    console.log('Fetched data:', {
      categories: categories.value,
      purchasesByCategory: purchasesByCategory.value
    });
    
  } catch (err) {
    console.error('Error loading data:', err);
  } finally {
    isLoading.value = false;
  }
};

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
  showAddCategoryDialog.value = false;
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
          <div class="flex justify-center">  
          <p>User Info</p>
          </div>
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
          <div class="flex justify-center">
          <h1>Spending</h1>
          </div>
        </div>
        <div
          class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="flex justify-center p-1">
          <p>Friends</p>
        </div>
        <div class="flex justify-center">
          <LaraFriendsList/>
        </div>
        </div>
      </div>
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
        <div class="flex justify-center">
          <div class="space-y-4 mb-6">
            <h1 class="text-2xl font-semibold">Categories</h1>
            <button
              @click="showAddCategoryDialog = true"
              class="px-4 py-2 bg-black text-white dark:bg-white dark:text-black rounded"
            >
              Add Category
            </button>
          </div>
        <div class="flex justify-center p-6">
          <Dialog v-model:open="showAddCategoryDialog">
            <DialogContent class="sm:max-w-[425px]">
              <LaraAddCategoryForm
                @closed="handleDialogClose"
                @saved="refreshProfile"
              />
            </DialogContent>
          </Dialog>
        </div>
        </div>
          <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div v-if="isLoading" class="flex justify-center p-8">
                <Spinner/>
            </div>

            <div v-else-if="error" class="text-red-500 p-4">
                Error loading data
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="category in categories" :key="category.id">
                    <LaraCategoryCard 
                        :category="category" 
                        :recentPurchases="purchasesByCategory[category.id] || []"
                        @purchase-created="fetchCategoriesWithPurchases"
                    />
                </div>
            </div>
        </div>
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
