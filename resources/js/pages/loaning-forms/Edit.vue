<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { ArrowLeft } from 'lucide-vue-next';

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
    item_id: number;
    status: string;
}

interface Props {
    loaningForm: LoaningForm;
    items: Item[];
}

 
const props = defineProps<Props>();

const form = useForm({
    item_id: props.loaningForm.item_id.toString(),
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Loaning Forms',
        href: '/loaning-forms',
    },
    {
        title: `Edit #${props.loaningForm.id}`,
        href: `/loaning-forms/${props.loaningForm.id}/edit`,
    },
];

const submit = () => {
    form.put(route('loaning-forms.update', props.loaningForm.id));
};
</script>

<template>

    <Head title="Edit Loaning Request" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center gap-4">
                <Link :href="route('loaning-forms.index')" as="button">
                <Button variant="outline" size="sm">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back
                </Button>
                </Link>
                <Heading title="Edit Loaning Request" :description="`Edit loaning request #${loaningForm.id}`" />
            </div>

            <div class="border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black rounded-xl">
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="item_id">Select Item to Borrow</Label>
                            <Select v-model="form.item_id" required>
                                <SelectTrigger>
                                    <SelectValue placeholder="Choose an item..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="item in items" :key="item.id" :value="item.id.toString()">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ item.name }}</span>
                                            <span class="text-sm text-muted-foreground">
                                                Owner: {{ item.owner.name }} | Manager: {{ item.manager.name }}
                                            </span>
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.item_id" class="text-sm text-destructive">
                                {{ form.errors.item_id }}
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link :href="route('loaning-forms.index')" as="button">
                            <Button variant="outline">Cancel</Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                Update Request
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
