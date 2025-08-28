<template>
  <div class="input-area">
    <div class="messages space-y-2 mb-4 max-h-64 overflow-y-auto">
      <div
        v-for="message in chatMessages"
        :key="message.id"
        :class="{
          'text-right': message.sender_id === currentUserId,
          'text-left': message.sender_id !== currentUserId
        }"
      >
      <span
        :class="{
          'bg-blue-100 text-blue-800': message.sender_id === currentUserId,
          'bg-gray-200 text-gray-800': message.sender_id !== currentUserId
        }"
        class="inline-block px-3 py-2 rounded"
      >
        {{message.content}}
      </span>
      </div>
    </div>
    <input
      v-model="newMessage"
      @keyup.enter="send"
      placeholder="Say sumn twin..."
      class="border rounded px-2 py-1 w-full"
    />
    <div class="flex justify-end">
      <button
      @click="send"
      class="bg-blue-500 text-white px-4 py-2 rounded ml-2 hover:bg-blue-600"
    >
      Send
    </button>
    </div>
    
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, onMounted, onRenderTriggered } from 'vue';
import axios from 'axios';


const props = defineProps<{
  currentUserId?: number;
  recipientId: number;
  chatMessages: Array<{ id: number; content: string; sender_id: number; recipient_id: number }>;
}>();

onMounted(()=> {
  scrollToBottom();
})

const newMessage = ref('');
const emit = defineEmits(['messageSent']);

const scrollToBottom = () => {
    const container = document.querySelector('.messages');
    if (container) container.scrollTop = container.scrollHeight;
};


const send = async () => {
  if (!newMessage.value.trim()) return;

  try {
    const response = await axios.post(route('chat.send'), {
      sender_id: props.currentUserId,
      recipient_id: props.recipientId,
      content: newMessage.value
    });
    
    emit('messageSent', response.data.message);

    newMessage.value = '';
  } catch (error) {
    console.error('Error sending message:', error);
  }
}
</script>