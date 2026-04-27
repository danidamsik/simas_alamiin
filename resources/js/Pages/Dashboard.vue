<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    CalendarClock,
    CheckCircle2,
    ClipboardCheck,
    Clock3,
    Users,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    role: String,
    today: String,
    hari: String,
    activePeriode: Object,
    schoolDashboard: Object,
    guruDashboard: Object,
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name ?? 'Pengguna');

const roleLabel = computed(() => ({
    admin: 'Admin',
    guru: 'Guru',
    kepala_sekolah: 'Kepala Sekolah',
}[props.role] ?? 'Pengguna'));

const statusLabels = {
    hadir: 'Hadir',
    izin: 'Izin',
    sakit: 'Sakit',
    alfa: 'Alfa',
};

const statusStyles = {
    hadir: {
        icon: CheckCircle2,
        border: 'border-green-200',
        bg: 'bg-green-50',
        text: 'text-green-700',
        bar: 'bg-green-500',
    },
    izin: {
        icon: CalendarClock,
        border: 'border-yellow-200',
        bg: 'bg-yellow-50',
        text: 'text-yellow-800',
        bar: 'bg-yellow-400',
    },
    sakit: {
        icon: ClipboardCheck,
        border: 'border-blue-200',
        bg: 'bg-blue-50',
        text: 'text-blue-700',
        bar: 'bg-blue-500',
    },
    alfa: {
        icon: XCircle,
        border: 'border-red-200',
        bg: 'bg-red-50',
        text: 'text-red-700',
        bar: 'bg-red-500',
    },
};

const titleCopy = computed(() => ({
    admin: 'Ringkasan operasional absensi sekolah hari ini.',
    guru: 'Jadwal mengajar dan status input absensi hari ini.',
    kepala_sekolah: 'Ringkasan monitoring kehadiran siswa hari ini.',
}[props.role] ?? 'SIMAS Al-Amiin'));

const school = computed(() => props.schoolDashboard ?? {
    totalSiswaAktif: 0,
    todayCounts: { hadir: 0, izin: 0, sakit: 0, alfa: 0 },
    todayPercentage: 0,
    trend: [],
    lowestClasses: [],
});

const guru = computed(() => props.guruDashboard ?? { jadwals: [] });
const isGuru = computed(() => props.role === 'guru');
const activePeriodeLabel = computed(() => props.activePeriode
    ? `${props.activePeriode.tahun_ajaran} ${props.activePeriode.semester}`
    : 'Tidak ada periode aktif');

const todayStatusCards = computed(() => Object.keys(statusLabels).map((status) => ({
    status,
    label: statusLabels[status],
    value: school.value.todayCounts?.[status] ?? 0,
    ...statusStyles[status],
})));

const maxTrendTotal = computed(() => Math.max(
    ...school.value.trend.map((day) => Object.values(day.counts ?? {}).reduce((sum, value) => sum + Number(value), 0)),
    1,
));

const totalJadwalGuru = computed(() => guru.value.jadwals?.length ?? 0);
const totalSudahInput = computed(() => guru.value.jadwals?.filter((jadwal) => jadwal.already_input).length ?? 0);

