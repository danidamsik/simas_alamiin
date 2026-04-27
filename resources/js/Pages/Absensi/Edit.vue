<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    absensi: Object,
    canEdit: Boolean,
    lockedReason: String,
});

const page = usePage();
const backRoute = computed(() => page.props.auth.user?.role === 'admin' ? route('absensi.index') : route('absensi.riwayat'));
const statusOptions = [
    { value: 'hadir', label: 'Hadir' },
    { value: 'izin', label: 'Izin' },
    { value: 'sakit', label: 'Sakit' },
    { value: 'alfa', label: 'Alfa' },
];

const details = computed(() => props.absensi.absensi_detail ?? []);
const form = useForm({
    details: details.value.map((detail) => ({
        siswa_id: detail.siswa_id,
        status: detail.status,
    })),
});

const statusByStudent = computed(() => Object.fromEntries(form.details.map((detail) => [detail.siswa_id, detail])));
const time = (value) => (value ?? '').slice(0, 5);
const date = (value) => (value ?? '').slice(0, 10);

const submit = () => {
    if (!props.canEdit) {
        return;
    }

    form.put(route('absensi.update', props.absensi.id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Edit Absensi" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Edit Absensi</h1></template>

        <form class="space-y-4" @submit.prevent="submit">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="font-heading text-lg font-semibold text-gray-950">{{ absensi.kelas?.nama_kelas }}</p>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ absensi.guru?.nama }} - {{ absensi.session?.nama_sesi }}
                            ({{ time(absensi.session?.jam_mulai) }}-{{ time(absensi.session?.jam_selesai) }})
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Tanggal: {{ date(absensi.tanggal) }} - Periode: {{ absensi.periode?.tahun_ajaran }} {{ absensi.periode?.semester }}
                        </p>
                    </div>
                    <Link :href="backRoute">
                        <AppButton variant="secondary" type="button">
                            <template #icon><ArrowLeft class="h-4 w-4" /></template>
                            Kembali
                        </AppButton>
                    </Link>
                </div>

                <p v-if="!canEdit" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-800">
                    {{ lockedReason }}
                </p>
                <p v-if="form.errors.edit || form.errors.details" class="mt-3 text-sm text-red-600">
                    {{ form.errors.edit || form.errors.details }}
                </p>
            </section>

            <DataTable
                :columns="[
                    { key: 'nama', label: 'Nama' },
                    { key: 'nis', label: 'NIS' },
                    { key: 'status', label: 'Status' },
                    { key: 'preview', label: 'Preview' },
                ]"
                :rows="details"
            >
                <template #cell-nama="{ row }">{{ row.siswa?.nama }}</template>
                <template #cell-nis="{ row }">{{ row.siswa?.nis }}</template>
                <template #cell-status="{ row }">
                    <AppSelect
                        v-model="statusByStudent[row.siswa_id].status"
                        placeholder="Pilih status"
                        :options="statusOptions"
                        :disabled="!canEdit || form.processing"
                    />
                </template>
                <template #cell-preview="{ row }">
                    <StatusBadge :status="statusByStudent[row.siswa_id].status" />
                </template>
            </DataTable>

            <div class="flex justify-end">
                <AppButton type="submit" :disabled="!canEdit || form.processing">
                    <template #icon><Save class="h-4 w-4" /></template>
                    Simpan Perubahan
                </AppButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
