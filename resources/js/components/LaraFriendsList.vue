<script setup lang="ts">
import { Paperclip, PlusIcon, TrashIcon, EyeIcon, DownloadIcon, User } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import LaraPurchaseForm from './LaraPurchaseForm.vue';
import axios from 'axios';
import LaraMessageBox from './LaraMessageBox.vue';
import LaraCollaborateForm from './LaraCollaborateForm.vue';
import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

const friends = ref<Array<{id: number; name: string}>>([]);
const showMessageBox = ref(false);
const currentUserId = ref<number>(0);
const recipientId = ref<number>(0);
const chatMessages = ref<Array<{ id: number; content: string; sender_id: number; recipient_id: number }>>([]);

onMounted(() => {
  fetchFriends();
  getUserId(); 
});

window.Pusher = Pusher;
const pusherKey = '30e66a7ce4a62122a6ed';
const pusherCluster = 'ap2';
const echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true
});



const fetchFriends = () => {
    try {
        axios.get(route('friends.list'))
        .then(response =>{
            friends.value = response.data.friends
            console.log(friends.value)
        });
    }catch (error){
        console.error('Error fetching friends: ', error)
    }
}

const removeFriend = (id: number) => {
    try {
        axios.put(route('remove.friend'), {friendId: id})
        .then(response => {
            console.log(response);
        });
    }catch (error){
        console.error('Error removing friend: ', error)
    }
}

const getUserId = async () => {
    try {
        const response = await axios.get(route('get.user'));
        currentUserId.value = response.data.user.id;
        echo.private(`chat.${currentUserId.value}`)
            .listen('MessageSent', (e: { message: { id: number; content: string; sender_id: number; recipient_id: number; }; }) => {
        chatMessages.value.push(e.message);
        })
    }catch (error){
        console.log('Error getting User Id:', error);
    }
}

const getFriendMessages = async (id: number) => {
    console.log(currentUserId.value)
  const selectedUser = friends.value.find(friend => friend.id === id);
  if (selectedUser) {
    recipientId.value = selectedUser.id;
    showMessageBox.value = true;
    try {
      const response = await axios.get(route('chat.messages'), {
        params: {
          sender_id: currentUserId.value,
          recipient_id: selectedUser.id
        }
      });
      chatMessages.value = response.data.messages;
    } catch (error) {
      console.error('Error fetching messages:', error);
    }
  }
};

const collaborate = (id: number) => {
    // todo
}

const handleNewMessage = (message: { id: number; content: string; sender_id: number; recipient_id: number; }) => {
  chatMessages.value.push(message);
};


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



<template>
<div class="border rounded-lg p-1 w-86" :class="
    'border-black-200 bg-black-50 dark:bg-black-900/20 dark:border-black-800'
">
<div v-for="friend in friends" :key="friend.id" class="flex items-center justify-between p-1 rounded-lg hover:bg-gray-40 dark:hover:bg-gray-950 transition-colors duration-200">
    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ friend.name }}</span>
    <div class="flex space-x-3">
        
        <Dialog>
            <DialogTrigger>
                <button @click="getFriendMessages(friend.id)" class="px-1 py-1 text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-md transition-colors flex items-center">
                    <i class="fas fa-comment mr-1"></i> Message
                </button>
            </DialogTrigger>
            <DialogContent>
                <LaraMessageBox 
                    :currentUserId="currentUserId"
                    :recipientId="recipientId"
                    :chatMessages="chatMessages"
                    @MessageSent="handleNewMessage"
                />                
            </DialogContent>
        </Dialog>
        <Dialog>
            <DialogTrigger>
                <button @click="collaborate(friend.id)" class="px-1 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md transition-colors flex items-center">
                    <i class="fas fa-users mr-1"></i> Collaborate
                </button>
            </DialogTrigger>
            <DialogContent>
                <LaraCollaborateForm />
            </DialogContent>    
        </Dialog>
        <Dialog>
            <DialogTrigger>
                <button class="px-1 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition-colors flex items-center">
                    <i class="fas fa-user-times mr-1"></i> Remove
                </button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                <DialogTitle>Are you sure about removing {{ friend.name }} as your friend?</DialogTitle>
                <DialogDescription>
                This action cannot be reversed
                </DialogDescription>
                </DialogHeader>
                <Button class="flex items-center bg-red-50 hover:bg-red-300 text-red-600 hover:text-red-800 px-2 py-0.5 rounded-md shadow-sm transition-colors" @click="removeFriend(friend.id)">
                <TrashIcon class="w-4 h-4 mr-0.02"/>
                </Button>
            </DialogContent>
        </Dialog>
        
    </div>
</div>
</div>

</template>

