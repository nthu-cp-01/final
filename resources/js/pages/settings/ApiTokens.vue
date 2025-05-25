<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import QRCodeStyling from 'qr-code-styling';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { formatDate } from '@/lib/utils';
import { Copy, Eye, EyeOff, Trash2, Plus, QrCode } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface Token {
    id: number;
    name: string;
    abilities: string[];
    last_used_at: string | null;
    created_at: string;
}

interface Props {
    tokens: Token[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'API Token settings',
        href: '/settings/api-tokens',
    },
];

// Get the newly created token from flash data
const newToken = computed(() => {
    const token = page.props.newToken as { id: number; name: string; token: string } | null;
    return token;
});

// Use a reactive ref instead of computed for the token value
const tokenValue = ref('');

// Also track the raw page props for debugging
const debugPageProps = computed(() => {
    console.log('Full page props:', page.props);
    return page.props;
});

// Update token value when newToken changes
watch(newToken, (token) => {
    if (token?.token) {
        tokenValue.value = token.token;
    } else {
        console.log('No token found in newToken:', token);
    }
}, { immediate: true });

// Form for creating new tokens
const form = useForm({
    name: '',
});

// Token visibility and QR code management
const visibleTokens = ref<Set<number>>(new Set());
const qrCodeElements = ref<Map<number, HTMLDivElement>>(new Map());
const qrCodeInstances = ref<Map<number, QRCodeStyling>>(new Map());

// New token handling
const showNewToken = ref(false);
const showNewTokenQR = ref(false);
const newTokenQRElement = ref<HTMLDivElement | null>(null);
const newTokenQRInstance = ref<QRCodeStyling | null>(null);

const createToken = () => {
    form.post(route('api-tokens.store'), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset();
            showNewToken.value = true;
        },
        onError: (errors) => {
            console.error('Error creating token:', errors);
        },
    });
};

const deleteToken = (tokenId: number) => {
    if (confirm('Are you sure you want to delete this token? This action cannot be undone.')) {
        useForm({}).delete(route('api-tokens.destroy', tokenId), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Token deleted successfully');
            },
        });
    }
};

const toggleTokenVisibility = (tokenId: number) => {
    if (visibleTokens.value.has(tokenId)) {
        visibleTokens.value.delete(tokenId);
        hideQRCode(tokenId);
    } else {
        visibleTokens.value.add(tokenId);
    }
};

const toggleQRCode = (tokenId: number, token: string) => {
    const existing = qrCodeInstances.value.get(tokenId);
    if (existing) {
        hideQRCode(tokenId);
    } else {
        showQRCode(tokenId, token);
    }
};

const showQRCode = (tokenId: number, token: string) => {
    const element = qrCodeElements.value.get(tokenId);
    if (!element) return;

    const qrCode = new QRCodeStyling({
        width: 256,
        height: 256,
        type: 'svg',
        data: token,
        dotsOptions: {
            color: '#000000',
            type: 'rounded',
        },
        backgroundOptions: {
            color: '#ffffff',
        },
        cornersSquareOptions: {
            color: '#000000',
            type: 'extra-rounded',
        },
        cornersDotOptions: {
            color: '#000000',
            type: 'dot',
        },
    });

    qrCode.append(element);
    qrCodeInstances.value.set(tokenId, qrCode);
};

const hideQRCode = (tokenId: number) => {
    const element = qrCodeElements.value.get(tokenId);
    const instance = qrCodeInstances.value.get(tokenId);

    if (element) {
        element.innerHTML = '';
    }
    if (instance) {
        qrCodeInstances.value.delete(tokenId);
    }
};

const copyToClipboard = async (text: string) => {
    try {
        await navigator.clipboard.writeText(text);
        toast.success('Token copied to clipboard');
    } catch (err) {
        toast.error('Failed to copy token');
    }
};

const setQRElement = (tokenId: number, element: HTMLDivElement | null) => {
    if (element) {
        qrCodeElements.value.set(tokenId, element);
    }
};

// Handle new token QR code
const showNewTokenQRCode = () => {
    if (!newToken.value?.token) {
        return;
    }

    if (newTokenQRInstance.value) {
        if (newTokenQRElement.value) {
            newTokenQRElement.value.innerHTML = '';
        }
        newTokenQRInstance.value = null;
        showNewTokenQR.value = false;
        return;
    }

    // Set the flag first to show the QR container
    showNewTokenQR.value = true;

    // Wait for next tick to ensure DOM is updated
    nextTick(() => {
        if (!newTokenQRElement.value) {
            return;
        }

        const qrCode = new QRCodeStyling({
            width: 256,
            height: 256,
            type: 'svg',
            data: newToken.value!.token,
            dotsOptions: {
                color: '#000000',
                type: 'rounded',
            },
            backgroundOptions: {
                color: '#ffffff',
            },
            cornersSquareOptions: {
                color: '#000000',
                type: 'extra-rounded',
            },
            cornersDotOptions: {
                color: '#000000',
                type: 'dot',
            },
        });

        try {
            qrCode.append(newTokenQRElement.value);
            newTokenQRInstance.value = qrCode;
        } catch (error) {
            console.error('Error appending QR code:', error);
        }
    });
};

