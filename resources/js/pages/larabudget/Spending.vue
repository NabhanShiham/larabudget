<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import CategoryCard from '@/components/LaraCategoryCard.vue';   
import { onMounted, ref } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Spending',
        href: '/spending',
    },
];

const categories = ref([])
const isLoading = ref(false)
const error = ref(null)

onMounted(() => {
  fetchCategories()
})

const fetchCategories = async () => {
  isLoading.value = true
  error.value = null
  
  try {
    const response = await axios.get(route('categories.show'))
    categories.value = response.data.categories
  } catch (error) {
    console.error('API Error:', error)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
    <Head title="spending" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <h1>Spending Analytics</h1>
                    <br>
                    <p>Current Spending</p>
                    <br>
                    <p>Categories</p>
                    <li v-for="category in categories" :key="category.id">
                        <CategoryCard :category="{
                            name: category.name,
                            budgeted_amount: category.budgeted_amount,
                            current_spent: category.current_spent,
                            }" />
                    
                    </li>
                    <br>
            </div>
        </div>
    </AppLayout>
</template>
