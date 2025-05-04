<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import Heading from '@/components/Heading.vue';
import { Pencil } from 'lucide-vue-next';

interface Location {
  id: number;
  name: string;
  description: string;
  deviceId: string;
}

interface Props {
  location: Location;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Locations',
    href: '/locations',
  },
  {
    title: props.location.name,
    href: `/locations/${props.location.id}`,
  },
];
</script>

<template>
  <Head :title="location.name" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex items-center justify-between">
        <Heading :title="location.name" description="Location details" />
        <Link :href="route('locations.edit', location.id)" as="button">
          <Button>
            <Pencil class="mr-2 h-4 w-4" />
            Edit Location
          </Button>
        </Link>
      </div>
      
      <div class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-black">
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Location Name</h3>
            <p class="mt-1 text-lg">{{ location.name }}</p>
          </div>
          
          <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Device ID</h3>
            <p class="mt-1 text-lg font-mono">{{ location.deviceId }}</p>
          </div>
          
          <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
            <p class="mt-1">{{ location.description }}</p>
          </div>
        </div>
        
        <div class="mt-6">
          <Link :href="route('locations.index')">
            <Button variant="outline">Back to Locations</Button>
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>