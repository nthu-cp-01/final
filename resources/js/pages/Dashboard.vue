<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Users, MapPin, Package, TrendingUp } from 'lucide-vue-next';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface Props {
    stats: {
        users_count: number;
        locations_count: number;
        items_count: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Users Count Card -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Users</p>
                                <p class="text-3xl font-bold">{{ stats.users_count }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/20">
                                <Users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-muted-foreground">
                            <TrendingUp class="mr-1 h-4 w-4 text-green-600" />
                            Registered users in the system
                        </div>
                    </div>
                </div>

                <!-- Locations Count Card -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Locations</p>
                                <p class="text-3xl font-bold">{{ stats.locations_count }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-50 dark:bg-green-900/20">
                                <MapPin class="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-muted-foreground">
                            <TrendingUp class="mr-1 h-4 w-4 text-green-600" />
                            Active locations available
                        </div>
                    </div>
                </div>

                <!-- Items Count Card -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Items</p>
                                <p class="text-3xl font-bold">{{ stats.items_count }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 dark:bg-purple-900/20">
                                <Package class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-muted-foreground">
                            <TrendingUp class="mr-1 h-4 w-4 text-green-600" />
                            Items tracked in inventory
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
