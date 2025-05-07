<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PlusCircle, Pencil, Trash2, Fan, Droplets, Thermometer, Gauge, PowerOff, Power } from 'lucide-vue-next';
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

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

interface Location {
    id: number;
    name: string;
    description: string;
    temperature: number;
    humidity: number;
    ac_on: boolean;
    dehumidifier_on: boolean;
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

// Track loading states for each device control
const loadingAc = ref<Record<number, boolean>>({});
const loadingDehumidifier = ref<Record<number, boolean>>({});

// Handle toggle AC for a location
const toggleAc = (locationId: number, locationName: string, currentState: boolean) => {
    loadingAc.value[locationId] = true;
    
    // Show loading toast
    const toastId = toast.loading(`Updating AC for ${locationName}...`);
    
    router.post(route('locations.toggle-ac', locationId), {}, {
        onSuccess: () => {
            loadingAc.value[locationId] = false;
            
            // Update toast on success
            toast.success(`AC ${!currentState ? 'enabled' : 'disabled'} for ${locationName}`, {
                id: toastId,
                description: `Temperature control updated successfully`,
            });
        },
        onError: (error) => {
            loadingAc.value[locationId] = false;
            
            // Update toast on error
            toast.error(`Failed to update AC for ${locationName}`, {
                id: toastId,
                description: error?.message || 'An error occurred while updating the device',
            });
        },
        preserveScroll: true,
    });
};

// Handle toggle dehumidifier for a location
const toggleDehumidifier = (locationId: number, locationName: string, currentState: boolean) => {
    loadingDehumidifier.value[locationId] = true;
    
    // Show loading toast
    const toastId = toast.loading(`Updating dehumidifier for ${locationName}...`);
    
    router.post(route('locations.toggle-dehumidifier', locationId), {}, {
        onSuccess: () => {
            loadingDehumidifier.value[locationId] = false;
            
            // Update toast on success
            toast.success(`Dehumidifier ${!currentState ? 'enabled' : 'disabled'} for ${locationName}`, {
                id: toastId,
                description: `Humidity control updated successfully`,
            });
        },
        onError: (error) => {
            loadingDehumidifier.value[locationId] = false;
            
            // Update toast on error
            toast.error(`Failed to update dehumidifier for ${locationName}`, {
                id: toastId,
                description: error?.message || 'An error occurred while updating the device',
            });
        },
        preserveScroll: true,
    });
};
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
                                <TableHead class="w-[8%] text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <Fan class="h-4 w-4" />
                                        <span>AC</span>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[8%] text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <Droplets class="h-4 w-4" />
                                        <span>Dehumidifier</span>
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
                                <TableCell class="text-center">
                                    <Fan 
                                        class="h-5 w-5 mx-auto cursor-pointer transition-colors" 
                                        :class="[
                                            location.ac_on ? 'text-green-500' : 'text-gray-400',
                                            loadingAc[location.id] ? 'opacity-50' : ''
                                        ]"
                                        @click="!loadingAc[location.id] && toggleAc(location.id, location.name, location.ac_on)" 
                                    />
                                </TableCell>
                                <TableCell class="text-center">
                                    <Droplets 
                                        class="h-5 w-5 mx-auto cursor-pointer transition-colors" 
                                        :class="[
                                            location.dehumidifier_on ? 'text-blue-500' : 'text-gray-400',
                                            loadingDehumidifier[location.id] ? 'opacity-50' : ''
                                        ]"
                                        @click="!loadingDehumidifier[location.id] && toggleDehumidifier(location.id, location.name, location.dehumidifier_on)" 
                                    />
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
