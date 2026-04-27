<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { CalendarDays, ClipboardCheck, FileSpreadsheet, Users } from 'lucide-vue-next';

const page = usePage();

const role = computed(() => page.props.auth.user?.role);
const userName = computed(() => page.props.auth.user?.name ?? 'Pengguna');
const roleLabel = computed(() => ({
    admin: 'Admin',
    guru: 'Guru',
    kepala_sekolah: 'Kepala Sekolah',
}[role.value] ?? 'Pengguna'));

const roleCopy = computed(() => ({
    admin: 'Kelola data master, jadwal, absensi, dan laporan sekolah dari satu tempat.',
    guru: 'Pantau jadwal mengajar dan proses input absensi kelas yang menjadi tanggung jawab Anda.',
    kepala_sekolah: 'Lihat ringkasan absensi dan laporan untuk kebutuhan monitoring sekolah.',
}[role.value] ?? 'Selamat datang di SIMAS Al-Amiin.'));

const quickStats = [
    { label: 'Siswa Aktif', value: '70', icon: Users },
    { label: 'Jadwal', value: '40', icon: CalendarDays },
    { label: 'Absensi', value: '40', icon: ClipboardCheck },
    { label: 'Laporan', value: 'PDF/XLSX', icon: FileSpreadsheet },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-heading text-lg font-semibold text-gray-950">Dashboard</h1>
        </template>

        <div class="space-y-6">
            <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-primary-600">SIMAS Al-Amiin</p>
                        <h2 class="mt-1 font-heading text-2xl font-semibold text-gray-950">Halo, {{ userName }}</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">{{ roleCopy }}</p>
                    </div>
                    <span class="inline-flex w-fit items-center rounded-full bg-primary-50 px-3 py-1 text-sm font-semibold text-primary-700 ring-1 ring-inset ring-primary-600/20">
                        {{ roleLabel }}
                    </span>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="stat in quickStats"
                    :key="stat.label"
                    class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ stat.label }}</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-950">{{ stat.value }}</p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                            <component :is="stat.icon" class="h-5 w-5" />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
