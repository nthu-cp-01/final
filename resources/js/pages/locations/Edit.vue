<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import Heading from '@/components/Heading.vue';

interface Location {
    id: number;
    name: string;
    description: string;
    deviceId: string;
    shadowName: string;
}

interface Props {
    location: Location;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Locations',
        href: '/locations',
    },
    {
        title: 'Edit',
        href: `/locations/${props.location.id}/edit`,
    },
];

const form = useForm({
    name: props.location.name,
    description: props.location.description,
    deviceId: props.location.deviceId,
    shadowName: props.location.shadowName,
});

const submit = () => {
    form.patch(route('locations.update', props.location.id));
};
</script>

<template>

    <Head title="Edit Location" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Edit Location" description="Update IoT device location details" />

            <div
                class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-black">
                <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                    <div>
                        <Label for="name">Location Name</Label>
                        <Input id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="4"
                            required />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div>
                        <Label for="deviceId">Thing Name</Label>
                        <Input id="deviceId" v-model="form.deviceId" type="text" class="mt-1 block w-full" required />
                        <p class="text-sm text-muted-foreground mt-1">
                            Unique identifier for the IoT device associated with this location
                        </p>
                        <InputError :message="form.errors.deviceId" />
                    </div>

                    <div>
                        <Label for="shadowName">Shadow Name</Label>
                        <Input id="shadowName" v-model="form.shadowName" type="text" class="mt-1 block w-full"
                            required />
                        <p class="text-sm text-muted-foreground mt-1">
                            Identifier for the IoT device shadow associated with this location
                        </p>
                        <InputError :message="form.errors.shadowName" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <Button :href="route('locations.index')" variant="outline" :disabled="form.processing">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Update Location
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
