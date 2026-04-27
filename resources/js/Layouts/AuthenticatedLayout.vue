<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    BookOpen,
    CalendarDays,
    ClipboardCheck,
    Clock3,
    FileSpreadsheet,
    GraduationCap,
    LayoutDashboard,
    LogOut,
    Menu,
    School,
    Users,
    UserRoundCog,
    X,
} from 'lucide-vue-next';
import ToastContainer from '@/Components/ToastContainer.vue';

const page = usePage();
const sidebarOpen = ref(false);

const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role);

const roleLabel = computed(() => ({
    admin: 'Admin',
    guru: 'Guru',
    kepala_sekolah: 'Kepala Sekolah',
}[role.value] ?? 'Pengguna'));

const allItems = [
    { label: 'Dashboard', href: '/dashboard', icon: LayoutDashboard, roles: ['admin', 'guru', 'kepala_sekolah'] },
    { label: 'Periode', href: '/periode', icon: CalendarDays, roles: ['admin'] },
    { label: 'Kelas', href: '/kelas', icon: School, roles: ['admin'] },
    { label: 'Siswa', href: '/siswa', icon: GraduationCap, roles: ['admin'] },
    { label: 'Guru', href: '/guru', icon: Users, roles: ['admin'] },
    { label: 'User', href: '/users', icon: UserRoundCog, roles: ['admin'] },
    { label: 'Session', href: '/sessions', icon: Clock3, roles: ['admin'] },
    { label: 'Jadwal', href: '/jadwal', icon: BookOpen, roles: ['admin'] },
    { label: 'Jadwal Saya', href: '/jadwal-saya', icon: BookOpen, roles: ['guru'] },
    { label: 'Input Absensi', href: '/absensi/input', icon: ClipboardCheck, roles: ['admin', 'guru'] },
    { label: 'Data Absensi', href: '/absensi', icon: BarChart3, roles: ['admin', 'kepala_sekolah'] },
    { label: 'Riwayat Absensi', href: '/absensi/riwayat', icon: ClipboardCheck, roles: ['guru'] },
    { label: 'Laporan', href: '/laporan', icon: FileSpreadsheet, roles: ['admin', 'kepala_sekolah'] },
];

const navigation = computed(() => allItems.filter((item) => item.roles.includes(role.value)));

const isActive = (href) => {
    if (href === '/dashboard') {
        return page.url === '/dashboard';
    }

    return page.url === href || page.url.startsWith(`${href}/`);
};
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <ToastContainer />

        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden" @click="sidebarOpen = false" />

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between border-b border-gray-100 px-5">
                <Link href="/dashboard" class="min-w-0">
                    <p class="font-heading text-lg font-semibold text-gray-950">SIMAS Al-Amiin</p>
                    <p class="text-xs font-medium text-primary-600">Presensi Digital</p>
                </Link>
                <button type="button" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                    :class="isActive(item.href)
                        ? 'bg-primary-50 text-primary-700'
                        : 'text-gray-700 hover:bg-gray-100 hover:text-gray-950'"
                    @click="sidebarOpen = false"
                >
                    <component :is="item.icon" class="h-5 w-5 shrink-0" />
                    <span class="truncate">{{ item.label }}</span>
                </Link>
            </nav>

            <div class="border-t border-gray-100 p-4">
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="truncate text-sm font-semibold text-gray-900">{{ user?.name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ roleLabel }}</p>
                </div>
            </div>
        </aside>

        <div class="lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = true">
                            <Menu class="h-5 w-5" />
                        </button>
                        <div class="min-w-0">
                            <slot name="header">
                                <h1 class="truncate font-heading text-lg font-semibold text-gray-950">Dashboard</h1>
                            </slot>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="max-w-48 truncate text-sm font-semibold text-gray-900">{{ user?.name }}</p>
                            <p class="text-xs text-gray-500">{{ user?.email }}</p>
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-50"
                            title="Logout"
                        >
                            <LogOut class="h-5 w-5" />
                        </Link>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </div>
</template>
