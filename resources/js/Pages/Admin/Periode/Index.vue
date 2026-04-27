<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    periodes: Object,
});

const showModal = ref(false);
const editing = ref(null);

const semesterOptions = [
    { value: 'ganjil', label: 'Ganjil' },
    { value: 'genap', label: 'Genap' },
];

const form = useForm({
    tahun_ajaran: '',
    semester: 'ganjil',
    is_active: false,
});

const title = computed(() => editing.value ? 'Edit Periode' : 'Tambah Periode');

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.semester = 'ganjil';
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (periode) => {
    editing.value = periode;
    form.tahun_ajaran = periode.tahun_ajaran;
    form.semester = periode.semester;
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

const setActive = (periode) => router.patch(route('periode.set-active', periode.id), {}, { preserveScroll: true });
const destroy = (periode) => {
    if (confirm(`Hapus periode ${periode.tahun_ajaran} ${periode.semester}?`)) {
        router.delete(route('periode.destroy', periode.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Periode" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Periode</h1></template>

        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">Kelola tahun ajaran dan semester aktif.</p>
                <AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton>
            </div>

            <DataTable
                :columns="[
                    { key: 'tahun_ajaran', label: 'Tahun Ajaran' },
                    { key: 'semester', label: 'Semester' },
                    { key: 'is_active', label: 'Status' },
                ]"
                :rows="periodes.data"
            >
                <template #cell-semester="{ value }">{{ value === 'ganjil' ? 'Ganjil' : 'Genap' }}</template>
                <template #cell-is_active="{ value }">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="value ? 'bg-primary-50 text-primary-700' : 'bg-gray-100 text-gray-600'">
                        {{ value ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </template>
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button v-if="!row.is_active" class="rounded-lg p-2 text-primary-700 hover:bg-primary-50" title="Set aktif" @click="setActive(row)">
                            <Check class="h-4 w-4" />
                        </button>
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)">
                            <Pencil class="h-4 w-4" />
                        </button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus" @click="destroy(row)">
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
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
                    Jadikan periode aktif
                </label>
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
