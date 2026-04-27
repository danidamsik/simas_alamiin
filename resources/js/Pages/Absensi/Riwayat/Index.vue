<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import AppButton from '@/Components/AppButton.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Pencil } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    absensis: Object,
    kelasOptions: Array,
    periodeOptions: Array,
    filters: Object,
});

const page = usePage();
const role = computed(() => page.props.auth.user?.role);
const canEditAbsensi = computed(() => ['admin', 'guru'].includes(role.value));
const filter = ref({
    tanggal: props.filters.tanggal ?? '',
    kelas_id: props.filters.kelas_id ?? '',
    periode_id: props.filters.periode_id ?? '',
});

const kelasSelectOptions = computed(() => props.kelasOptions.map((item) => ({ value: item.id, label: item.nama_kelas })));
const periodeSelectOptions = computed(() => props.periodeOptions.map((item) => ({
    value: item.id,
    label: `${item.tahun_ajaran} ${item.semester}${item.is_active ? ' - Aktif' : ''}`,
})));

let filterTimer;
watch(filter, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => router.get(route().current('absensi.index') ? route('absensi.index') : route('absensi.riwayat'), filter.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    }), 300);
}, { deep: true });

const time = (value) => (value ?? '').slice(0, 5);
const countStatus = (row, status) => row.absensi_detail?.filter((detail) => detail.status === status).length ?? 0;
const columns = computed(() => [
    { key: 'tanggal', label: 'Tanggal' },
    { key: 'kelas', label: 'Kelas' },
    { key: 'guru', label: 'Guru' },
    { key: 'session', label: 'Session' },
    { key: 'rekap', label: 'Rekap' },
    ...(canEditAbsensi.value ? [{ key: 'aksi', label: 'Aksi' }] : []),
]);
</script>

<template>
    <Head title="Riwayat Absensi" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Riwayat Absensi</h1></template>

        <div class="space-y-4">
            <div class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-3">
                <AppInput v-model="filter.tanggal" type="date" label="Tanggal" />
                <AppSelect v-model="filter.kelas_id" label="Kelas" placeholder="Semua kelas" :options="kelasSelectOptions" />
                <AppSelect v-model="filter.periode_id" label="Periode" placeholder="Semua periode" :options="periodeSelectOptions" />
            </div>

            <DataTable
                :columns="columns"
                :rows="absensis.data"
            >
                <template #cell-kelas="{ row }">{{ row.kelas?.nama_kelas }}</template>
                <template #cell-guru="{ row }">{{ row.guru?.nama }}</template>
                <template #cell-session="{ row }">{{ row.session?.nama_sesi }} ({{ time(row.session?.jam_mulai) }}-{{ time(row.session?.jam_selesai) }})</template>
                <template #cell-rekap="{ row }">
                    H: {{ countStatus(row, 'hadir') }},
                    I: {{ countStatus(row, 'izin') }},
                    S: {{ countStatus(row, 'sakit') }},
                    A: {{ countStatus(row, 'alfa') }}
                </template>
                <template #cell-aksi="{ row }">
                    <Link :href="route('absensi.edit', row.id)">
                        <AppButton variant="secondary" size="sm">
                            <template #icon><Pencil class="h-4 w-4" /></template>
                            Edit
                        </AppButton>
                    </Link>
                </template>
            </DataTable>
            <Pagination :links="absensis.links" />
        </div>
    </AuthenticatedLayout>
</template>
