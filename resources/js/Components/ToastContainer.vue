<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, CircleAlert, Info, X } from 'lucide-vue-next';

const page = usePage();
const toasts = ref([]);
let nextId = 1;

const icons = {
    success: CheckCircle2,
    error: CircleAlert,
    info: Info,
};

const addToast = ({ type = 'info', message, title }) => {
    if (!message) return;

    const id = nextId++;
    toasts.value.push({ id, type, message, title });

    window.setTimeout(() => removeToast(id), 4500);
};

const removeToast = (id) => {
    toasts.value = toasts.value.filter((toast) => toast.id !== id);
};

const handleToast = (event) => addToast(event.detail ?? {});

watch(
    () => page.props.flash,
    (flash) => {
        addToast({ type: 'success', message: flash?.success ?? flash?.status });
        addToast({ type: 'error', message: flash?.error });
    },
    { deep: true, immediate: true }
);

onMounted(() => window.addEventListener('toast', handleToast));
onUnmounted(() => window.removeEventListener('toast', handleToast));
</script>

<template>
    <div class="pointer-events-none fixed right-4 top-4 z-50 flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3">
        <TransitionGroup
            enter-active-class="transition duration-200"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex gap-3 rounded-lg border bg-white p-4 text-sm shadow-lg"
                :class="toast.type === 'error' ? 'border-red-200' : 'border-green-200'"
            >
                <component
                    :is="icons[toast.type] ?? icons.info"
                    class="mt-0.5 h-5 w-5 shrink-0"
                    :class="toast.type === 'error' ? 'text-red-600' : toast.type === 'success' ? 'text-primary-600' : 'text-blue-600'"
                />
                <div class="min-w-0 flex-1">
                    <p v-if="toast.title" class="font-semibold text-gray-900">{{ toast.title }}</p>
                    <p class="text-gray-700">{{ toast.message }}</p>
                </div>
                <button type="button" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" @click="removeToast(toast.id)">
                    <X class="h-4 w-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
