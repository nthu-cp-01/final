<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { ArrowLeft, Check, X } from 'lucide-vue-next';
import { formatDate } from '@/lib/utils';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Item {
    id: number;
    name: string;
    description: string;
    owner: User;
    manager: User;
}

interface LoaningForm {
    id: number;
    status: 'requested' | 'approved' | 'rejected';
    start_at: string | null;
    end_at: string | null;
    created_at: string;
    item: Item;
    applicant: User;
}

interface Props {
    loaningForm: LoaningForm;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Loaning Forms',
        href: '/loaning-forms',
    },
    {
        title: `Request #${props.loaningForm.id}`,
        href: `/loaning-forms/${props.loaningForm.id}`,
    },
];

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

const approve = () => {
    if (confirm('Are you sure you want to approve this loaning request?')) {
        useForm({}).post(route('loaning-forms.approve', props.loaningForm.id));
    }
};

const reject = () => {
    if (confirm('Are you sure you want to reject this loaning request?')) {
        useForm({}).post(route('loaning-forms.reject', props.loaningForm.id));
    }
};
</script>

<template>

    <Head title="Loaning Request Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Link :href="route('loaning-forms.index')" as="button">
                <Button variant="outline" size="sm">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back
                </Button>
                </Link>
                <Heading title="Loaning Request Details" :description="`Request #${loaningForm.id}`" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Request Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Request Information</CardTitle>
                        <CardDescription>Details about this loaning request</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Request ID</Label>
                                <p class="text-sm">#{{ loaningForm.id }}</p>
                            </div>
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Status</Label>
                                <div class="mt-1">
                                    <Badge :class="getStatusBadge(loaningForm.status).class">
                                        {{ loaningForm.status.charAt(0).toUpperCase() + loaningForm.status.slice(1) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Submitted By</Label>
                            <p class="text-sm">{{ loaningForm.applicant.name }}</p>
                            <p class="text-xs text-muted-foreground">{{ loaningForm.applicant.email }}</p>
                        </div>

                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Submitted On</Label>
                            <p class="text-sm">{{ formatDate(loaningForm.created_at) }}</p>
                        </div>

                        <div v-if="loaningForm.start_at && loaningForm.end_at" class="space-y-2">
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Loan Start Date</Label>
                                <p class="text-sm">{{ formatDate(loaningForm.start_at) }}</p>
                            </div>
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Loan End Date</Label>
                                <p class="text-sm">{{ formatDate(loaningForm.end_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Item Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Item Information</CardTitle>
                        <CardDescription>Details about the requested item</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Item Name</Label>
                            <p class="text-sm font-medium">{{ loaningForm.item.name }}</p>
                        </div>

                        <div v-if="loaningForm.item.description">
                            <Label class="text-sm font-medium text-muted-foreground">Description</Label>
                            <p class="text-sm">{{ loaningForm.item.description }}</p>
                        </div>

                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Current Owner</Label>
                            <p class="text-sm">{{ loaningForm.item.owner.name }}</p>
                            <p class="text-xs text-muted-foreground">{{ loaningForm.item.owner.email }}</p>
                        </div>

                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Manager</Label>
                            <p class="text-sm">{{ loaningForm.item.manager.name }}</p>
                            <p class="text-xs text-muted-foreground">{{ loaningForm.item.manager.email }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions (if status is requested) -->
                <Card v-if="loaningForm.status === 'requested'" class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Actions</CardTitle>
                        <CardDescription>Approve or reject this loaning request</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-4 justify-center">
                            <Button @click="approve" class="flex-1 max-w-xs">
                                <Check class="mr-2 h-4 w-4" />
                                Approve Request
                            </Button>

                            <Button @click="reject" variant="destructive" class="flex-1 max-w-xs">
                                <X class="mr-2 h-4 w-4" />
                                Reject Request
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
