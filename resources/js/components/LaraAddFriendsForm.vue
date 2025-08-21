<template class="sm:max-w-[425px] mx-auto">
  <form @submit.prevent="handleSearch" class="space-y-6">
    <div>
      <Label for="name" class="space-y-3 text-center">Name</Label>
      <Input
        id="name"
        v-model="form.name"
        required
      />
    </div>


    <div class="flex flex-wrap justify-end gap-4 p-4">
      <Button
        type="button"
        variant="outline"
        @click="$emit('cancel')"
      >
        Cancel
      </Button>
      <Dialog>
        <DialogTrigger as-child>
          <Button type="submit" :disabled="form.processing">
            <Spinner v-if="form.processing" class="mr-2 h-4 w-4" />
            Search
          </Button>
        </DialogTrigger>
        <DialogContent>
          <div v-for="user in searchedUsers" :key="user.id">
            <LaraUserCard :User="user"/>
          </div>
        </DialogContent>
      </Dialog>
      
    </div>
  </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Label } from 'reka-ui'
import {Input } from '@/components/ui/input'
import {Button} from '@/components/ui/button'
import Spinner from './ui/spinner/Spinner.vue'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import Dialog from './ui/dialog/Dialog.vue'
import DialogTrigger from './ui/dialog/DialogTrigger.vue'
import DialogContent from './ui/dialog/DialogContent.vue'
import LaraUserCard from '@/components/LaraUserCard.vue'
import { User } from 'lucide-vue-next'




const emit = defineEmits(['search-clicked', 'cancel'])

const form = useForm({
  name: '',
})

interface User {
  id: number;
  name: string;
  email: string;
}

const searchedUsers =  ref<User[]>([]);
 
const user = ref<{ id: number; name: string; email: string } | null>(null)

const fetchCurrentUser = async () => {
  try{
    const response = await axios.get(route('current.user'))
    user.value = response.data.user; 
  }catch(error){
    console.error('error getting the current user id brev', error)
  }
}

onMounted(() => {
  fetchCurrentUser();
});

const handleSearch = async () =>  {
    try {
      const response = await axios.get(route('search.friends'));
      searchedUsers.value = Array.isArray(response.data.users) ? response.data.users : []
      searchedUsers.value = searchedUsers.value.filter(u => u.id != user.value!.id);
    }catch(error){
      console.error('error searching for ur friends buddy', error);
    }
    console.log(searchedUsers.value);
}

</script>