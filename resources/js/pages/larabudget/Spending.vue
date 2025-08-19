<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CategoryCard from '@/components/LaraCategoryCard.vue';   
import { onMounted, ref } from 'vue';
import axios from 'axios';
import Spinner from '@/components/ui/spinner/Spinner.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Spending',
        href: '/spending',
    },
];

interface Category {
    id: number;
    name: string;
    budgeted_amount: number;
    current_spent: number;
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

onMounted(() => {
  fetchCategoriesWithPurchases();
});

// const fetchData = async () => {
//   isLoading.value = true;
//   try {
//     const categoriesResponse = await axios.get(route('categories.show'));
//     categories.value = categoriesResponse.data.categories;
    
//     const purchasesResponse = await axios.get(route('purchases.index'), {
//       params: {
//         category_ids: categories.value.map(c => c.id).join(',')
//       }
//     });

//     console.log(purchasesResponse);
//     const purchases = Array.isArray(purchasesResponse.data)
//       ? purchasesResponse.data
//       : purchasesResponse.data?.purchases || [];
//     console.log(purchases)
    

//     purchases.forEach(purchase  => {
//         const categoryId = purchase.category_id;
//         if (!purchasesByCategory.value[categoryId]) {
//           purchasesByCategory.value[categoryId] = [];
//         }
//         purchasesByCategory.value[categoryId].push(purchase);
//       });
//     console.log(purchasesByCategory);
    
//   } catch (err) {
//     console.error('Error loading data:', err);
//   } finally {
//     isLoading.value = false;
//   }
// };
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
</script>

<template>
<Head title="Spending" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div v-if="isLoading" class="flex justify-center p-8">
                <Spinner/>
            </div>

            <div v-else-if="error" class="text-red-500 p-4">
                Error loading data
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="category in categories" :key="category.id">
                    <CategoryCard 
                        :category="category" 
                        :recentPurchases="purchasesByCategory[category.id] || []"
                        @purchase-created="fetchCategoriesWithPurchases"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>