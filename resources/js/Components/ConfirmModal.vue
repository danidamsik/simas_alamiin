<script setup>
import Modal from '@/Components/Modal.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Konfirmasi',
    },
    message: {
        type: String,
        default: 'Lanjutkan aksi ini?',
    },
    confirmText: {
        type: String,
        default: 'Ya, lanjutkan',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :show="show" max-width="md" @close="$emit('close')">
        <div class="p-6">
            <h2 class="font-heading text-lg font-semibold text-gray-900">{{ title }}</h2>
            <p class="mt-2 text-sm leading-6 text-gray-600">{{ message }}</p>
            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <AppButton variant="secondary" @click="$emit('close')">Batal</AppButton>
                <AppButton variant="danger" :disabled="processing" @click="$emit('confirm')">{{ confirmText }}</AppButton>
            </div>
        </div>
    </Modal>
</template>
