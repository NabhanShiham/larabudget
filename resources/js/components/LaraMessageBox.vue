<template>
    <div class="message-box">
        <div class="header">
            <h3>{{}}</h3>
        </div>
        <div class="chat-container">
            <div v-for="message in messages" :key="message.id">
                <div :class="['message', message.sender_id === user.id ? 'sent' : 'received']">
                    {{message.content}}
                </div>
            </div>
        </div>
        <div class="input-area">
            <input v-model="newMessage" @keyup.enter="send" placeholder="Say sumn twin...">
            <button @click="send" class="bg-blue dark:bg-white dark:text-black px-2 py-2">Send</button>
        </div>
    </div>
</template>

<script>
    export default {
    props: ['user', 'recipient', 'messages'],
    data() {
        return {
        newMessage: ''
        }
    },
    methods: {
        send() {
        if (this.newMessage.trim() === '') return;
        
        this.$emit('send-message', {
            content: this.newMessage,
            recipient_id: this.recipient.id
        });
        
        this.newMessage = '';
        }
    }
    }
</script>
<style>
        .message-transition-enter-active, .message-transition-leave-active {
            transition: all 0.3s ease;
        }
        .message-transition-enter, .message-transition-leave-to {
            opacity: 0;
            transform: translateY(-10px);
        }
        .chat-container {
            scrollbar-width: thin;
            scrollbar-color: #c5c5c5 #f1f1f1;
        }
        .chat-container::-webkit-scrollbar {
            width: 6px;
        }
        .chat-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .chat-container::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 10px;
        }
    </style>