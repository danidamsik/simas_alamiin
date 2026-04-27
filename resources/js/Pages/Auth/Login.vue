<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Loader2, LogIn, School } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <div class="border-b border-gray-100 px-6 pb-6 pt-7 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-50 ring-1 ring-inset ring-primary-600/20">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
                    <School class="h-6 w-6" stroke-width="2.25" />
                </div>
            </div>
            <div class="mt-4 space-y-1">
                <h1 class="font-heading text-2xl font-semibold leading-7 text-gray-950">SIMAS Al-Amiin</h1>
                <p class="mx-auto max-w-xs text-sm font-medium leading-5 text-primary-700">
                    Membangun Disiplin Melalui Presensi Digital
                </p>
            </div>
        </div>

        <form class="space-y-5 px-6 py-6" @submit.prevent="submit">
            <div v-if="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ status }}
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-600 focus:ring-primary-600"
                    placeholder="admin@alamiin.sch.id"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-600 focus:ring-primary-600"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between gap-3">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm font-medium text-primary-700 hover:text-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2"
                >
                    Lupa password?
                </Link>
            </div>

            <button
                type="submit"
                class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-primary-600 bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="form.processing"
            >
                <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                <LogIn v-else class="h-4 w-4" />
                Masuk
            </button>
        </form>
    </GuestLayout>
</template>
