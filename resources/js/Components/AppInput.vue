<script setup>
defineProps({
    id: String,
    label: String,
    error: String,
    type: {
        type: String,
        default: 'text',
    },
});

const model = defineModel({
    type: [String, Number],
    default: '',
});
</script>

<template>
    <label class="block">
        <span v-if="label" class="mb-1.5 block text-sm font-medium text-gray-700">{{ label }}</span>
        <input
            :id="id"
            v-model="model"
            :type="type"
            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-600 focus:ring-primary-600"
            v-bind="$attrs"
        />
        <span v-if="error" class="mt-1 block text-sm text-red-600">{{ error }}</span>
    </label>
</template>
