<template>
  <div class="input-area">
    <input
      v-model="newMessage"
      @keyup.enter="send"
      placeholder="Say sumn twin..."
      class="border rounded px-2 py-1 w-full"
    />
    <button
      @click="send"
      class="bg-blue-500 text-white px-4 py-2 rounded ml-2 hover:bg-blue-600"
    >
      Send
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';


const props = defineProps<{
  currentUserId?: number;
  recipientId: number;
  chatMessages: Array<{ id: number; content: string; sender_id: number; recipient_id: number }>;
}>();


const newMessage = ref('');
const emit = defineEmits(['messageSent']);

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