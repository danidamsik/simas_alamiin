<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineProps({ kelas: Object });

const showModal = ref(false);
const editing = ref(null);
const form = useForm({ nama_kelas: '' });
const title = computed(() => editing.value ? 'Edit Kelas' : 'Tambah Kelas');

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (kelas) => {
    editing.value = kelas;
    form.nama_kelas = kelas.nama_kelas;
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => showModal.value = false };
    editing.value ? form.put(route('kelas.update', editing.value.id), options) : form.post(route('kelas.store'), options);
};

const destroy = (kelas) => {
    if (confirm(`Hapus kelas ${kelas.nama_kelas}?`)) {
        router.delete(route('kelas.destroy', kelas.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Kelas" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Kelas</h1></template>
        <div class="space-y-4">
            <div class="flex justify-end"><AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton></div>
            <DataTable
                :columns="[
                    { key: 'nama_kelas', label: 'Nama Kelas' },
                    { key: 'siswa_count', label: 'Siswa' },
                    { key: 'jadwal_count', label: 'Jadwal' },
                ]"
                :rows="kelas.data"
            >
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)"><Pencil class="h-4 w-4" /></button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus" @click="destroy(row)"><Trash2 class="h-4 w-4" /></button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="kelas.links" />
        </div>
        <Modal :show="showModal" max-width="md" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <AppInput v-model="form.nama_kelas" label="Nama Kelas" placeholder="X IPA 1" :error="form.errors.nama_kelas" />
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
