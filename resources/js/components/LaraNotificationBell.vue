<template>
  <div class="relative">
    <button @click="toggleNotifications" class="relative text-gray-600 hover:text-gray-800">
      <span class="text-xl">🔔</span>
      <span v-if="unreadCount > 0" class="absolute right-0 mt-2 w-96 bg-background border border-secondary rounded-lg shadow-lg z-50">
        {{ unreadCount }}
      </span>
    </button>

    <transition name="fade">
      <div v-if="showNotifications" class="absolute right-0 mt-2 w-96 bg-black border border-gray-200 rounded-lg shadow-lg z-50">
        <div class="p-4 border-b border-secondary flex justify-between items-center">
          <h3 class="text-lg font-semibold text-primary">Notifications</h3>
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
              :class="['p-4 border-b border-gray-100 flex justify-between items-start', { 'bg-black-50': !notification.read }]"
            >
              <div class="flex-1">
                <div class="text-sm font-medium text-white-700">
                  {{ getNotificationTypeLabel(notification.type) }}
                </div>
                <div class="text-sm text-white-600">{{ notification.title }}</div>
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
import axios from 'axios';

export default {
  data() {
    return {
      showNotifications: false,
      notifications: [],
      unreadCount: 0
    }
  },

  mounted() {
    this.fetchNotifications();
    this.listenForNotifications();
    document.addEventListener('keydown', this.handleEscape);
  },

  beforeUnmount(){
    document.removeEventListener('keydown', this.handleEscape);
  },  

  methods: {
    handleEscape(event){
      if(event.key === 'Escape' && this.showNotifications){
        this.showNotifications = false; 
      }
    },
    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
      if (this.showNotifications) {
        this.fetchNotifications();
      }
    },

    async fetchNotifications() {
      try {
        const response = await axios.get(route('notifications.list'));
        this.notifications = response.data.notifications;
        this.unreadCount = response.data.unread_count;
      } catch (error) {
        console.error('Error fetching notifications:', error);
      }
    },

    async markAllAsRead() {
      try {
        await axios.post('/notifications/mark-all-read');
        this.notifications = this.notifications.map(notification => ({
          ...notification,
          read: true
        }));
        this.unreadCount = 0;
      } catch (error) {
        console.error('Error marking notifications as read:', error);
      }
    },

    async deleteNotification(notification) {
      try {
        await axios.delete(`/notifications/${notification.id}`);
        this.notifications = this.notifications.filter(n => n.id !== notification.id);
        if (!notification.read) {
          this.unreadCount--;
        }
      } catch (error) {
        console.error('Error deleting notification:', error);
      }
    },

    // take request_id, query from db and then accept
    async acceptFriendRequest(notification) {
      try {
        await axios.post(route('friend.request.respond'), {
          request_id: notification.data.request_id, 
          action: 'accept'
        });
        this.notifications = this.notifications.filter(n => n.id !== notification.id);
        this.unreadCount--;
        
      } catch (error) {
        console.error('Error accepting friend request:', error);
      }
    },

    // same thing here mibomboclaaaa probably need to edit notification migration add request_id to notification
    async declineFriendRequest(notification) {
      try {
        await axios.post('/friend-requests/respond', {
          request_id: notification.data.request_id,
          action: 'reject'
        });
        
        this.notifications = this.notifications.filter(n => n.id !== notification.id);
        this.unreadCount--;
        
      } catch (error) {
        console.error('Error declining friend request:', error);
      }
    },

    getNotificationTypeLabel(type) {
      const types = {
        friend_request: 'Friend Request',
      };
      return types[type] || type;
    },

    formatTime(timestamp) {
      return new Date(timestamp).toLocaleString();
    },

    listenForNotifications() {
      if (typeof Echo !== 'undefined') {
        Echo.private(`App.Models.User.${this.$page.props.auth.user.id}`)
          .notification((notification) => {
            this.notifications.unshift(notification);
            this.unreadCount++;
          });
      }
    }
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>