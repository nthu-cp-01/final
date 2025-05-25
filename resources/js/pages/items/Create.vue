<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import Heading from '@/components/Heading.vue';

interface User {
    id: number;
    name: string;
}

interface Location {
    id: number;
    name: string;
}

interface Props {
    users: User[];
    locations: Location[];
}

//eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<Props>();
const page = usePage();
const auth = page.props.auth as { user: User };
const currentUser = auth.user;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: '/items',
    },
    {
        title: 'Create',
        href: '/items/create',
    },
];

const form = useForm({
    name: '',
    purchase_date: new Date().toISOString().split('T')[0], // Default to today's date
    description: '',
    manager_id: String(currentUser?.id || ''),
    location_id: '',
    owner_id: String(currentUser?.id || ''),
    status: 'registered',
});

const submit = () => {
    form.post(route('items.store'));
};
</script>

<template>

    <Head title="Create Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Create Item" description="Add a new inventory item" />

            <div
                class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-black">
                <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                    <div>
                        <Label for="name">Item Name</Label>
                        <Input id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <Label for="purchase_date">Purchase Date</Label>
                        <Input id="purchase_date" v-model="form.purchase_date" type="date" class="mt-1 block w-full"
                            required />
                        <InputError :message="form.errors.purchase_date" />
                    </div>

                    <div>
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div>
                        <Label for="location_id">Location</Label>
                        <Select v-model="form.location_id" required>
                            <SelectTrigger class="mt-1 w-full">
                                <SelectValue placeholder="Select a location" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="location in locations" :key="location.id"
                                    :value="String(location.id)">
                                    {{ location.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.location_id" />
                    </div>

                    <div>
                        <Label for="manager_id">Manager</Label>
                        <Select v-model="form.manager_id" required>
                            <SelectTrigger class="mt-1 w-full">
                                <SelectValue placeholder="Select a manager" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">
                                    {{ user.name }}{{ currentUser && user.id === currentUser.id ? ' (You)' : '' }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.manager_id" />
                    </div>

                    <div>
                        <Label for="owner_id">Owner</Label>
                        <Select v-model="form.owner_id" required>
                            <SelectTrigger class="mt-1 w-full">
                                <SelectValue placeholder="Select an owner" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">
                                    {{ user.name }}{{ currentUser && user.id === currentUser.id ? ' (You)' : '' }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.owner_id" />
                    </div>

                    <div>
                        <Label for="status">Status</Label>
                        <Select v-model="form.status" required>
                            <SelectTrigger class="mt-1 w-full">
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="registered">Registered</SelectItem>
                                <SelectItem value="normal">Normal</SelectItem>
                                <SelectItem value="reserved">Reserved</SelectItem>
                                <SelectItem value="gone">Gone</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <Button :href="route('items.index')" variant="outline" :disabled="form.processing">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Create Item
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
