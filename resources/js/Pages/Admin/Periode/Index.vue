<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, Loader2, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    periodes: Object,
});

const showModal = ref(false);
const editing = ref(null);
const deleteTarget = ref(null);
const deleting = ref(false);
const activeTarget = ref(null);
const shouldAutoFillDates = ref(false);

const semesterOptions = [
    { value: 'ganjil', label: 'Ganjil' },
    { value: 'genap', label: 'Genap' },
];

const form = useForm({
    tahun_ajaran: '',
    semester: 'ganjil',
    tanggal_mulai: '',
    tanggal_selesai: '',
    is_active: false,
});

const title = computed(() => editing.value ? 'Edit Periode' : 'Tambah Periode');
const dateFormatter = new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
});

const defaultDateRange = () => {
    const match = String(form.tahun_ajaran).match(/^(\d{4})\D+(\d{4})$/);

    if (!match) {
        return { tanggal_mulai: '', tanggal_selesai: '' };
    }

    return form.semester === 'ganjil'
        ? { tanggal_mulai: `${match[1]}-07-01`, tanggal_selesai: `${match[1]}-12-31` }
        : { tanggal_mulai: `${match[2]}-01-01`, tanggal_selesai: `${match[2]}-06-30` };
};

const applyDefaultDates = () => {
    const dates = defaultDateRange();

    form.tanggal_mulai = dates.tanggal_mulai;
    form.tanggal_selesai = dates.tanggal_selesai;
};

const date = (value) => value ? dateFormatter.format(new Date(`${value}T00:00:00`)) : '-';

const openCreate = () => {
    editing.value = null;
    shouldAutoFillDates.value = true;
    form.reset();
    form.semester = 'ganjil';
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (periode) => {
    editing.value = periode;
    shouldAutoFillDates.value = false;
    form.tahun_ajaran = periode.tahun_ajaran;
    form.semester = periode.semester;
    form.tanggal_mulai = periode.tanggal_mulai ?? '';
    form.tanggal_selesai = periode.tanggal_selesai ?? '';
    form.is_active = periode.is_active;
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => showModal.value = false,
    };

    editing.value
        ? form.put(route('periode.update', editing.value.id), options)
        : form.post(route('periode.store'), options);
};

const setActive = (periode) => {
    activeTarget.value = periode.id;
    router.patch(route('periode.set-active', periode.id), {}, {
        preserveScroll: true,
        onFinish: () => activeTarget.value = null,
    });
};
const openDelete = (periode) => {
    deleteTarget.value = periode;
};
const closeDelete = () => {
    if (!deleting.value) {
        deleteTarget.value = null;
    }
};
const destroy = () => {
    deleting.value = true;
    router.delete(route('periode.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => deleteTarget.value = null,
        onFinish: () => deleting.value = false,
    });
};

watch([() => form.tahun_ajaran, () => form.semester], () => {
    if (showModal.value && shouldAutoFillDates.value) {
        applyDefaultDates();
    }
});
</script>

<template>
    <Head title="Periode" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Periode</h1></template>

        <div class="space-y-4">
            <div class="flex justify-end">
                <AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton>
            </div>

            <DataTable
                :columns="[
                    { key: 'tahun_ajaran', label: 'Tahun Ajaran' },
                    { key: 'semester', label: 'Semester' },
                    { key: 'tanggal_mulai', label: 'Mulai' },
                    { key: 'tanggal_selesai', label: 'Selesai' },
                    { key: 'is_active', label: 'Status' },
                ]"
                :rows="periodes.data"
            >
                <template #cell-semester="{ value }">{{ value === 'ganjil' ? 'Ganjil' : 'Genap' }}</template>
                <template #cell-tanggal_mulai="{ value }">{{ date(value) }}</template>
                <template #cell-tanggal_selesai="{ value }">{{ date(value) }}</template>
                <template #cell-is_active="{ value }">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="value ? 'bg-primary-50 text-primary-700' : 'bg-gray-100 text-gray-600'">
                        {{ value ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </template>
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button
                            v-if="!row.is_active"
                            class="rounded-lg p-2 text-primary-700 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-60"
                            title="Set aktif"
                            :disabled="activeTarget === row.id"
                            @click="setActive(row)"
                        >
                            <Loader2 v-if="activeTarget === row.id" class="h-4 w-4 animate-spin" />
                            <Check v-else class="h-4 w-4" />
                        </button>
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)">
                            <Pencil class="h-4 w-4" />
                        </button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus" @click="openDelete(row)">
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="periodes.links" />
        </div>

        <Modal :show="showModal" max-width="md" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <AppInput v-model="form.tahun_ajaran" label="Tahun Ajaran" placeholder="2025/2026" :error="form.errors.tahun_ajaran" />
                <AppSelect v-model="form.semester" label="Semester" :options="semesterOptions" :error="form.errors.semester" />
                <div class="grid gap-3 sm:grid-cols-2">
                    <AppInput v-model="form.tanggal_mulai" type="date" label="Tanggal Mulai" :error="form.errors.tanggal_mulai" />
                    <AppInput v-model="form.tanggal_selesai" type="date" label="Tanggal Selesai" :error="form.errors.tanggal_selesai" />
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
                    Jadikan periode aktif
                </label>
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :loading="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
        <ConfirmModal
            :show="!!deleteTarget"
            title="Hapus Periode"
            :message="`Hapus periode ${deleteTarget?.tahun_ajaran ?? ''} ${deleteTarget?.semester ?? ''}?`"
            confirm-text="Ya, hapus"
            :processing="deleting"
            @close="closeDelete"
            @confirm="destroy"
        />
    </AuthenticatedLayout>
</template>
