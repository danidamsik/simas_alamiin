<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
    },
    size: {
        type: String,
        default: 'md',
    },
    type: {
        type: String,
        default: 'button',
    },
});

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-lg border font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
    {
        sm: 'h-9 px-3 text-xs',
        md: 'h-10 px-4 text-sm',
        lg: 'h-11 px-5 text-sm',
    }[props.size],
    {
        primary: 'border-primary-600 bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-600',
        secondary: 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-primary-600',
        danger: 'border-red-600 bg-red-600 text-white hover:bg-red-700 focus:ring-red-600',
        ghost: 'border-transparent bg-transparent text-gray-700 shadow-none hover:bg-gray-100 focus:ring-primary-600',
    }[props.variant],
]);
</script>

<template>
    <button :type="type" :class="classes" v-bind="$attrs">
        <slot name="icon" />
        <slot />
    </button>
</template>
