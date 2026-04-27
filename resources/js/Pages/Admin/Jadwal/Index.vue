<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppSelect from '@/Components/AppSelect.vue';
import Modal from '@/Components/Modal.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    jadwalsByDay: Object,
    days: Array,
    kelasOptions: Array,
    guruOptions: Array,
    sessionOptions: Array,
    periodeOptions: Array,
    filters: Object,
});

const showModal = ref(false);
const editing = ref(null);
const filter = ref({
    periode_id: props.filters.periode_id ?? '',
    hari: props.filters.hari ?? '',
    kelas_id: props.filters.kelas_id ?? '',
    guru_id: props.filters.guru_id ?? '',
});

const form = useForm({
    kelas_id: '',
    guru_id: '',
    mata_pelajaran: '',
    hari: 'senin',
    session_id: '',
    periode_id: '',
});

const title = computed(() => editing.value ? 'Edit Jadwal' : 'Tambah Jadwal');
const dayOptions = computed(() => props.days.map((day) => ({ value: day, label: dayLabel(day) })));
const kelasSelectOptions = computed(() => props.kelasOptions.map((item) => ({ value: item.id, label: item.nama_kelas })));
const guruSelectOptions = computed(() => props.guruOptions.map((item) => ({ value: item.id, label: item.nama })));
const sessionSelectOptions = computed(() => props.sessionOptions.map((item) => ({
    value: item.id,
    label: `${item.nama_sesi} (${toTime(item.jam_mulai)}-${toTime(item.jam_selesai)})`,
})));
const periodeSelectOptions = computed(() => props.periodeOptions.map((item) => ({
    value: item.id,
    label: `${item.tahun_ajaran} ${capitalize(item.semester)}${item.is_active ? ' - Aktif' : ''}`,
})));

let filterTimer;
watch(filter, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => router.get(route('jadwal.index'), filter.value, { preserveState: true, preserveScroll: true, replace: true }), 300);
}, { deep: true });

function capitalize(value) {
    return value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
}

function dayLabel(day) {
    return capitalize(day);
}

function toTime(value) {
    return (value ?? '').slice(0, 5);
}

const activePeriodeId = computed(() => props.periodeOptions.find((periode) => periode.is_active)?.id ?? props.periodeOptions[0]?.id ?? '');

const openCreate = (day = 'senin') => {
    editing.value = null;
    form.reset();
    form.hari = day;
    form.periode_id = filter.value.periode_id || activePeriodeId.value;
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (jadwal) => {
    editing.value = jadwal;
    form.kelas_id = jadwal.kelas_id;
    form.guru_id = jadwal.guru_id;
    form.mata_pelajaran = jadwal.mata_pelajaran;
    form.hari = jadwal.hari;
    form.session_id = jadwal.session_id;
    form.periode_id = jadwal.periode_id;
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => showModal.value = false };
    editing.value ? form.put(route('jadwal.update', editing.value.id), options) : form.post(route('jadwal.store'), options);
};

const destroy = (jadwal) => {
    if (confirm(`Hapus jadwal ${jadwal.mata_pelajaran}?`)) {
        router.delete(route('jadwal.destroy', jadwal.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Jadwal" />
    <AuthenticatedLayout>
        <template #header><h1 class="font-heading text-lg font-semibold text-gray-950">Jadwal Pelajaran</h1></template>

        <div class="space-y-5">
            <div class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-5">
                <AppSelect v-model="filter.periode_id" label="Periode" placeholder="Semua periode" :options="periodeSelectOptions" />
                <AppSelect v-model="filter.hari" label="Hari" placeholder="Semua hari" :options="dayOptions" />
                <AppSelect v-model="filter.kelas_id" label="Kelas" placeholder="Semua kelas" :options="kelasSelectOptions" />
                <AppSelect v-model="filter.guru_id" label="Guru" placeholder="Semua guru" :options="guruSelectOptions" />
                <div class="flex items-end justify-end">
                    <AppButton @click="openCreate(filter.hari || 'senin')"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton>
                </div>
            </div>

            <section v-for="day in days" :key="day" class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                    <h2 class="font-heading text-base font-semibold text-gray-950">{{ dayLabel(day) }}</h2>
                    <AppButton size="sm" variant="secondary" @click="openCreate(day)"><template #icon><Plus class="h-4 w-4" /></template>Tambah</AppButton>
                </div>
                <div class="divide-y divide-gray-100">
                    <div v-if="(jadwalsByDay[day] ?? []).length === 0" class="px-4 py-6 text-sm text-gray-500">Tidak ada jadwal.</div>
                    <div
                        v-for="jadwal in jadwalsByDay[day] ?? []"
                        :key="jadwal.id"
                        class="grid gap-3 px-4 py-4 md:grid-cols-[180px_1fr_180px_160px_auto] md:items-center"
                    >
                        <div class="text-sm font-semibold text-gray-950">
                            {{ jadwal.session?.nama_sesi }}
                            <p class="text-xs font-medium text-gray-500">{{ toTime(jadwal.session?.jam_mulai) }}-{{ toTime(jadwal.session?.jam_selesai) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-950">{{ jadwal.mata_pelajaran }}</p>
                            <p class="text-sm text-gray-500">{{ jadwal.kelas?.nama_kelas }}</p>
                        </div>
                        <p class="text-sm text-gray-700">{{ jadwal.guru?.nama }}</p>
                        <p class="text-sm text-gray-500">{{ jadwal.periode?.tahun_ajaran }} {{ capitalize(jadwal.periode?.semester) }}</p>
                        <div class="flex justify-end gap-2">
                            <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" title="Edit" @click="openEdit(jadwal)"><Pencil class="h-4 w-4" /></button>
                            <button class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus" @click="destroy(jadwal)"><Trash2 class="h-4 w-4" /></button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <form class="space-y-4 p-6" @submit.prevent="submit">
                <h2 class="font-heading text-lg font-semibold text-gray-950">{{ title }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <AppSelect v-model="form.periode_id" label="Periode" :options="periodeSelectOptions" :error="form.errors.periode_id" />
                    <AppSelect v-model="form.hari" label="Hari" :options="dayOptions" :error="form.errors.hari" />
                    <AppSelect v-model="form.kelas_id" label="Kelas" :options="kelasSelectOptions" :error="form.errors.kelas_id" />
                    <AppSelect v-model="form.guru_id" label="Guru" :options="guruSelectOptions" :error="form.errors.guru_id" />
                    <AppSelect v-model="form.session_id" label="Session" :options="sessionSelectOptions" :error="form.errors.session_id" />
                    <AppInput v-model="form.mata_pelajaran" label="Mata Pelajaran" :error="form.errors.mata_pelajaran" />
                </div>
                <div class="flex justify-end gap-3">
                    <AppButton type="button" variant="secondary" @click="showModal = false">Batal</AppButton>
                    <AppButton type="submit" :disabled="form.processing">Simpan</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
