<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { FileSpreadsheet, FileText, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    kelasOptions: Array,
    periodeOptions: Array,
    filters: Object,
    report: Object,
    hasFilters: Boolean,
});

const page = usePage();
const generating = ref(false);
const initialPeriodeId = props.filters.periode_id ?? '';
const findPeriode = (periodeId) => props.periodeOptions.find((item) => String(item.id) === String(periodeId));
const initialPeriode = findPeriode(initialPeriodeId);
const form = ref({
    periode_id: initialPeriodeId,
    kelas_id: props.filters.kelas_id ?? '',
    tanggal_mulai: props.filters.tanggal_mulai || initialPeriode?.tanggal_mulai || '',
    tanggal_selesai: props.filters.tanggal_selesai || initialPeriode?.tanggal_selesai || '',
});

const columns = [
    { key: 'no', label: 'No' },
    { key: 'nama', label: 'Nama' },
    { key: 'nis', label: 'NIS' },
    { key: 'hadir', label: 'Total Hadir' },
    { key: 'izin', label: 'Izin' },
    { key: 'sakit', label: 'Sakit' },
    { key: 'alfa', label: 'Alfa' },
    { key: 'persentase', label: 'Persentase' },
];

const kelasSelectOptions = computed(() => props.kelasOptions.map((item) => ({ value: item.id, label: item.nama_kelas })));
const periodeSelectOptions = computed(() => props.periodeOptions.map((item) => ({
    value: item.id,
    label: `${item.tahun_ajaran} ${item.semester}${item.is_active ? ' - Aktif' : ''}`,
    tanggal_mulai: item.tanggal_mulai,
    tanggal_selesai: item.tanggal_selesai,
})));

const query = computed(() => Object.fromEntries(Object.entries(form.value).filter(([, value]) => value !== '' && value !== null)));
const rows = computed(() => props.report?.rows ?? []);
const hasRows = computed(() => rows.value.length > 0);
const errors = computed(() => page.props.errors ?? {});

const previewRows = computed(() => rows.value.map((row, index) => ({
    ...row,
    no: index + 1,
})));

const submit = () => {
    generating.value = true;
    router.get(route('laporan.index'), query.value, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => generating.value = false,
    });
};

watch(() => form.value.periode_id, (periodeId) => {
    const periode = findPeriode(periodeId);

    form.value.tanggal_mulai = periode?.tanggal_mulai || '';
    form.value.tanggal_selesai = periode?.tanggal_selesai || '';
});

const percent = (value) => `${Number(value ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 1 })}%`;
</script>

<template>
    <Head title="Laporan Absensi" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-heading text-lg font-semibold text-gray-950">Laporan Absensi</h1>
        </template>

        <div class="space-y-4">
            <form class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm" @submit.prevent="submit">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <AppSelect
                        v-model="form.periode_id"
                        label="Periode"
                        placeholder="Semua periode"
                        :options="periodeSelectOptions"
                        :error="errors.periode_id"
                    />
                    <AppSelect
                        v-model="form.kelas_id"
                        label="Kelas"
                        placeholder="Pilih kelas"
                        :options="kelasSelectOptions"
                        :error="errors.kelas_id"
                    />
                    <AppInput v-model="form.tanggal_mulai" type="date" label="Tanggal Mulai" :error="errors.tanggal_mulai" />
                    <AppInput v-model="form.tanggal_selesai" type="date" label="Tanggal Selesai" :error="errors.tanggal_selesai" />
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <AppButton type="submit" :loading="generating">
                        <template #icon><Search class="h-4 w-4" /></template>
                        Generate
                    </AppButton>

                    <div class="flex flex-wrap gap-2">
                        <a
                            :href="hasRows ? route('laporan.export.pdf', query) : '#'"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border px-4 text-sm font-semibold shadow-sm transition"
                            :class="hasRows ? 'border-red-600 bg-red-600 text-white hover:bg-red-700' : 'pointer-events-none border-gray-300 bg-gray-100 text-gray-400'"
                        >
                            <FileText class="h-4 w-4" />
                            PDF
                        </a>
                        <a
                            :href="hasRows ? route('laporan.export.excel', query) : '#'"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border px-4 text-sm font-semibold shadow-sm transition"
                            :class="hasRows ? 'border-primary-600 bg-primary-600 text-white hover:bg-primary-700' : 'pointer-events-none border-gray-300 bg-gray-100 text-gray-400'"
                        >
                            <FileSpreadsheet class="h-4 w-4" />
                            Excel
                        </a>
                    </div>
                </div>
            </form>

            <section v-if="report" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="font-heading text-base font-semibold text-gray-950">Preview Rekap</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ report.kelas.nama_kelas }} - {{ report.periode_label }}
                        </p>
                    </div>
                    <div class="grid grid-cols-4 gap-2 text-center sm:min-w-[360px]">
                        <div class="rounded-lg bg-green-50 p-3">
                            <p class="text-xs font-medium text-green-700">Hadir</p>
                            <p class="mt-1 font-semibold text-gray-950">{{ report.totals.hadir }}</p>
                        </div>
                        <div class="rounded-lg bg-yellow-50 p-3">
                            <p class="text-xs font-medium text-yellow-800">Izin</p>
                            <p class="mt-1 font-semibold text-gray-950">{{ report.totals.izin }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <p class="text-xs font-medium text-blue-700">Sakit</p>
                            <p class="mt-1 font-semibold text-gray-950">{{ report.totals.sakit }}</p>
                        </div>
                        <div class="rounded-lg bg-red-50 p-3">
                            <p class="text-xs font-medium text-red-700">Alfa</p>
                            <p class="mt-1 font-semibold text-gray-950">{{ report.totals.alfa }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div v-if="hasFilters && report && !hasRows" class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                Data absensi untuk filter yang dipilih kosong.
            </div>

            <DataTable
                v-if="report"
                :columns="columns"
                :rows="previewRows"
                empty-text="Data laporan belum tersedia"
            >
                <template #cell-persentase="{ row }">{{ percent(row.persentase) }}</template>
            </DataTable>
        </div>
    </AuthenticatedLayout>
</template>
