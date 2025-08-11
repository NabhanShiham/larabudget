<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile Information',
        href: '/profile',
    },
];
</script>

<template>
    <Head title="Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern/>
                    <center><h1>Main Budget</h1></center>
                    <br>
                    <center><p>Current Spending</p></center>
                    <br>
                    <center><p>Categories</p></center>
                    <br>
            </div>
        </div>
    </AppLayout>
</template>

<script>


</script>