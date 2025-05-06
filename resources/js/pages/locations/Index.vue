<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusCircle, Pencil, Trash2, Fan, Droplets, Thermometer, Gauge } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import Heading from '@/components/Heading.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { type BreadcrumbItem } from '@/types';

interface Location {
    id: number;
    name: string;
    description: string;
    temperature: number;
    humidity: number;
    ac_on: boolean;
    humidifier_on: boolean;
}

interface Props {
    locations: Location[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Locations',
        href: '/locations',
    },
];
</script>

<template>

    <Head title="Locations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Locations" description="Manage IoT device locations" />
                <Link :href="route('locations.create')" as="button">
                <Button>
                    <PlusCircle class="mr-2 h-4 w-4" />
                    Add Location
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
                                <TableHead class="w-[30%]">Description</TableHead>
                                <TableHead class="w-[12%]">
                                    <div class="flex items-center gap-1">
                                        <Thermometer class="h-4 w-4" />
                                        <span>Temperature</span>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[12%]">
                                    <div class="flex items-center gap-1">
                                        <Gauge class="h-4 w-4" />
                                        <span>Humidity</span>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[8%]">
                                    <div class="flex items-center gap-1">
                                        <Fan class="h-4 w-4" />
                                        <span>AC</span>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[8%]">
                                    <div class="flex items-center gap-1">
                                        <Droplets class="h-4 w-4" />
                                        <span>Humidifier</span>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[10%] text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="locations.length === 0">
                                <TableCell colspan="7" class="text-center py-8">
                                    No locations found. Click 'Add Location' to create one.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="location in locations" :key="location.id">
                                <TableCell class="font-medium">{{ location.name }}</TableCell>
                                <TableCell class="max-w-0">
                                    <div class="truncate">{{ location.description }}</div>
                                </TableCell>
                                <TableCell>{{ location.temperature }}°C</TableCell>
                                <TableCell>{{ location.humidity }}%</TableCell>
                                <TableCell>
                                    <div :class="location.ac_on ? 'text-green-500' : 'text-gray-400'"
                                        class="flex justify-center">
                                        <Fan class="h-5 w-5" />
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div :class="location.humidifier_on ? 'text-blue-500' : 'text-gray-400'"
                                        class="flex justify-center">
                                        <Droplets class="h-5 w-5" />
                                    </div>
                                </TableCell>
                                <TableCell class="text-right whitespace-nowrap">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('locations.edit', location.id)" as="button">
                                        <Button variant="outline" size="icon">
                                            <Pencil class="h-4 w-4" />
                                            <span class="sr-only">Edit</span>
                                        </Button>
                                        </Link>
                                        <Link :href="route('locations.destroy', location.id)" method="delete"
                                            as="button" preserve-scroll>
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
