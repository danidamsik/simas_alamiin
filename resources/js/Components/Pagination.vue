<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

defineProps({
    links: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-1">
            <Link
                v-for="(link, index) in links"
                :key="`${link.label}-${index}`"
                :href="link.url || '#'"
                preserve-scroll
                class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm font-medium"
                :class="[
                    link.active
                        ? 'border-primary-600 bg-primary-600 text-white'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                    !link.url ? 'pointer-events-none opacity-50' : '',
                ]"
            >
                <ChevronLeft v-if="link.label.includes('Previous')" class="h-4 w-4" />
                <ChevronRight v-else-if="link.label.includes('Next')" class="h-4 w-4" />
                <span v-else v-html="link.label" />
            </Link>
        </div>
    </nav>
</template>
