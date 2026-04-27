<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    BookOpen,
    CalendarDays,
    ChevronDown,
    ChevronRight,
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
const openGroups = ref(['master-data', 'administrasi', 'absensi', 'laporan']);

const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role);

const roleLabel = computed(() => ({
    admin: 'Admin',
    guru: 'Guru',
    kepala_sekolah: 'Kepala Sekolah',
}[role.value] ?? 'Pengguna'));

const allSections = [
    { type: 'item', label: 'Dashboard', href: '/dashboard', icon: LayoutDashboard, roles: ['admin', 'guru', 'kepala_sekolah'] },
    {
        type: 'group',
        id: 'master-data',
        label: 'Data Master',
        icon: School,
        children: [
            { label: 'Periode', href: '/periode', icon: CalendarDays, roles: ['admin'] },
            { label: 'Kelas', href: '/kelas', icon: School, roles: ['admin'] },
            { label: 'Siswa', href: '/siswa', icon: GraduationCap, roles: ['admin'] },
            { label: 'Guru', href: '/guru', icon: Users, roles: ['admin'] },
        ],
    },
    {
        type: 'group',
        id: 'administrasi',
        label: 'Administrasi',
        icon: UserRoundCog,
        children: [
            { label: 'User', href: '/users', icon: UserRoundCog, roles: ['admin'] },
            { label: 'Session', href: '/sessions', icon: Clock3, roles: ['admin'] },
            { label: 'Jadwal', href: '/jadwal', icon: BookOpen, roles: ['admin'] },
        ],
    },
    {
        type: 'group',
        id: 'absensi',
        label: 'Absensi',
        icon: ClipboardCheck,
        children: [
            { label: 'Input Absensi', href: '/absensi/input', icon: ClipboardCheck, roles: ['admin', 'guru'] },
            { label: 'Data Absensi', href: '/absensi', icon: BarChart3, roles: ['admin', 'kepala_sekolah'] },
            { label: 'Riwayat Absensi', href: '/absensi/riwayat', icon: ClipboardCheck, roles: ['guru'] },
        ],
    },
    {
        type: 'group',
        id: 'laporan',
        label: 'Laporan',
        icon: FileSpreadsheet,
        children: [
            { label: 'Rekap Absensi', href: '/laporan', icon: FileSpreadsheet, roles: ['admin', 'kepala_sekolah'] },
        ],
    },
];

const navigation = computed(() => allSections
    .map((section) => {
        if (section.type === 'item') {
            return section.roles.includes(role.value) ? section : null;
        }

        const children = section.children.filter((item) => item.roles.includes(role.value));

        return children.length ? { ...section, children } : null;
    })
    .filter(Boolean));

const isActive = (href) => {
    const currentPath = page.url.split('?')[0];

    if (href === '/dashboard') {
        return currentPath === '/dashboard';
    }

    if (href === '/absensi') {
        return currentPath === '/absensi';
    }

    return currentPath === href || currentPath.startsWith(`${href}/`);
};

const isGroupActive = (section) => section.children?.some((item) => isActive(item.href)) ?? false;
const isGroupOpen = (section) => openGroups.value.includes(section.id) || isGroupActive(section);
const toggleGroup = (section) => {
    if (isGroupOpen(section) && !isGroupActive(section)) {
        openGroups.value = openGroups.value.filter((id) => id !== section.id);

        return;
    }

    if (!openGroups.value.includes(section.id)) {
        openGroups.value = [...openGroups.value, section.id];
    }
};

