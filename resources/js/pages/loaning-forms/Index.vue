<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { PlusCircle, Eye, Pencil, Trash2, Check, X } from 'lucide-vue-next';
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

interface Item {
    id: number;
    name: string;
    owner?: User;
    manager?: User;
}

interface LoaningForm {
    id: number;
    status: 'requested' | 'approved' | 'rejected';
    start_at: string | null;
    end_at: string | null;
    created_at: string;
    item?: Item;
    applicant?: User;
}

interface PaginatedLoaningForms {
    data: LoaningForm[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    loaningForms: PaginatedLoaningForms;
}

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
        title: 'Loaning Forms',
        href: '/loaning-forms',
    },
];

// Function to get status badge variant
const getStatusBadge = (status: string) => {
    switch (status) {
        case 'requested':
            return { variant: 'secondary', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' };
        case 'approved':
            return { variant: 'success', class: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' };
        case 'rejected':
            return { variant: 'destructive', class: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' };
        default:
            return { variant: 'outline', class: '' };
    }
};
</script>

<template>
    <Head title="Loaning Forms" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Loaning Forms" description="Manage item loaning requests" />
                <Link :href="route('loaning-forms.create')" as="button">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Submit New Request
                    </Button>
                </Link>
            </div>

            <div class="overflow-x-auto border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black rounded-xl">
                <div class="p-6">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[15%]">ID</TableHead>
                                <TableHead class="w-[25%]">Item</TableHead>
                                <TableHead class="w-[20%]">Applicant</TableHead>
                                <TableHead class="w-[15%]">Status</TableHead>
                                <TableHead class="w-[15%]">Submitted</TableHead>
                                <TableHead class="w-[10%]">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="loaningForm in loaningForms.data" :key="loaningForm.id">
                                <TableCell class="font-medium">
                                    #{{ loaningForm.id }}
                                </TableCell>
                                <TableCell>
                                    <div class="truncate">
                                        <div class="font-medium">{{ loaningForm.item?.name || 'Unknown Item' }}</div>
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
                                    <Badge :class="getStatusBadge(loaningForm.status).class">
                                        {{ loaningForm.status.charAt(0).toUpperCase() + loaningForm.status.slice(1) }}
                                    </Badge>
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
                                        
                                        <Link 
                                            v-if="loaningForm.status === 'requested'" 
                                            :href="route('loaning-forms.edit', loaningForm.id)" 
                                            as="button"
                                        >
                                            <Button variant="outline" size="sm">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        
                                        <Link 
                                            v-if="loaningForm.status === 'requested'" 
                                            :href="route('loaning-forms.destroy', loaningForm.id)" 
                                            method="delete"
                                            as="button"
                                            :onBefore="() => confirm('Are you sure you want to delete this loaning form?')"
                                        >
                                            <Button variant="destructive" size="sm">
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div v-if="loaningForms.data.length === 0" class="py-8 text-center text-muted-foreground">
                        No loaning forms found. 
                        <Link :href="route('loaning-forms.create')" class="text-primary hover:underline">
                            Submit your first request
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination would go here if needed -->
            <div v-if="loaningForms.last_page > 1" class="flex justify-center">
                <div class="text-sm text-muted-foreground">
                    Showing {{ (loaningForms.current_page - 1) * loaningForms.per_page + 1 }} to 
                    {{ Math.min(loaningForms.current_page * loaningForms.per_page, loaningForms.total) }} 
                    of {{ loaningForms.total }} results
                </div>
            </div>
        </div>
    </AppLayout>
</template>
