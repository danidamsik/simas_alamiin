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
import { Pencil, Plus, Search, UserX } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    siswas: Object,
    kelasOptions: Array,
    filters: Object,
});

const showModal = ref(false);
const editing = ref(null);
const deactivateTarget = ref(null);
const deactivating = ref(false);
const filter = ref({
    search: props.filters.search ?? '',
    kelas_id: props.filters.kelas_id ?? '',
    status: props.filters.status ?? 'active',
});

const kelasSelectOptions = computed(() => props.kelasOptions.map((kelas) => ({ value: kelas.id, label: kelas.nama_kelas })));
const statusOptions = [
    { value: 'active', label: 'Aktif' },
    { value: 'all', label: 'Semua' },
];

const form = useForm({
    nama: '',
    nis: '',
    kelas_id: '',
    is_active: true,
    keterangan: '',
});

const title = computed(() => editing.value ? 'Edit Siswa' : 'Tambah Siswa');

let filterTimer;
watch(filter, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => {
        router.get(route('siswa.index'), filter.value, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
}, { deep: true });

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.is_active = true;
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (siswa) => {
    editing.value = siswa;
    form.nama = siswa.nama;
    form.nis = siswa.nis;
    form.kelas_id = siswa.kelas_id;
    form.is_active = siswa.is_active;
    form.keterangan = '';
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => showModal.value = false };
    editing.value ? form.put(route('siswa.update', editing.value.id), options) : form.post(route('siswa.store'), options);
};

const openDeactivate = (siswa) => {
    deactivateTarget.value = siswa;
};
const closeDeactivate = () => {
    if (!deactivating.value) {
        deactivateTarget.value = null;
    }
};
const deactivate = () => {
    deactivating.value = true;
    router.delete(route('siswa.destroy', deactivateTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => deactivateTarget.value = null,
        onFinish: () => deactivating.value = false,
    });
};
</script>

<template>
    <Head title="Siswa" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Siswa</h1></template>
        <div class="space-y-4">
            <div class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_160px_auto]">
                <AppInput v-model="filter.search" placeholder="Cari nama atau NIS" label="Pencarian" />
                <AppSelect v-model="filter.kelas_id" label="Kelas" placeholder="Semua kelas" :options="kelasSelectOptions" />
                <AppSelect v-model="filter.status" label="Status" :options="statusOptions" />
                <div class="flex items-end justify-end gap-2">
                    <AppButton variant="secondary" title="Cari"><template #icon><Search class="h-4 w-4" /></template></AppButton>
                    <AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton>
                </div>
            </div>

            <DataTable
                :columns="[
                    { key: 'nama', label: 'Nama' },
                    { key: 'nis', label: 'NIS' },
                    { key: 'kelas', label: 'Kelas' },
                    { key: 'is_active', label: 'Status' },
                ]"
                :rows="siswas.data"
            >
                <template #cell-kelas="{ row }">{{ row.kelas?.nama_kelas }}</template>
                <template #cell-is_active="{ value }">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="value ? 'bg-primary-50 text-primary-700' : 'bg-gray-100 text-gray-600'">
                        {{ value ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </template>
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)"><Pencil class="h-4 w-4" /></button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Nonaktifkan" @click="openDeactivate(row)"><UserX class="h-4 w-4" /></button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="siswas.links" />
        </div>

        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <AppInput v-model="form.nama" label="Nama" :error="form.errors.nama" />
                    <AppInput v-model="form.nis" label="NIS" :error="form.errors.nis" />
                    <AppSelect v-model="form.kelas_id" label="Kelas" :options="kelasSelectOptions" :error="form.errors.kelas_id" />
                    <label class="flex items-center gap-2 pt-7 text-sm text-gray-700">
                        <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
                        Siswa aktif
                    </label>
                </div>
                <AppInput v-if="editing" v-model="form.keterangan" label="Keterangan pindah kelas" placeholder="pindah kelas" :error="form.errors.keterangan" />
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :loading="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
        <ConfirmModal
            :show="!!deactivateTarget"
            title="Nonaktifkan Siswa"
            :message="`Nonaktifkan siswa ${deactivateTarget?.nama ?? ''}? Siswa tidak akan muncul di daftar absensi kelas aktif.`"
            confirm-text="Ya, nonaktifkan"
            :processing="deactivating"
            @close="closeDeactivate"
            @confirm="deactivate"
        />
    </AuthenticatedLayout>
</template>