const currentPath = computed(() => page.url.split('?')[0]);
const breadcrumbs = computed(() => {
    const path = currentPath.value;
    const crumbs = [{ label: 'Dashboard', href: '/dashboard' }];

    const rules = [
        { match: (path) => path === '/profile', crumbs: [{ label: 'Profil' }] },
        { match: (path) => path.startsWith('/periode'), crumbs: [{ label: 'Data Master' }, { label: 'Periode', href: '/periode' }] },
        { match: (path) => path.startsWith('/kelas'), crumbs: [{ label: 'Data Master' }, { label: 'Kelas', href: '/kelas' }] },
        { match: (path) => path.startsWith('/siswa'), crumbs: [{ label: 'Data Master' }, { label: 'Siswa', href: '/siswa' }] },
        { match: (path) => path.startsWith('/guru'), crumbs: [{ label: 'Data Master' }, { label: 'Guru', href: '/guru' }] },
        { match: (path) => path.startsWith('/users'), crumbs: [{ label: 'Administrasi' }, { label: 'User', href: '/users' }] },
        { match: (path) => path.startsWith('/sessions'), crumbs: [{ label: 'Administrasi' }, { label: 'Session', href: '/sessions' }] },
        { match: (path) => path.startsWith('/jadwal'), crumbs: [{ label: 'Administrasi' }, { label: 'Jadwal', href: '/jadwal' }] },
        { match: (path) => path.startsWith('/absensi/input/') && path !== '/absensi/input', crumbs: [{ label: 'Absensi' }, { label: 'Input Absensi', href: '/absensi/input' }, { label: 'Form Input' }] },
        { match: (path) => path === '/absensi/input', crumbs: [{ label: 'Absensi' }, { label: 'Input Absensi', href: '/absensi/input' }] },
        { match: (path) => path.startsWith('/absensi/riwayat'), crumbs: [{ label: 'Absensi' }, { label: 'Riwayat Absensi', href: '/absensi/riwayat' }] },
        { match: (path) => path.startsWith('/absensi') && path.endsWith('/edit'), crumbs: [{ label: 'Absensi' }, { label: 'Edit Absensi' }] },
        { match: (path) => path === '/absensi', crumbs: [{ label: 'Absensi' }, { label: 'Data Absensi', href: '/absensi' }] },
        { match: (path) => path.startsWith('/laporan'), crumbs: [{ label: 'Laporan' }, { label: 'Rekap Absensi', href: '/laporan' }] },
    ];

    const match = rules.find((rule) => rule.match(path));
    if (path === '/dashboard' || !match) {
        return crumbs;
    }

    return [...crumbs, ...match.crumbs].map((crumb, index, items) => ({
        ...crumb,
        current: index === items.length - 1,
    }));
});
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
                <Link href="/dashboard" class="flex min-w-0 items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-600 text-white shadow-sm ring-1 ring-primary-700/10">
                        <School class="h-5 w-5" />
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate font-heading text-lg font-semibold leading-5 text-gray-950">SIMAS Al-Amiin</span>
                        <span class="block truncate text-xs font-medium text-primary-600">Presensi Digital</span>
                    </span>
                </Link>
                <button type="button" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto px-3 py-4">
                <template v-for="section in navigation" :key="section.id ?? section.href">
                    <Link
                        v-if="section.type === 'item'"
                        :href="section.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                        :class="isActive(section.href)
                            ? 'bg-primary-50 text-primary-700'
                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-950'"
                        @click="sidebarOpen = false"
                    >
                        <component :is="section.icon" class="h-5 w-5 shrink-0" />
                        <span class="truncate">{{ section.label }}</span>
                    </Link>

                    <div v-else class="space-y-1">
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
                            :class="isGroupActive(section)
                                ? 'bg-primary-50 text-primary-700'
                                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-950'"
                            :aria-expanded="isGroupOpen(section)"
                            @click="toggleGroup(section)"
                        >
                            <component :is="section.icon" class="h-5 w-5 shrink-0" />
                            <span class="min-w-0 flex-1 truncate text-left">{{ section.label }}</span>
                            <ChevronDown
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="isGroupOpen(section) ? 'rotate-180' : ''"
                            />
                        </button>

                        <div v-show="isGroupOpen(section)" class="space-y-1 pl-4">
                            <Link
                                v-for="item in section.children"
                                :key="item.href"
                                :href="item.href"
                                class="flex items-center gap-3 rounded-lg py-2 pl-4 pr-3 text-sm font-medium transition"
                                :class="isActive(item.href)
                                    ? 'bg-primary-50 text-primary-700'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-950'"
                                @click="sidebarOpen = false"
                            >
                                <component :is="item.icon" class="h-4 w-4 shrink-0" />
                                <span class="truncate">{{ item.label }}</span>
                            </Link>
                        </div>
                    </div>
                </template>
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
                <nav v-if="breadcrumbs.length > 1" class="mb-4 flex min-w-0 items-center gap-1 text-sm" aria-label="Breadcrumb">
                    <template v-for="(crumb, index) in breadcrumbs" :key="`${crumb.label}-${index}`">
                        <ChevronRight v-if="index > 0" class="h-4 w-4 shrink-0 text-gray-400" />
                        <Link
                            v-if="crumb.href && !crumb.current"
                            :href="crumb.href"
                            class="truncate font-medium text-gray-500 hover:text-primary-700"
                        >
                            {{ crumb.label }}
                        </Link>
                        <span v-else class="truncate font-semibold text-gray-900" aria-current="page">{{ crumb.label }}</span>
                    </template>
                </nav>

                <slot />
            </main>
        </div>
    </div>
</template>
