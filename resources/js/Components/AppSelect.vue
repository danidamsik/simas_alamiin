<script setup>
defineProps({
    id: String,
    label: String,
    error: String,
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Pilih data',
    },
});

const model = defineModel({
    type: [String, Number, Boolean],
    default: '',
});
</script>

<template>
    <label class="block">
        <span v-if="label" class="mb-1.5 block text-sm font-medium text-gray-700">{{ label }}</span>
        <select
            :id="id"
            v-model="model"
            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-600 focus:ring-primary-600"
            v-bind="$attrs"
        >
            <option value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option.value ?? option.id ?? option"
                :value="option.value ?? option.id ?? option"
            >
                {{ option.label ?? option.name ?? option.nama ?? option }}
            </option>
        </select>
        <span v-if="error" class="mt-1 block text-sm text-red-600">{{ error }}</span>
    </label>
</template>
