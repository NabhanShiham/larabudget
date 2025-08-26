<template>
<div class="border rounded-lg p-1 w-86" :class="
    'border-black-200 bg-black-50 dark:bg-black-900/20 dark:border-black-800'
">
<div v-for="friend in friends" :key="friend.id" class="flex items-center justify-between p-1 rounded-lg hover:bg-gray-40 dark:hover:bg-gray-950 transition-colors duration-200">
    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ friend.name }}</span>
    <div class="flex space-x-3">
        <button @click="messageFriend(friend.id)" class="px-1 py-1 text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-md transition-colors flex items-center">
            <i class="fas fa-comment mr-1"></i> Message
        </button>
        <button @click="collaborate(friend.id)" class="px-1 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md transition-colors flex items-center">
            <i class="fas fa-users mr-1"></i> Collaborate
        </button>
        <button @click="removeFriend(friend.id)" class="px-1 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition-colors flex items-center">
            <i class="fas fa-user-times mr-1"></i> Remove
        </button>
    </div>
</div>
</div>
</template>

<script setup lang="ts">
import { Paperclip, PlusIcon, TrashIcon, EyeIcon, DownloadIcon } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { computed, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import LaraPurchaseForm from './LaraPurchaseForm.vue';
import axios from 'axios';

onMounted(() => {
  fetchFriends();
});

const friends = ref<Array<{id: number; name: string}>>([]);

const fetchFriends = () => {
    try {
        axios.get(route('friends.list'))
        .then(response =>{
            friends.value = response.data.friends
        });
    }catch (error){
        console.error('Error fetching friends: ', error)
    }
}

const removeFriend = (id: number) => {
    // todo
}

const messageFriend = (id: number) => {
    // todo
}

const collaborate = (id: number) => {
    // todo
}


const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const refreshProfile = () => {
  window.location.reload();
}
</script>