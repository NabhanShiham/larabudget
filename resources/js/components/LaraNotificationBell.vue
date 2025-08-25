<template>
  <div class="relative">
    <button @click="toggleNotifications" class="relative text-gray-600 hover:text-gray-800">
      <span class="text-xl">🔔</span>
      <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
        {{ unreadCount }}
      </span>
    </button>

    <transition name="fade">
      <div v-if="showNotifications" class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-lg font-semibold">Notifications</h3>
          <button @click="markAllAsRead" class="text-sm text-blue-500 hover:underline">Mark all as read</button>
        </div>

        <div class="max-h-80 overflow-y-auto">
          <div v-if="notifications.length === 0" class="p-4 text-gray-500 text-center">
            No notifications
          </div>

          <div v-else>
            <div 
              v-for="notification in notifications" 
              :key="notification.id"
              :class="['p-4 border-b border-gray-100 flex justify-between items-start', { 'bg-gray-50': !notification.read }]"
            >
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-700">
                  {{ getNotificationTypeLabel(notification.type) }}
                </div>
                <div class="text-sm text-gray-600">{{ notification.title }}</div>
                <div class="text-sm text-gray-500">{{ notification.message }}</div>

                <div v-if="notification.type === 'friend_request'" class="mt-2 flex gap-2">
                  <button @click="acceptFriendRequest(notification)" class="px-2 py-1 text-xs bg-green-500 text-white rounded">Accept</button>
                  <button @click="declineFriendRequest(notification)" class="px-2 py-1 text-xs bg-red-500 text-white rounded">Decline</button>
                </div>

                <div class="text-xs text-gray-400 mt-1">
                  {{ formatTime(notification.created_at) }}
                </div>
              </div>

              <button @click="deleteNotification(notification)" class="text-gray-400 hover:text-red-500 text-lg ml-2">×</button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import Pusher from 'pusher-js';

export default {
  data() {
    return {
      notifications: [],
      showNotifications: false,
      pusher: null,
      channel: null
    };
  },

  computed: {
    unreadCount() {
      return this.notifications.filter(n => !n.read).length;
    }
  },

  async mounted() {
    await this.fetchNotifications();
    this.initializePusher();
  },

  beforeUnmount() {
    if (this.pusher) {
      this.pusher.disconnect();
    }
  },

  methods: {
    async fetchNotifications() {
      try {
        const response = await axios.get('/api/notifications');
        this.notifications = response.data;
      } catch (error) {
        console.error('Error fetching notifications:', error);
      }
    },

    initializePusher() {
      this.pusher = new Pusher(process.env.VUE_APP_PUSHER_APP_KEY, {
        cluster: process.env.VUE_APP_PUSHER_APP_CLUSTER,
        forceTLS: true
      });

      this.channel = this.pusher.subscribe(`notifications.${this.$store.state.user.id}`);
      
      this.channel.bind('new-notification', (data) => {
        this.notifications.unshift(data);
        this.showBrowserNotification(data);
      });
      
      this.channel.bind('friend-request-sent', (data) => {
        const notification = {
          id: data.id,
          title: 'New Friend Request',
          message: data.message,
          type: 'friend_request',
          data: data,
          read: false,
          created_at: data.created_at
        };
        
        this.notifications.unshift(notification);
        this.showBrowserNotification(notification);
      });
    },

    showBrowserNotification(notification) {
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(notification.title, {
          body: notification.message,
          icon: '/icon.png'
        });
      }
    },

    async acceptFriendRequest(notification) {
      try {
        const response = await axios.post('/api/friend-requests/accept', {
          friend_request_id: notification.data.friend_request_id || notification.id
        });
        
        await this.markAsRead(notification);
        
        this.notifications = this.notifications.filter(n => n.id !== notification.id);
        
        this.$toast.success('Friend request accepted');
      } catch (error) {
        console.error('Error accepting friend request:', error);
        this.$toast.error('Failed to accept friend request');
      }
    },

    async declineFriendRequest(notification) {
      try {
        const response = await axios.post('/api/friend-requests/decline', {
          friend_request_id: notification.data.friend_request_id || notification.id
        });
        
        await this.markAsRead(notification);
        
        this.notifications = this.notifications.filter(n => n.id !== notification.id);
        
        this.$toast.info('Friend request declined');
      } catch (error) {
        console.error('Error declining friend request:', error);
        this.$toast.error('Failed to decline friend request');
      }
    },

    async markAsRead(notification) {
      if (!notification.read) {
        try {
          await axios.put(`/api/notifications/${notification.id}/read`);
          notification.read = true;
        } catch (error) {
          console.error('Error marking as read:', error);
        }
      }
    },

    async markAllAsRead() {
      try {
        await axios.put('/api/notifications/read-all');
        this.notifications.forEach(n => n.read = true);
      } catch (error) {
        console.error('Error marking all as read:', error);
      }
    },

    async deleteNotification(notification) {
      try {
        await axios.delete(`/api/notifications/${notification.id}`);
        this.notifications = this.notifications.filter(n => n.id !== notification.id);
      } catch (error) {
        console.error('Error deleting notification:', error);
      }
    },

    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
    },

    formatTime(dateString) {
      return new Date(dateString).toLocaleTimeString();
    },
    
    getNotificationTypeLabel(type) {
      const types = {
        'friend_request': 'Friend Request',
        'info': 'Information',
        'success': 'Success',
        'warning': 'Warning',
        'error': 'Error'
      };
      
      return types[type] || type;
    }
  }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>