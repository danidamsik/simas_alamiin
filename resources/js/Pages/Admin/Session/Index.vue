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

defineProps({
    sessions: Object,
});

const showModal = ref(false);
const editing = ref(null);
const form = useForm({
    nama_sesi: '',
    jam_mulai: '',
    jam_selesai: '',
});

const title = computed(() => editing.value ? 'Edit Session' : 'Tambah Session');

const toTime = (value) => (value ?? '').slice(0, 5);

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (session) => {
    editing.value = session;
    form.nama_sesi = session.nama_sesi;
    form.jam_mulai = toTime(session.jam_mulai);
    form.jam_selesai = toTime(session.jam_selesai);
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => showModal.value = false };
    editing.value ? form.put(route('sessions.update', editing.value.id), options) : form.post(route('sessions.store'), options);
};

const destroy = (session) => {
    if (confirm(`Hapus ${session.nama_sesi}?`)) {
        router.delete(route('sessions.destroy', session.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Session" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Session</h1></template>

        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">Atur jam pelajaran dan cegah waktu yang tumpang tindih.</p>
                <AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton>
            </div>

            <DataTable
                :columns="[
                    { key: 'nama_sesi', label: 'Nama Sesi' },
                    { key: 'jam_mulai', label: 'Jam Mulai' },
                    { key: 'jam_selesai', label: 'Jam Selesai' },
                ]"
                :rows="sessions.data"
            >
                <template #cell-jam_mulai="{ value }">{{ toTime(value) }}</template>
                <template #cell-jam_selesai="{ value }">{{ toTime(value) }}</template>
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)"><Pencil class="h-4 w-4" /></button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus" @click="destroy(row)"><Trash2 class="h-4 w-4" /></button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="sessions.links" />
        </div>

        <Modal :show="showModal" max-width="md" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <AppInput v-model="form.nama_sesi" label="Nama Sesi" placeholder="Jam 1" :error="form.errors.nama_sesi" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <AppInput v-model="form.jam_mulai" type="time" label="Jam Mulai" :error="form.errors.jam_mulai" />
                    <AppInput v-model="form.jam_selesai" type="time" label="Jam Selesai" :error="form.errors.jam_selesai" />
                </div>
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