const labelDay = (value) => value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
const percent = (value) => `${Number(value ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 1 })}%`;
const cssPercent = (value) => `${Number(value ?? 0)}%`;
const time = (value) => (value ?? '').slice(0, 5);
const trendHeight = (value) => `${Math.max((Number(value) / maxTrendTotal.value) * 100, value > 0 ? 8 : 0)}%`;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-heading text-lg font-semibold text-gray-950">Dashboard</h1>
        </template>

        <div class="space-y-6">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-primary-600">SIMAS Al-Amiin</p>
                        <h2 class="mt-1 font-heading text-2xl font-semibold text-gray-950">Halo, {{ userName }}</h2>
                        <p class="mt-2 text-sm text-gray-600">{{ titleCopy }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="rounded-full bg-primary-50 px-3 py-1 font-semibold text-primary-700 ring-1 ring-inset ring-primary-600/20">
                            {{ roleLabel }}
                        </span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 font-semibold text-gray-700">
                            {{ labelDay(hari) }}, {{ today }}
                        </span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 font-semibold text-gray-700">
                            {{ activePeriodeLabel }}
                        </span>
                    </div>
                </div>
            </section>

            <template v-if="!isGuru">
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Siswa Aktif</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-950">{{ school.totalSiswaAktif }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                <Users class="h-5 w-5" />
                            </div>
                        </div>
                    </div>

                    <div
                        v-for="card in todayStatusCards"
                        :key="card.status"
                        class="rounded-lg border bg-white p-5 shadow-sm"
                        :class="card.border"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ card.label }} Hari Ini</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-950">{{ card.value }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg" :class="[card.bg, card.text]">
                                <component :is="card.icon" class="h-5 w-5" />
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 lg:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Persentase Kehadiran</p>
                                <p class="mt-2 text-3xl font-semibold text-gray-950">{{ percent(school.todayPercentage) }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-50 text-primary-700">
                                <BarChart3 class="h-5 w-5" />
                            </div>
                        </div>
                        <div class="mt-5 h-3 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-primary-600" :style="{ width: cssPercent(school.todayPercentage) }" />
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-heading text-base font-semibold text-gray-950">Tren 7 Hari</h3>
                                <p class="mt-1 text-sm text-gray-500">Rekap per status</p>
                            </div>
                            <div class="hidden flex-wrap gap-2 sm:flex">
                                <StatusBadge v-for="status in Object.keys(statusLabels)" :key="status" :status="status" />
                            </div>
                        </div>

                        <div class="mt-6 grid h-64 grid-cols-7 gap-3">
                            <div v-for="day in school.trend" :key="day.date" class="flex min-w-0 flex-col items-center gap-2">
                                <div class="flex h-48 w-full items-end justify-center gap-1 rounded-lg bg-gray-50 px-2 py-2">
                                    <div
                                        v-for="status in Object.keys(statusLabels)"
                                        :key="status"
                                        class="w-full rounded-t"
                                        :class="statusStyles[status].bar"
                                        :style="{ height: trendHeight(day.counts?.[status] ?? 0) }"
                                        :title="`${statusLabels[status]}: ${day.counts?.[status] ?? 0}`"
                                    />
                                </div>
                                <p class="truncate text-xs font-medium text-gray-500">{{ day.label }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="font-heading text-base font-semibold text-gray-950">5 Kelas Kehadiran Terendah</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Kelas</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Hadir</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Tidak Hadir</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="school.lowestClasses.length === 0">
                                    <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">Data absensi hari ini belum tersedia.</td>
                                </tr>
                                <tr v-for="kelas in school.lowestClasses" :key="kelas.id">
                                    <td class="whitespace-nowrap px-5 py-3 text-sm font-semibold text-gray-900">{{ kelas.nama_kelas }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm text-gray-700">{{ kelas.counts.hadir }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm text-gray-700">{{ kelas.total - kelas.counts.hadir }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-sm font-semibold text-gray-900">{{ percent(kelas.attendance_percentage) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </template>

            <template v-else>
                <section class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Jadwal Hari Ini</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-950">{{ totalJadwalGuru }}</p>
                    </div>
                    <div class="rounded-lg border border-green-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Sudah Diinput</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-950">{{ totalSudahInput }}</p>
                    </div>
                    <div class="rounded-lg border border-yellow-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Belum Diinput</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-950">{{ Math.max(totalJadwalGuru - totalSudahInput, 0) }}</p>
                    </div>
                </section>

                <section class="grid gap-4 lg:grid-cols-2">
                    <div v-if="guru.jadwals.length === 0" class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-500 shadow-sm">
                        Tidak ada jadwal mengajar hari ini.
                    </div>
                    <div v-for="jadwal in guru.jadwals" :key="jadwal.id" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-heading text-base font-semibold text-gray-950">{{ jadwal.mata_pelajaran }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ jadwal.kelas?.nama_kelas }}</p>
                                <p class="mt-1 inline-flex items-center gap-2 text-sm text-gray-500">
                                    <Clock3 class="h-4 w-4" />
                                    {{ jadwal.session?.nama_sesi }} ({{ time(jadwal.session?.jam_mulai) }}-{{ time(jadwal.session?.jam_selesai) }})
                                </p>
                            </div>
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="jadwal.already_input ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800'"
                            >
                                {{ jadwal.already_input ? 'Sudah input' : 'Belum input' }}
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs font-medium text-gray-500">Hadir</p>
                                <p class="mt-1 text-xl font-semibold text-gray-950">{{ jadwal.total_hadir }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs font-medium text-gray-500">Tidak Hadir</p>
                                <p class="mt-1 text-xl font-semibold text-gray-950">{{ jadwal.total_tidak_hadir }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <Link
                                v-if="jadwal.already_input && jadwal.absensi_id"
                                :href="route('absensi.edit', jadwal.absensi_id)"
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                            >
                                Lihat Absensi
                            </Link>
                            <Link
                                v-else
                                :href="route('absensi.input.index')"
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-primary-600 bg-primary-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-primary-700"
                            >
                                Input Absensi
                            </Link>
                        </div>
                    </div>
                </section>
            </template>
        </div>
    </AuthenticatedLayout>
</template>
