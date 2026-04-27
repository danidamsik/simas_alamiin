<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const styles = {
    hadir: 'bg-green-100 text-green-700 ring-green-600/20',
    izin: 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
    sakit: 'bg-blue-100 text-blue-700 ring-blue-600/20',
    alfa: 'bg-red-100 text-red-700 ring-red-600/20',
};

const labels = {
    hadir: 'Hadir',
    izin: 'Izin',
    sakit: 'Sakit',
    alfa: 'Alfa',
};

const badgeClass = computed(() => styles[props.status] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20');
const label = computed(() => labels[props.status] ?? props.status);
</script>

<template>
    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset" :class="badgeClass">
        {{ label }}
    </span>
</template>
