<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    jadwal: Object,
    students: Array,
    today: String,
});

const statusOptions = [
    { value: 'hadir', label: 'Hadir' },
    { value: 'izin', label: 'Izin' },
    { value: 'sakit', label: 'Sakit' },
    { value: 'alfa', label: 'Alfa' },
];

const form = useForm({
    details: props.students.map((student) => ({
        siswa_id: student.id,
        status: 'hadir',
    })),
});

const statusByStudent = computed(() => Object.fromEntries(form.details.map((detail) => [detail.siswa_id, detail])));
const time = (value) => (value ?? '').slice(0, 5);

const submit = () => {
    form.post(route('absensi.input.store', props.jadwal.id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Input Absensi" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Input Absensi</h1></template>

        <form class="space-y-4" @submit.prevent="submit">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <p class="font-heading text-lg font-semibold text-gray-950">{{ jadwal.mata_pelajaran }}</p>
                <p class="mt-1 text-sm text-gray-600">
                    {{ jadwal.kelas?.nama_kelas }} - {{ jadwal.guru?.nama }} - {{ jadwal.session?.nama_sesi }}
                    ({{ time(jadwal.session?.jam_mulai) }}-{{ time(jadwal.session?.jam_selesai) }})
                </p>
                <p class="mt-1 text-sm text-gray-500">Tanggal: {{ today }}</p>
                <p v-if="form.errors.jadwal || form.errors.details || form.errors.session || form.errors.hari || form.errors.periode" class="mt-3 text-sm text-red-600">
                    {{ form.errors.jadwal || form.errors.details || form.errors.session || form.errors.hari || form.errors.periode }}
                </p>
            </section>

            <DataTable
                :columns="[
                    { key: 'nama', label: 'Nama' },
                    { key: 'nis', label: 'NIS' },
                    { key: 'status', label: 'Status' },
                    { key: 'preview', label: 'Preview' },
                ]"
                :rows="students"
            >
                <template #cell-status="{ row }">
                    <AppSelect v-model="statusByStudent[row.id].status" placeholder="Pilih status" :options="statusOptions" />
                </template>
                <template #cell-preview="{ row }">
                    <StatusBadge :status="statusByStudent[row.id].status" />
                </template>
            </DataTable>

            <div class="flex justify-end">
                <AppButton type="submit" :loading="form.processing">
                    <template #icon><Save class="h-4 w-4" /></template>
                    Simpan Absensi
                </AppButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
