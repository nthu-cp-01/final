<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { PlusCircle, Pencil, Trash2, PackageCheck, BadgeMinus, BadgeAlert } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import Heading from '@/components/Heading.vue';
import { toast } from 'vue-sonner';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { watch } from 'vue';
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
}

interface Props {
    items: Item[];
}

//eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<Props>();
const page = usePage();

// Watch for flash messages from Inertia shared data
watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        toast.success(flash.success);
    }

    if (flash.error) {
        toast.error(flash.error);
    }
}, { immediate: true });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: '/items',
    },
];

// Function to get status badge variant
const getStatusBadge = (status: string) => {
    switch (status) {
        case 'registered':
            return { variant: 'secondary', icon: BadgeAlert };
        case 'normal':
            return { variant: 'success', icon: PackageCheck };
        case 'gone':
            return { variant: 'destructive', icon: BadgeMinus };
        default:
            return { variant: 'outline', icon: null };
    }
};
</script>

<template>

    <Head title="Items" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Items" description="Manage inventory items" />
                <Link :href="route('items.create')" as="button">
                <Button>
                    <PlusCircle class="mr-2 h-4 w-4" />
                    Add Item
                </Button>
                </Link>
            </div>

            <div
                class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[20%]">Name</TableHead>
                                <TableHead class="w-[15%]">Purchase Date</TableHead>
                                <TableHead class="w-[15%]">Status</TableHead>
                                <TableHead class="w-[15%]">Location</TableHead>
                                <TableHead class="w-[15%]">Manager</TableHead>
                                <TableHead class="w-[10%]">Owner</TableHead>
                                <TableHead class="w-[10%] text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="items.length === 0">
                                <TableCell colspan="7" class="text-center py-8">
                                    No items found. Click 'Add Item' to create one.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="item in items" :key="item.id">
                                <TableCell class="font-medium">
                                    <Link :href="route('items.show', item.id)" class="hover:underline">
                                    {{ item.name }}
                                    </Link>
                                </TableCell>
                                <TableCell>{{ formatDate(item.purchase_date) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadge(item.status).variant">
                                        <component :is="getStatusBadge(item.status).icon" class="mr-1 h-3 w-3"
                                            v-if="getStatusBadge(item.status).icon" />
                                        {{ item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Link :href="route('locations.show', item.location.id)" class="hover:underline">
                                    {{ item.location.name }}
                                    </Link>
                                </TableCell>
                                <TableCell>{{ item.manager.name }}</TableCell>
                                <TableCell>{{ item.owner.name }}</TableCell>
                                <TableCell class="text-right whitespace-nowrap">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('items.edit', item.id)" as="button">
                                        <Button variant="outline" size="icon">
                                            <Pencil class="h-4 w-4" />
                                            <span class="sr-only">Edit</span>
                                        </Button>
                                        </Link>
                                        <Link :href="route('items.destroy', item.id)" method="delete" as="button"
                                            preserve-scroll>
                                        <Button variant="outline" size="icon">
                                            <Trash2 class="h-4 w-4" />
                                            <span class="sr-only">Delete</span>
                                        </Button>
                                        </Link>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.max-w-0 {
    max-width: 0;
}
</style>
