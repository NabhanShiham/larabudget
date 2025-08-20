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


    <div class="flex justify-end gap-2 pt-4">
      <Button
        type="button"
        variant="outline"
        @click="$emit('cancel')"
      >
        Cancel
      </Button>
      <Button type="submit" :disabled="form.processing">
        <Spinner v-if="form.processing" class="mr-2 h-4 w-4" />
        Search
      </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Label } from 'reka-ui'
import {Input } from '@/components/ui/input'
import {Button} from '@/components/ui/button'
import Spinner from './ui/spinner/Spinner.vue'

const emit = defineEmits(['search-clicked', 'cancel'])

const form = useForm({
  name: '',
})

const handleSearch = () =>  {
    form.get(route('search.friends'), {
        preserveScroll: true
    })
}

</script>