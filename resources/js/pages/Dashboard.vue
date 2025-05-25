<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Users, MapPin, Package, TrendingUp, Check, X, Eye, FileText, Clock } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDate } from '@/lib/utils';
import { toast } from 'vue-sonner';
import { watch } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Item {
    id: number;
    name: string;
    owner?: User;
    manager?: User;
}

interface LoaningForm {
    id: number;
    status: 'requested' | 'approved' | 'rejected';
    created_at: string;
    item?: Item;
    applicant?: User;
}

interface Props {
    stats: {
        users_count: number;
        locations_count: number;
        items_count: number;
    };
    requestedLoaningForms: LoaningForm[];
}

//eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<Props>();
const page = usePage();

// Watch for flash messages from Inertia shared data
watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        toast.success(flash.success);
    }

    if (flash?.error) {
        toast.error(flash.error);
    }
}, { immediate: true });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Function to approve a loaning form
const approve = (loaningFormId: number) => {
    if (confirm('Are you sure you want to approve this loaning request?')) {
        useForm({}).post(route('loaning-forms.approve', loaningFormId));
    }
};

// Function to reject a loaning form
const reject = (loaningFormId: number) => {
    if (confirm('Are you sure you want to reject this loaning request?')) {
        useForm({}).post(route('loaning-forms.reject', loaningFormId));
    }
};
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Users Count Card -->
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Users</p>
                                <p class="text-3xl font-bold">{{ stats.users_count }}</p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/20">
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
                <Link :href="route('locations.index')" class="block">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black hover:border-green-300 dark:hover:border-green-600 transition-colors cursor-pointer">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Locations</p>
                                <p class="text-3xl font-bold">{{ stats.locations_count }}</p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-green-50 dark:bg-green-900/20">
                                <MapPin class="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-muted-foreground">
                            <TrendingUp class="mr-1 h-4 w-4 text-green-600" />
                            Active locations available
                        </div>
                    </div>
                </div>
                </Link>

                <!-- Items Count Card -->
                <Link :href="route('items.index')" class="block">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black hover:border-purple-300 dark:hover:border-purple-600 transition-colors cursor-pointer">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Items</p>
                                <p class="text-3xl font-bold">{{ stats.items_count }}</p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 dark:bg-purple-900/20">
                                <Package class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-muted-foreground">
                            <TrendingUp class="mr-1 h-4 w-4 text-green-600" />
                            Items tracked in inventory
                        </div>
                    </div>
                </div>
                </Link>
            </div>

            <!-- Pending Loaning Requests Section -->
            <div class="border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black rounded-xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-50 dark:bg-yellow-900/20">
                                <Clock class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Pending Loaning Requests</h3>
                                <p class="text-sm text-muted-foreground">Review and process item loaning requests</p>
                            </div>
                        </div>
                        <Link :href="route('loaning-forms.index')" as="button">
                        <Button variant="outline" size="sm">
                            <FileText class="mr-2 h-4 w-4" />
                            View All
                        </Button>
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <Table v-if="requestedLoaningForms.length > 0">
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[15%]">Request ID</TableHead>
                                    <TableHead class="w-[25%]">Item</TableHead>
                                    <TableHead class="w-[20%]">Applicant</TableHead>
                                    <TableHead class="w-[15%]">Submitted</TableHead>
                                    <TableHead class="w-[25%]">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="loaningForm in requestedLoaningForms" :key="loaningForm.id">
                                    <TableCell class="font-medium">
                                        #{{ loaningForm.id }}
                                    </TableCell>
                                    <TableCell>
                                        <div class="truncate">
                                            <div class="font-medium">{{ loaningForm.item?.name || 'Unknown Item' }}
                                            </div>
                                            <div class="text-sm text-muted-foreground">
                                                Owner: {{ loaningForm.item?.owner?.name || 'Unknown Owner' }}
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div class="truncate">
                                            {{ loaningForm.applicant?.name || 'Unknown Applicant' }}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        {{ formatDate(loaningForm.created_at) }}
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex justify-end gap-2">
                                            <Link :href="route('loaning-forms.show', loaningForm.id)" as="button">
                                            <Button variant="outline" size="sm">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                            </Link>

                                            <Button @click="approve(loaningForm.id)" variant="default" size="sm"
                                                class="bg-green-600 hover:bg-green-700 text-white">
                                                <Check class="h-4 w-4" />
                                                Approve
                                            </Button>

                                            <Button @click="reject(loaningForm.id)" variant="destructive" size="sm">
                                                <X class="h-4 w-4" />
                                                Decline
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <div v-else class="py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-green-50 dark:bg-green-900/20">
                                    <Check class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium">All caught up!</h4>
                                    <p class="text-sm text-muted-foreground">
                                        No pending loaning requests at the moment.
                                        <Link :href="route('loaning-forms.index')"
                                            class="text-primary hover:underline ml-1">
                                        View all requests
                                        </Link>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
