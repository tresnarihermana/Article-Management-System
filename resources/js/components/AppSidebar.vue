<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, UserRoundCog, ShieldCheck, NotebookPen, Newspaper } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { can } from '@/lib/can';
import { computed } from 'vue';
const unfilteredMainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,

    },
    {
        title: 'Users',
        href: '/manage/users',
        icon: Users,
        permission: 'users.view',

    },
    {
        title: 'Roles',
        href: '/manage/roles',
        icon: UserRoundCog,
    },
    {
        title: 'Permissions',
        href: '/manage/permissions',
        icon: ShieldCheck,
        permission: 'permissions.view',
    },
    {
        title: 'Articles',
        href: '/manage/articles',
        icon: NotebookPen,
        // permission : 'article.view',
    },
    {
        title: 'Approve Article',
        href: '/manage/approve-artile',
        icon: Newspaper,
        // permission : 'approve-article.view',
    },
];

const mainNavItems = computed(() =>
    unfilteredMainNavItems.filter(item => !item.permission || can(item.permission))
);

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
