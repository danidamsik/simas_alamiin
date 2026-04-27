<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { KeyRound, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const showModal = ref(false);
const showPasswordModal = ref(false);
const editing = ref(null);
const passwordTarget = ref(null);
const filter = ref({ search: props.filters.search ?? '', role: props.filters.role ?? '' });
const roleOptions = computed(() => props.roles.map((role) => ({ value: role, label: roleLabel(role) })));

const form = useForm({ name: '', email: '', role: 'guru', password: '', password_confirmation: '' });
const passwordForm = useForm({ password: '', password_confirmation: '' });
const title = computed(() => editing.value ? 'Edit User' : 'Tambah User');

let filterTimer;
watch(filter, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => router.get(route('users.index'), filter.value, { preserveState: true, preserveScroll: true, replace: true }), 300);
}, { deep: true });

function roleLabel(role) {
    return {
        admin: 'Admin',
        guru: 'Guru',
        kepala_sekolah: 'Kepala Sekolah',
    }[role] ?? role;
}

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.role = 'guru';
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (user) => {
    editing.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => showModal.value = false };
    editing.value ? form.put(route('users.update', editing.value.id), options) : form.post(route('users.store'), options);
};

const openPasswordReset = (user) => {
    passwordTarget.value = user;
    passwordForm.reset();
    passwordForm.clearErrors();
    showPasswordModal.value = true;
};

const resetPassword = () => {
    passwordForm.put(route('admin.users.password.update', passwordTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => showPasswordModal.value = false,
    });
};

const destroy = (user) => {
    if (confirm(`Hapus user ${user.name}?`)) {
        router.delete(route('users.destroy', user.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="User" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">User</h1></template>
        <div class="space-y-4">
            <div class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_190px_auto]">
                <AppInput v-model="filter.search" label="Pencarian" placeholder="Cari nama atau email" />
                <AppSelect v-model="filter.role" label="Role" placeholder="Semua role" :options="roleOptions" />
                <div class="flex items-end justify-end"><AppButton @click="openCreate"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton></div>
            </div>
            <DataTable
                :columns="[
                    { key: 'name', label: 'Nama' },
                    { key: 'email', label: 'Email' },
                    { key: 'role', label: 'Role' },
                    { key: 'guru', label: 'Guru' },
                ]"
                :rows="users.data"
            >
                <template #cell-role="{ value }">{{ roleLabel(value) }}</template>
                <template #cell-guru="{ row }">{{ row.guru?.nama ?? '-' }}</template>
                <template #actions="{ row }">
                    <div class="flex justify-end gap-2">
                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(row)"><Pencil class="h-4 w-4" /></button>
                        <button class="rounded-lg p-2 text-primary-700 hover:bg-primary-50" title="Reset password" @click="openPasswordReset(row)"><KeyRound class="h-4 w-4" /></button>
                        <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus" @click="destroy(row)"><Trash2 class="h-4 w-4" /></button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="users.links" />
        </div>

        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <AppInput v-model="form.name" label="Nama" :error="form.errors.name" />
                    <AppInput v-model="form.email" type="email" label="Email" :error="form.errors.email" />
                    <AppSelect v-model="form.role" label="Role" :options="roleOptions" :error="form.errors.role" />
                    <template v-if="!editing">
                        <AppInput v-model="form.password" type="password" label="Password" :error="form.errors.password" />
                        <AppInput v-model="form.password_confirmation" type="password" label="Konfirmasi Password" :error="form.errors.password_confirmation" />
                    </template>
                </div>
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>

        <Modal :show="showPasswordModal" max-width="md" @close="showPasswordModal = false">
            <form class="space-y-4 p-6" @submit.prevent="resetPassword">
                <h2 class="font-heading text-lg font-semibold text-gray-950">Reset Password</h2>
                <p class="text-sm text-gray-600">User: {{ passwordTarget?.name }}</p>
                <AppInput v-model="passwordForm.password" type="password" label="Password Baru" :error="passwordForm.errors.password" />
                <AppInput v-model="passwordForm.password_confirmation" type="password" label="Konfirmasi Password" :error="passwordForm.errors.password_confirmation" />
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showPasswordModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="passwordForm.processing">Reset</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
