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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Locations',
        href: '/locations',
    },
    {
        title: 'Create',
        href: '/locations/create',
    },
];

const form = useForm({
    name: '',
    description: '',
    deviceId: '',
    controllerShadowName: '',
    sensorShadowName: '',
});

const submit = () => {
    form.post(route('locations.store'));
};
</script>

<template>

    <Head title="Create Location" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Create Location" description="Add a new IoT device location" />

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
                        <Label for="controllerShadowName">Controller Shadow Name</Label>
                        <Input id="controllerShadowName" v-model="form.controllerShadowName" type="text" class="mt-1 block w-full"
                            required />
                        <p class="text-sm text-muted-foreground mt-1">
                            Identifier for the IoT device controller shadow that manages AC and dehumidifier
                        </p>
                        <InputError :message="form.errors.controllerShadowName" />
                    </div>

                    <div>
                        <Label for="sensorShadowName">Sensor Shadow Name</Label>
                        <Input id="sensorShadowName" v-model="form.sensorShadowName" type="text" class="mt-1 block w-full"
                            required />
                        <p class="text-sm text-muted-foreground mt-1">
                            Identifier for the IoT device sensor shadow that reports temperature and humidity
                        </p>
                        <InputError :message="form.errors.sensorShadowName" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <Button :href="route('locations.index')" variant="outline" :disabled="form.processing">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Create Location
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
