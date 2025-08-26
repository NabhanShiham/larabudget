<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import LaraFriendsList from '@/components/LaraFriendsList.vue';
import { ref } from 'vue';
import LaraCollaborateForm from '@/components/LaraCollaborateForm.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Collaborate',
        href: '/collaborate',
    },
];

const showCollaborateDialog = ref(false); 


const handleDialogClose = () => {
    // todo
}
const refreshProfile = () => {
    window.location.reload();
}
</script>

<template>
    <Head title="Collaborate" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div class="flex justify-center py-5">
                    <h1>Collaborate with your Friends!</h1>
                    </div>
                    <div class="flex justify-center">
                    <button @click="showCollaborateDialog = true" class="dark:bg-white px-2 py-0.5 bg-black dark:text-black text-white rounded-md">New Project</button>
                    </div>
                    <Dialog v-model:open="showCollaborateDialog">
                        <DialogContent class="sm:max-w-[425px]">
                        <LaraCollaborateForm
                            @closed="handleDialogClose"
                            @saved="refreshProfile"
                        />
                        </DialogContent>
                    </Dialog>


                    <div class="grid grid-rows-1 grid-flow-col flex justify-center gap-4 p-4">
                    <p>Your Friends</p>
                    </div>
                    <div class="flex row-span-1 justify-center">
                    <LaraFriendsList />
                    </div>
            </div>
        </div>
    </AppLayout>
</template>
