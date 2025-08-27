<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import { PencilIcon, Search } from 'lucide-vue-next';
import LaraAddFriendsForm from '@/components/LaraAddFriendsForm.vue';
import LaraFriendsList from '@/components/LaraFriendsList.vue';
import { SearchIcon } from 'lucide-vue-next';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Friends',
        href: '/friends',
    },
];
</script>

<template>

    <Head title="Friends" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">

            <Dialog>
            <DialogTrigger as-child>
                <div class="flex flex-wrap justify-end gap-4 p-4">
              <Button variant="default" size="lg">
                <Search class="w-4 h-4 mr-2" />
                Add Friends
              </Button>
              </div>

            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
              <LaraAddFriendsForm />
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
