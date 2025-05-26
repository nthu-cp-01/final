<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { PlusCircle, Pencil, Trash2, PackageCheck, BadgeMinus, BadgeAlert, Lock, Upload, QrCode } from 'lucide-vue-next';
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
import { watch, computed, reactive } from 'vue';
import { formatDate } from '@/lib/utils';
import { Checkbox } from '@/components/ui/checkbox';

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
    status: 'registered' | 'normal' | 'gone' | 'reserved';
    manager: User;
    owner: User;
    location: Location;
}

interface Props {
    items: Item[];
}


const props = defineProps<Props>();
const page = usePage();

// Using reactive for selectedItems with v-model support
const state = reactive({
    selectedItems: {} as Record<number, boolean>,
});

// Computed property to get selected item IDs
const selectedItemIds = computed(() => {
    return Object.keys(state.selectedItems)
        .filter(id => state.selectedItems[Number(id)])
        .map(id => Number(id));
});

// Computed property to get selected count
const selectedCount = computed(() => {
    return selectedItemIds.value.length;
});

// Check if all items are selected, some are selected (indeterminate), or none are selected
const allSelected = computed((): boolean | 'indeterminate' => {
    const selectedCount = selectedItemIds.value.length;
    const totalCount = props.items.length;
    
    if (selectedCount === 0) {
        return false; // No items selected
    } else if (selectedCount === totalCount && totalCount > 0) {
        return true; // All items selected
    } else {
        return 'indeterminate'; // Some items selected
    }
});

// Toggle all items selection
const toggleAllSelection = () => {
    if (allSelected.value === true) {
        // If all are selected, clear all selections
        state.selectedItems = {};
    } else {
        // If none or some are selected, select all items
        const newSelection: Record<number, boolean> = {};
        props.items.forEach(item => {
            newSelection[item.id] = true;
        });
        state.selectedItems = newSelection;
    }
};

// Handle QR code export
const exportQrCodes = () => {
    console.log('Export triggered, selected items:', selectedItemIds.value);
    if (selectedItemIds.value.length > 0) {
        toast.success(`Exporting ${selectedItemIds.value.length} QR code(s)`);
        router.visit(route('items.qrcodes'), {
            method: 'post',
            data: {
                items: selectedItemIds.value
            },
            preserveState: true
        });
    } else {
        toast.error('Please select at least one item to export');
    }
};

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
        title: 'Items',
        href: '/items',
    },
];

// Define the variants
type BadgeVariant = 'default' | 'secondary' | 'destructive' | 'outline';

// Function to get status badge variant
const getStatusBadge = (status: string) => {
    switch (status) {
        case 'registered':
            return { variant: 'secondary' as BadgeVariant, icon: BadgeAlert };
        case 'normal':
            // For compatibility with shadcn-vue badge component
            return { variant: 'default' as BadgeVariant, icon: PackageCheck };
        case 'gone':
            return { variant: 'destructive' as BadgeVariant, icon: BadgeMinus };
        case 'reserved':
            return { variant: 'default' as BadgeVariant, icon: Lock };
        default:
            return { variant: 'outline' as BadgeVariant, icon: null };
    }
};
</script>

<template>

    <Head title="Items" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Items" description="Manage inventory items" />
                <div class="flex gap-2">
                    <Button variant="outline" @click="exportQrCodes" v-show="selectedCount !== 0">
                        <QrCode class="mr-2 h-4 w-4" />
                        Export QR Codes <span v-if="selectedCount > 0">({{ selectedCount }})</span>
                    </Button>
                    <Link :href="route('items.import')" as="button">
                    <Button variant="outline">
                        <Upload class="mr-2 h-4 w-4" />
                        Import CSV
                    </Button>
                    </Link>
                    <Link :href="route('items.create')" as="button">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Add Item
                    </Button>
                    </Link>
                </div>
            </div>

            <div
                class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[5%]">
                                    <div class="flex items-center">
                                        <Checkbox :modelValue="allSelected" @update:modelValue="toggleAllSelection"
                                            :aria-label="allSelected === true ? 'Deselect all items' : 'Select all items'" />
                                    </div>
                                </TableHead>
                                <TableHead class="w-[15%]">Name</TableHead>
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
                                <TableCell colspan="8" class="text-center py-8">
                                    No items found. Click 'Add Item' to create one.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="item in items" :key="item.id">
                                <TableCell>
                                    <div class="flex items-center">
                                        <Checkbox v-model="state.selectedItems[item.id]"
                                            :aria-label="`Select item ${item.name}`" />
                                    </div>
                                </TableCell>
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
