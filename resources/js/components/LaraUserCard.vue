<template>
  <div class="p-4 bg-gray dark:bg-black-800 rounded shadow-sm">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
        <slot name="title">
          {{ User.name }}
        </slot>
      </h3>
      <Button class="ml-4" @click="handleAddFriend()">Add Friend</Button>
    </div>
  </div>
</template>


<script setup lang="ts">
import Button from './ui/button/Button.vue'
import axios from 'axios'
import { ref } from 'vue'

const props = defineProps({
  User: {
    type: Object as () => {
      id: number
      name: string
      email: string
    },
    required: true
  },
})

const handleAddFriend = async () => {
    try {
        const response = await axios.post(route('friend.request.send'), { receiver_id: props.User.id })
        console.log(props.User.id);
        console.log('Friend Request Sent', response.data);
    }catch (error){
        console.log('Error adding yo friend brev', error);
    }
}

</script>

