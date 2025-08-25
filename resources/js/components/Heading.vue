<script setup lang="ts">
import LaraNotificationBell from '@/components/LaraNotificationBell.vue';

interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-8 space-y-0.5">
        <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
          <LaraNotificationBell />
        </p>
    </div>
</template>
