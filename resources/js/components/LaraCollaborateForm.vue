<template>
  <div v-if="showForm" class="sm:max-w-[425px] mx-auto">
    <div class="bg-white dark:bg-black rounded-lg shadow-lg w-full max-w-md p-6">
      <form @submit.prevent="submitProject" class="space-y-6">
        <div class="space-y-3 text-center">
          <h2 class="text-xl font-semibold">New Collaborative Project</h2>
          <p class="text-sm text-gray-500">Define a new Collaborative Project with your friends!</p>
        </div>

        <div class="grid gap-2 px-4">
          <label for="category">Project Name</label>
          <input
            id="project"
            v-model="collaborateForm.name"
            placeholder="e.g. Maldives Trip, Business"
            class="border rounded px-3 py-2 w-full"
          />
          <p class="text-red-500 text-sm">{{ collaborateForm.errors.name }}</p>
        </div>

        <div class="grid gap-2 px-4">
          <label for="goal">Project Goal</label>
          <input
            id="goal"
            v-model="collaborateForm.goal"
            placeholder="Goal"
            class="border rounded px-3 py-2 w-full"
          />
          <p class="text-red-500 text-sm">{{ collaborateForm.errors.goal }}</p>
        </div>

        

        <div class="grid gap-2 px-4">
            <label for="members">Members</label>
            <select
            id="members"
            v-model="collaborateForm.members"
            multiple
            class="border rounded px-3 py-2 w-full"
            >
            <option v-for="friend in friends" :key="friend.id" :value="friend.id" class="dark:text-white text-black rounded-sm">
                {{ friend.name }}
            </option>
            </select>
            <p class="text-red-500 text-sm">{{ collaborateForm.errors.members }}</p>
        </div>


        <div class="flex justify-end gap-2">
          <button type="button" @click="closeCategoryModal" class="px-4 py-2 dark:bg-red-400 bg-gray-300 rounded">
            Cancel
          </button>
          <button type="submit" :disabled="collaborateForm.processing" class="px-4 py-2 dark:bg-white dark:text-black bg-blue-500 text-white rounded">
            Create Project
          </button>
        </div>
      </form>
    </div>
  </div>
</template>



<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { onMounted, ref, onUnmounted } from 'vue'
import axios from 'axios'


const showForm = ref(true)


const collaborateForm = useForm({
  name: '',
  goal: '',
  members : [],
})

const emit = defineEmits(['closed']);

const showDialog = ref(true); 
const friends = ref<Array<{id: number; name: string}>>([]);


onMounted(() => {
  showDialog.value = true;
});


onMounted(async () => {
  const response = await axios.get(route('friends.list'));
  friends.value = response.data.friends;
});


const submitProject = () => {
  collaborateForm.post(route('create.collaborate'), {
    preserveScroll: true,
    onFinish: () => collaborateForm.reset(),
  })
}
const closeCategoryModal = () => {
  collaborateForm.clearErrors()
    collaborateForm.reset()
  refreshProfile()
}
const refreshProfile = () => {
  window.location.reload();
}

onUnmounted(() => {
  emit('closed');
});

</script>

