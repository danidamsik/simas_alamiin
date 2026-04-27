<script setup>
defineProps({
    columns: {
        type: Array,
        default: () => [],
    },
    rows: {
        type: Array,
        default: () => [],
    },
    emptyText: {
        type: String,
        default: 'Data belum tersedia',
    },
});
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            scope="col"
                            class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600"
                        >
                            {{ column.label }}
                        </th>
                        <th v-if="$slots.actions" scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr v-if="rows.length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-8 text-center text-sm text-gray-500">
                            {{ emptyText }}
                        </td>
                    </tr>
                    <tr v-for="row in rows" :key="row.id ?? JSON.stringify(row)" class="hover:bg-gray-50">
                        <td v-for="column in columns" :key="column.key" class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                {{ row[column.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <slot name="actions" :row="row" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
