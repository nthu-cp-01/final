<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Upload, FileText, Info } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: '/items',
    },
    {
        title: 'Import CSV',
        href: '/items-import',
    },
];

const form = useForm({
    csv_file: null as File | null,
});

const submit = () => {
    form.post(route('items.import.process'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            // Error handling is already handled by the form validation
        },
    });
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.csv_file = target.files[0];
    }
};
</script>

<template>
    <Head title="Import Items" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Import Items" description="Upload a CSV file to import multiple items at once" />

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Import Form -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Upload class="h-5 w-5" />
                            Upload CSV File
                        </CardTitle>
                        <CardDescription>
                            Select a CSV file containing item data to import
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <Label for="csv_file">CSV File</Label>
                                <Input 
                                    id="csv_file" 
                                    type="file" 
                                    accept=".csv,.txt"
                                    class="mt-1 block w-full" 
                                    required 
                                    @change="handleFileChange"
                                />
                                <InputError :message="form.errors.csv_file" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Maximum file size: 2MB. Supported formats: CSV, TXT
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Button :href="route('items.index')" variant="outline" :disabled="form.processing">
                                    Cancel
                                </Button>
                                <Button type="submit" :disabled="form.processing || !form.csv_file">
                                    <Upload class="mr-2 h-4 w-4" />
                                    {{ form.processing ? 'Importing...' : 'Import Items' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- Instructions -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            CSV Format Requirements
                        </CardTitle>
                        <CardDescription>
                            Your CSV file should include the following columns
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-3">
                            <div>
                                <h4 class="font-medium text-sm">Required Columns:</h4>
                                <ul class="mt-1 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li>• <code class="bg-muted px-1 rounded">name</code> - Item name</li>
                                    <li>• <code class="bg-muted px-1 rounded">location</code> - Location name (must exist)</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-sm">Optional Columns:</h4>
                                <ul class="mt-1 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li>• <code class="bg-muted px-1 rounded">description</code> - Item description</li>
                                    <li>• <code class="bg-muted px-1 rounded">purchase_date</code> - Purchase date (YYYY-MM-DD)</li>
                                    <li>• <code class="bg-muted px-1 rounded">manager</code> - Manager name or email</li>
                                    <li>• <code class="bg-muted px-1 rounded">owner</code> - Owner name or email</li>
                                    <li>• <code class="bg-muted px-1 rounded">status</code> - registered, normal, reserved, or gone</li>
                                </ul>
                            </div>
                        </div>

                        <Alert>
                            <Info class="h-4 w-4" />
                            <AlertDescription>
                                <strong>Default values:</strong> If manager, owner, or status are not specified, 
                                they will default to the current user (for manager/owner) and "registered" (for status).
                            </AlertDescription>
                        </Alert>

                        <div class="pt-4 border-t">
                            <h4 class="font-medium text-sm mb-2">Example CSV:</h4>
                            <div class="bg-muted p-3 rounded text-xs font-mono">
                                name,description,purchase_date,location,manager,owner,status<br>
                                Laptop A,Dell Laptop,2024-01-15,Office A,john@example.com,jane@example.com,normal<br>
                                Monitor B,24-inch Monitor,2024-02-01,Office B,,,reserved
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
