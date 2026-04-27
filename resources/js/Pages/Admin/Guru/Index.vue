<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, UserX } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    gurus: Object,
    userOptions: Array,
    filters: Object,
});

const showModal = ref(false);
const editing = ref(null);
const filter = ref({ search: props.filters.search ?? '', status: props.filters.status ?? 'active' });
const statusOptions = [{ value: 'active', label: 'Aktif' }, { value: 'all', label: 'Semua' }];
const userSelectOptions = computed(() => {
    const options = props.userOptions.map((user) => ({ value: user.id, label: `${user.name} (${user.email})` }));
    if (editing.value?.user) {
        options.unshift({ value: editing.value.user.id, label: `${editing.value.user.name} (${editing.value.user.email})` });
    }
    return options;
});

const form = useForm({ user_id: '', nama: '', nip: '', is_active: true });
const title = computed(() => editing.value ? 'Edit Guru' : 'Tambah Guru');

let filterTimer;
watch(filter, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => router.get(route('guru.index'), filter.value, { preserveState: true, preserveScroll: true, replace: true }), 300);
}, { deep: true });

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.is_active = true;
    form.clearErrors();
    showModal.value = true;
};
const openEdit = (guru) => {
    editing.value = guru;
    form.user_id = guru.user_id;
    form.nama = guru.nama;
    form.nip = guru.nip ?? '';
    form.is_active = guru.is_active;
    form.clearErrors();
    showModal.value = true;
};
const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => showModal.value = false };
    editing.value ? form.put(route('guru.update', editing.value.id), options) : form.post(route('guru.store'), options);
};
const deactivate = (guru) => {
    if (confirm(`Nonaktifkan guru ${guru.nama}?`)) {
        router.delete(route('guru.destroy', guru.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Guru" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Guru</h1></template>
        <div class="space-y-4">
            <div class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_160px_auto]">
                <AppInput v-model="filter.search" label="Pencarian" placeholder="Cari nama atau NIP" />
                <AppSelect v-model="filter.status" label="Status" :options="statusOptions" />
                <div class="flex items-end justify-end"><AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton></div>
            </div>
            <DataTable
                :columns="[
                    { key: 'nama', label: 'Nama' },
                    { key: 'nip', label: 'NIP' },
                    { key: 'user', label: 'User' },
                    { key: 'is_active', label: 'Status' },
                ]"
                :rows="gurus.data"
            >
                <template #cell-user="{ row }">{{ row.user?.email }}</template>
                <template #cell-is_active="{ value }">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="value ? 'bg-primary-50 text-primary-700' : 'bg-gray-100 text-gray-600'">{{ value ? 'Aktif' : 'Nonaktif' }}</span>
                </template>
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)"><Pencil class="h-4 w-4" /></button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Nonaktifkan" @click="deactivate(row)"><UserX class="h-4 w-4" /></button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="gurus.links" />
        </div>
        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <AppInput v-model="form.nama" label="Nama" :error="form.errors.nama" />
                    <AppInput v-model="form.nip" label="NIP" :error="form.errors.nip" />
                    <AppSelect v-model="form.user_id" class="sm:col-span-2" label="User Login" :options="userSelectOptions" :error="form.errors.user_id" />
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
                        Guru aktif
                    </label>
                </div>
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
