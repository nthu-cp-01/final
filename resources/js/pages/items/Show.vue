<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import Heading from '@/components/Heading.vue';
import { Pencil, PackageCheck, BadgeMinus, BadgeAlert } from 'lucide-vue-next';
import { formatDate } from '@/lib/utils';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Location {
    id: number;
    name: string;
}

interface Item {
    id: number;
    name: string;
    purchase_date: string;
    description: string;
    status: 'registered' | 'normal' | 'gone';
    manager: User;
    owner: User;
    location: Location;
    created_at: string;
    updated_at: string;
}

interface Props {
    item: Item;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: '/items',
    },
    {
        title: props.item.name,
        href: `/items/${props.item.id}`,
    },
];

// Function to get status badge variant
const getStatusBadge = (status: string) => {
    switch(status) {
        case 'registered':
            return { variant: 'secondary', icon: BadgeAlert, label: 'Registered' };
        case 'normal':
            return { variant: 'success', icon: PackageCheck, label: 'Normal' };
        case 'gone':
            return { variant: 'destructive', icon: BadgeMinus, label: 'Gone' };
        default:
            return { variant: 'outline', icon: null, label: status };
    }
};

const statusInfo = getStatusBadge(props.item.status);
</script>

<template>
    <Head :title="item.name" />
  
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading :title="item.name" description="Item details" />
                <Link :href="route('items.edit', item.id)" as="button">
                    <Button>
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit Item
                    </Button>
                </Link>
            </div>
      
            <div class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-black">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Item Name</h3>
                        <p class="mt-1 text-lg">{{ item.name }}</p>
                    </div>
          
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                        <div class="mt-2">
                            <Badge :variant="statusInfo.variant">
                                <component :is="statusInfo.icon" class="mr-1 h-3 w-3" v-if="statusInfo.icon" />
                                {{ statusInfo.label }}
                            </Badge>
                        </div>
                    </div>
          
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Date</h3>
                        <p class="mt-1">{{ formatDate(item.purchase_date) }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</h3>
                        <p class="mt-1">
                            <Link :href="route('locations.show', item.location.id)" class="text-blue-600 hover:underline dark:text-blue-400">
                                {{ item.location.name }}
                            </Link>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Manager</h3>
                        <p class="mt-1">{{ item.manager.name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Owner</h3>
                        <p class="mt-1">{{ item.owner.name }}</p>
                    </div>
          
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
                        <p class="mt-1">{{ item.description || 'No description provided' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</h3>
                        <p class="mt-1">{{ formatDate(item.created_at) }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</h3>
                        <p class="mt-1">{{ formatDate(item.updated_at) }}</p>
                    </div>
                </div>
        
                <div class="mt-6">
                    <Link :href="route('items.index')">
                        <Button variant="outline">Back to Items</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
