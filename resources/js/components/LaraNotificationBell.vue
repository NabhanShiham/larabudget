<template>
  <div class="notification-container">
    <button @click="toggleNotifications" class="notification-bell">
      <span class="bell-icon">🔔</span>
      <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
    </button>

    <div v-if="showNotifications" class="notification-dropdown">
      <div class="notification-header">
        <h3>Notifications</h3>
        <button @click="markAllAsRead" class="mark-all-read">
          Mark all as read
        </button>
      </div>

      <div v-if="notifications.length === 0" class="empty-state">
        No notifications
      </div>

      <div v-else class="notification-list">
        <div 
          v-for="notification in notifications" 
          :key="notification.id"
          :class="['notification-item', { unread: !notification.read }]"
        >
          <div class="notification-type" :class="notification.type">
            {{ getNotificationTypeLabel(notification.type) }}
          </div>
          <div class="notification-content">
            <div class="notification-title">{{ notification.title }}</div>
            <div class="notification-message">{{ notification.message }}</div>
            
            <div v-if="notification.type === 'friend_request'" class="friend-request-actions">
              <button @click="acceptFriendRequest(notification)" class="btn-accept">
                Accept
              </button>
              <button @click="declineFriendRequest(notification)" class="btn-decline">
                Decline
              </button>
            </div>
            
            <div class="notification-time">
              {{ formatTime(notification.created_at) }}
            </div>
          </div>
          <button 
            @click="deleteNotification(notification)"
            class="delete-btn"
          >
            ×
          </button>
        </div>
      </div>
    </div>
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
.friend-request-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.btn-accept {
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.3rem 0.8rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;
}

.btn-decline {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 0.3rem 0.8rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;
}

.btn-accept:hover {
  background-color: #45a049;
}

.btn-decline:hover {
  background-color: #d32f2f;
}

.notification-type.friend_request {
  background: #e8f5e8;
  color: #2e7d32;
}
</style>