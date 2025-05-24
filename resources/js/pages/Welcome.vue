<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Package, MapPin, Users, BarChart3, ShieldCheck, Zap } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => {
    const auth = page.props.auth as any;
    return auth?.user;
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="flex min-h-screen flex-col bg-background">        <!-- Header -->
        <header class="border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-2">
                        <AppLogoIcon class="size-8 fill-current text-foreground" />
                        <span class="text-lg font-semibold">Assets Management</span>
                    </div>
                    <nav class="flex items-center gap-4">
                        <Link v-if="isAuthenticated" :href="route('dashboard')">
                            <Button variant="outline">Dashboard</Button>
                        </Link>
                        <template v-else>
                            <Link :href="route('login')">
                                <Button variant="ghost">Log in</Button>
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1">
            <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6 pb-8 pt-6 md:pb-12 md:pt-10 lg:py-32">
                <div class="mx-auto flex max-w-[58rem] flex-col items-center space-y-4 text-center">
                    <h1 class="font-heading text-3xl font-bold leading-[1.1] sm:text-3xl md:text-6xl">
                        Streamline Your Asset Management
                    </h1>
                    <p class="max-w-[85%] leading-normal text-muted-foreground sm:text-lg sm:leading-7">
                        Comprehensive asset tracking and inventory management system. Monitor items, manage locations, 
                        and track loans with IoT integration for real-time environmental monitoring.
                    </p>
                    <div class="space-x-4">
                        <Link v-if="isAuthenticated" :href="route('dashboard')">
                            <Button size="lg" class="px-8">
                                Go to Dashboard
                            </Button>
                        </Link>
                        <template v-else>
                            <Link :href="route('login')">
                                <Button size="lg" class="px-8">
                                    Get Started
                                </Button>
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="bg-slate-50 py-8 dark:bg-transparent md:py-12 lg:py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                    <div class="mx-auto flex max-w-[58rem] flex-col items-center space-y-4 text-center">
                        <h2 class="font-heading text-2xl font-bold leading-[1.1] md:text-4xl">
                            Powerful Features
                        </h2>
                        <p class="max-w-[85%] leading-normal text-muted-foreground sm:text-lg sm:leading-7">
                            Everything you need to manage your assets efficiently and effectively.
                        </p>
                    </div>
                    <div class="mx-auto grid justify-center gap-4 sm:grid-cols-2 md:max-w-[64rem] md:grid-cols-3">
                    <!-- Asset Tracking -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                                <Package class="h-6 w-6 text-primary" />
                            </div>
                            <CardTitle>Asset Tracking</CardTitle>
                            <CardDescription>
                                Track and manage all your inventory items with detailed information, purchase dates, and status monitoring.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <!-- Location Management -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500/10">
                                <MapPin class="h-6 w-6 text-green-600" />
                            </div>
                            <CardTitle>Location Management</CardTitle>
                            <CardDescription>
                                Organize assets by location with IoT device integration for real-time temperature and humidity monitoring.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <!-- User Management -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                                <Users class="h-6 w-6 text-blue-600" />
                            </div>
                            <CardTitle>User Management</CardTitle>
                            <CardDescription>
                                Manage asset ownership, assign managers, and track responsibility with comprehensive user roles.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <!-- Analytics -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/10">
                                <BarChart3 class="h-6 w-6 text-purple-600" />
                            </div>
                            <CardTitle>Analytics & Reports</CardTitle>
                            <CardDescription>
                                Get insights into asset utilization, location statistics, and comprehensive inventory reports.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <!-- Security -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-500/10">
                                <ShieldCheck class="h-6 w-6 text-orange-600" />
                            </div>
                            <CardTitle>Secure & Reliable</CardTitle>
                            <CardDescription>
                                Built with Laravel security best practices and AWS Cognito authentication for enterprise-grade security.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <!-- IoT Integration -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-500/10">
                                <Zap class="h-6 w-6 text-yellow-600" />
                            </div>
                            <CardTitle>IoT Integration</CardTitle>
                            <CardDescription>
                                Monitor environmental conditions with real-time IoT sensor data for optimal asset storage conditions.
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
                </div>
            </section>

            <!-- Call to Action -->
            <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 md:py-12 lg:py-24">
                <div class="mx-auto flex max-w-[58rem] flex-col items-center space-y-4 text-center">
                    <h2 class="font-heading text-2xl font-bold leading-[1.1] md:text-4xl">
                        Ready to get started?
                    </h2>
                    <p class="max-w-[85%] leading-normal text-muted-foreground sm:text-lg sm:leading-7">
                        Join organizations that trust our asset management system to track and manage their valuable resources.
                    </p>
                    <div class="space-x-4">
                        <Link v-if="isAuthenticated" :href="route('dashboard')">
                            <Button size="lg" class="px-8">
                                Go to Dashboard
                            </Button>
                        </Link>
                        <template v-else>
                            <Link :href="route('login')">
                                <Button size="lg" class="px-8">
                                    Start Managing Assets
                                </Button>
                            </Link>
                        </template>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-border/40 py-6 md:py-0">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-between gap-4 md:h-16 md:flex-row">
                <div class="flex items-center gap-2">
                    <AppLogoIcon class="size-5 fill-current text-foreground" />
                    <span class="text-sm font-medium">Assets Management System</span>
                </div>
                <p class="text-center text-sm leading-loose text-muted-foreground md:text-left">
                    Built for efficient asset tracking and inventory management.
                </p>
            </div>
        </footer>
    </div>
</template>
