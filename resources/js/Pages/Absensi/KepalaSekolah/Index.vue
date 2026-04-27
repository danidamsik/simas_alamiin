<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    details: Object,
    kelasOptions: Array,
    periodeOptions: Array,
    filters: Object,
});

const filter = ref({
    tanggal: props.filters.tanggal ?? '',
    kelas_id: props.filters.kelas_id ?? '',
    periode_id: props.filters.periode_id ?? '',
});

const columns = [
    { key: 'tanggal', label: 'Tanggal' },
    { key: 'kelas', label: 'Kelas' },
    { key: 'siswa', label: 'Siswa' },
    { key: 'nis', label: 'NIS' },
    { key: 'status', label: 'Status' },
    { key: 'guru', label: 'Guru' },
    { key: 'session', label: 'Session' },
    { key: 'periode', label: 'Periode' },
];

const kelasSelectOptions = computed(() => props.kelasOptions.map((item) => ({ value: item.id, label: item.nama_kelas })));
const periodeSelectOptions = computed(() => props.periodeOptions.map((item) => ({
    value: item.id,
    label: `${item.tahun_ajaran} ${item.semester}${item.is_active ? ' - Aktif' : ''}`,
})));

let filterTimer;
watch(filter, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => router.get(route('absensi.index'), filter.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    }), 300);
}, { deep: true });

const time = (value) => (value ?? '').slice(0, 5);
const date = (value) => (value ?? '').slice(0, 10);
</script>

<template>
    <Head title="Data Absensi Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-heading text-lg font-semibold text-gray-950">Data Absensi Siswa</h1>
        </template>

        <div class="space-y-4">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="font-heading text-base font-semibold text-gray-950">Monitoring Absensi</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Data detail absensi per siswa. Halaman ini hanya untuk melihat data.
                </p>
            </section>

            <div class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-3">
                <AppSelect v-model="filter.periode_id" label="Periode" placeholder="Semua periode" :options="periodeSelectOptions" />
                <AppSelect v-model="filter.kelas_id" label="Kelas" placeholder="Semua kelas" :options="kelasSelectOptions" />
                <AppInput v-model="filter.tanggal" type="date" label="Tanggal" />
            </div>

            <DataTable
                :columns="columns"
                :rows="details.data"
                empty-text="Data absensi belum tersedia"
            >
                <template #cell-tanggal="{ row }">{{ date(row.absensi?.tanggal) }}</template>
                <template #cell-kelas="{ row }">{{ row.absensi?.kelas?.nama_kelas }}</template>
                <template #cell-siswa="{ row }">
                    <span class="font-medium text-gray-900">{{ row.siswa?.nama }}</span>
                </template>
                <template #cell-nis="{ row }">{{ row.siswa?.nis }}</template>
                <template #cell-status="{ row }"><StatusBadge :status="row.status" /></template>
                <template #cell-guru="{ row }">{{ row.absensi?.guru?.nama }}</template>
                <template #cell-session="{ row }">
                    {{ row.absensi?.session?.nama_sesi }}
                    <span class="text-gray-500">
                        ({{ time(row.absensi?.session?.jam_mulai) }}-{{ time(row.absensi?.session?.jam_selesai) }})
                    </span>
                </template>
                <template #cell-periode="{ row }">
                    {{ row.absensi?.periode?.tahun_ajaran }} {{ row.absensi?.periode?.semester }}
                </template>
            </DataTable>

            <Pagination :links="details.links" />
        </div>
    </AuthenticatedLayout>
</template>
