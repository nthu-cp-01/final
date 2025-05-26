<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { onMounted, ref } from 'vue';
import { ArrowLeft, Printer, Download } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';

// Define the QRCodeStyling global from the library
declare const QRCodeStyling: any;

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
const qrCodeRefs = ref<{ [key: number]: any }>({});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: '/items',
    },
    {
        title: 'QR Codes',
        href: '#',
    },
];

// Print QR codes
const handlePrint = () => {
    window.print();
};

// Download all QR codes as PNG files
const downloadAll = async () => {
    for (const item of props.items) {
        const qrCodeInstance = qrCodeRefs.value[item.id];
        if (qrCodeInstance) {
            await qrCodeInstance.download({
                name: `item-${item.id}-qrcode`,
                extension: 'png'
            });
        }
    }
};

// Generate a single QR code
const generateQRCode = (item: Item, containerElement: HTMLElement) => {
    // Clear any existing content
    containerElement.innerHTML = '';

    // Create a unique container for this QR code
    const qrContainer = document.createElement('div');
    qrContainer.id = `qr-container-${item.id}`;
    containerElement.appendChild(qrContainer);

    // Create a new QR code instance with the item's ID
    try {
        const qrCode = new QRCodeStyling({
            width: 200,
            height: 200,
            type: "canvas",
            data: JSON.stringify({ id: item.id }),
            dotsOptions: {
                color: "#000000",
                type: "rounded"
            },
            cornersSquareOptions: {
                type: "extra-rounded"
            },
            backgroundOptions: {
                color: "#ffffff",
            },
            cornersDotOptions: {
                color: '#000000',
                type: 'dot',
            },
        });

        // Append the QR code to the container
        qrCode.append(qrContainer);

        // Store the reference for later use
        qrCodeRefs.value[item.id] = qrCode;

    } catch (error) {
        console.error(`Error generating QR code for item ${item.id}:`, error);
    }
};

onMounted(() => {
    // Log the items we're processing
    console.log(`QR Codes component mounted. Generating QR codes for ${props.items.length} items:`,
        props.items.map(item => item.id));

    // Use nextTick to ensure DOM is ready
    setTimeout(() => {
        // Process each item one by one
        props.items.forEach((item, index) => {
            // Add a small delay between each QR code generation
            setTimeout(() => {
                const qrCodeElement = document.getElementById(`qr-code-${item.id}`);
                console.log(`Processing item ${item.id}, found element:`, !!qrCodeElement);

                if (qrCodeElement) {
                    generateQRCode(item, qrCodeElement);
                    console.log(`QR code generated successfully for item ${item.id}`);
                } else {
                    console.error(`Element not found for item ${item.id}`);
                }
            }, index * 50); // Stagger QR code generation
        });
    }, 100);
});
</script>

<template>

    <Head title="Item QR Codes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Item QR Codes" description="Print or download QR codes for selected items" />
                <div class="flex gap-2">
                    <Button variant="outline" @click="handlePrint">
                        <Printer class="mr-2 h-4 w-4" />
                        Print QR Codes
                    </Button>
                    <Button variant="outline" @click="downloadAll">
                        <Download class="mr-2 h-4 w-4" />
                        Download All
                    </Button>
                    <Link :href="route('items.index')" as="button">
                    <Button>
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Items
                    </Button>
                    </Link>
                </div>
            </div>

            <div class="border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black rounded-xl">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 print:grid-cols-3">
                        <div v-for="item in items" :key="item.id"
                            class="flex flex-col items-center border border-gray-200 dark:border-gray-800 rounded-lg p-4 print:break-inside-avoid">
                            <div :id="`qr-code-${item.id}`" class="mb-4 w-[200px] h-[200px]"></div>
                            <div class="text-center">
                                <p class="font-semibold">{{ item.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ item.id }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Location: {{ item.location.name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@media print {

    button,
    a {
        display: none !important;
    }
}
</style>