// Watch for new token and auto-show
watch(newToken, (token) => {
    if (token) {
        showNewToken.value = true;
    }
}, { immediate: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="API Token settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="API Tokens"
                    description="Create and manage API tokens for programmatic access to your account" />

                <!-- New Token Success Message -->
                <Card v-if="newToken && showNewToken"
                    class="border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950">
                    <CardHeader>
                        <CardTitle class="text-green-800 dark:text-green-200">Token Created Successfully</CardTitle>
                        <CardDescription class="text-green-700 dark:text-green-300">
                            Please copy this token now. For security reasons, you won't be able to see it again.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label class="text-green-800 dark:text-green-200">Token Name</Label>
                            <p class="font-medium text-green-900 dark:text-green-100">{{ newToken.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="new-token-value" class="text-green-800 dark:text-green-200">Token Value</Label>
                            <div class="flex gap-2">
                                <Input id="new-token-value" v-model="tokenValue" readonly
                                    class="font-mono text-sm bg-white dark:bg-gray-900 border-green-300 dark:border-green-700 text-gray-900 dark:text-gray-100" />
                                <Button @click="copyToClipboard(tokenValue)" variant="outline" size="sm">
                                    <Copy class="h-4 w-4" />
                                </Button>
                                <Button @click="showNewTokenQRCode" variant="outline" size="sm">
                                    <QrCode class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>

                        <!-- QR Code for new token -->
                        <div v-if="showNewTokenQR" class="flex justify-center pt-4">
                            <div class="p-4 bg-white rounded-lg border">
                                <div ref="newTokenQRElement"></div>
                                <p class="text-center text-sm text-muted-foreground mt-2">Scan to copy token</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <Button @click="showNewToken = false" variant="outline" size="sm">
                                Dismiss
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Create New Token Form -->
                <Card>
                    <CardHeader>
                        <CardTitle>Create New Token</CardTitle>
                        <CardDescription>
                            Generate a new API token to access your account programmatically.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="createToken" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Token Name</Label>
                                <Input id="name" v-model="form.name" type="text"
                                    placeholder="e.g., Mobile App, Script Access" required />
                                <InputError :message="form.errors.name" />
                            </div>

                            <Button type="submit" :disabled="form.processing">
                                <Plus class="h-4 w-4 mr-2" />
                                Create Token
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Existing Tokens -->
                <Card>
                    <CardHeader>
                        <CardTitle>Existing Tokens</CardTitle>
                        <CardDescription>
                            Manage your existing API tokens. You can revoke tokens that are no longer needed.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="tokens.length === 0" class="text-center py-8">
                            <p class="text-muted-foreground">No API tokens created yet.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead class="w-[25%]">Name</TableHead>
                                        <TableHead class="w-[20%]">Abilities</TableHead>
                                        <TableHead class="w-[20%]">Last Used</TableHead>
                                        <TableHead class="w-[20%]">Created</TableHead>
                                        <TableHead class="w-[15%]">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="token in tokens" :key="token.id">
                                        <TableCell class="font-medium">
                                            {{ token.name }}
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex flex-wrap gap-1">
                                                <Badge v-for="ability in token.abilities" :key="ability"
                                                    variant="secondary">
                                                    {{ ability === '*' ? 'All' : ability }}
                                                </Badge>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <span v-if="token.last_used_at" class="text-sm">
                                                {{ formatDate(token.last_used_at) }}
                                            </span>
                                            <span v-else class="text-sm text-muted-foreground">Never</span>
                                        </TableCell>
                                        <TableCell>
                                            <span class="text-sm">
                                                {{ formatDate(token.created_at) }}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex justify-end gap-2">
                                                <Button @click="deleteToken(token.id)" variant="outline" size="sm"
                                                    class="text-destructive hover:text-destructive-foreground hover:bg-destructive">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>

                <!-- Information Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>API Usage</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <h4 class="font-medium">Authentication</h4>
                            <p class="text-sm text-muted-foreground">
                                Include your API token in the Authorization header:
                            </p>
                            <div class="p-3 bg-muted rounded-md">
                                <code class="text-sm">Authorization: Bearer YOUR_TOKEN_HERE</code>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h4 class="font-medium">Example Request</h4>
                            <div class="p-3 bg-muted rounded-md">
                                <code class="text-sm">
                                    curl -H "Authorization: Bearer YOUR_TOKEN_HERE" https://final.test/api/user
                                </code>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
