<script setup>
import { Loader2 } from 'lucide-vue-next';

defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        class="inline-flex items-center gap-2 rounded-lg border border-transparent bg-primary-600 px-4 py-2 text-xs font-semibold uppercase text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 active:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-60"
        :disabled="disabled || loading"
    >
        <Loader2 v-if="loading" class="h-4 w-4 animate-spin" />
        <slot />
    </button>
</template>
