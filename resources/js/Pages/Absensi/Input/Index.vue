<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ClipboardCheck } from 'lucide-vue-next';

defineProps({
    activePeriode: Object,
    today: String,
    hari: String,
    jadwals: Array,
});

const time = (value) => (value ?? '').slice(0, 5);
const labelDay = (value) => value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
</script>

<template>
    <Head title="Input Absensi" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Input Absensi</h1></template>

        <div class="space-y-4">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-gray-950">{{ labelDay(hari) }}, {{ today }}</p>
                <p class="mt-1 text-sm text-gray-600">
                    Periode: {{ activePeriode ? `${activePeriode.tahun_ajaran} ${activePeriode.semester}` : 'Tidak ada periode aktif' }}
                </p>
            </section>

            <div v-if="!activePeriode" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                Tidak ada periode aktif. Admin harus mengaktifkan periode sebelum absensi dapat diinput.
            </div>

            <section class="grid gap-4 lg:grid-cols-2">
                <div v-if="jadwals.length === 0" class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-500 shadow-sm">
                    Tidak ada jadwal untuk hari ini.
                </div>
                <div v-for="jadwal in jadwals" :key="jadwal.id" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-heading text-base font-semibold text-gray-950">{{ jadwal.mata_pelajaran }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ jadwal.kelas?.nama_kelas }} - {{ jadwal.guru?.nama }}</p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ jadwal.session?.nama_sesi }} ({{ time(jadwal.session?.jam_mulai) }}-{{ time(jadwal.session?.jam_selesai) }})
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="jadwal.already_input ? 'bg-gray-100 text-gray-600' : 'bg-primary-50 text-primary-700'">
                            {{ jadwal.already_input ? 'Sudah input' : 'Belum input' }}
                        </span>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <Link v-if="!jadwal.already_input && jadwal.can_input_now" :href="route('absensi.input.create', jadwal.id)">
                            <AppButton><template #icon><ClipboardCheck class="h-4 w-4" /></template>Input</AppButton>
                        </Link>
                        <AppButton v-else variant="secondary" disabled>
                            {{ jadwal.already_input ? 'Sudah Diinput' : 'Di luar jam' }}
                        </AppButton>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
