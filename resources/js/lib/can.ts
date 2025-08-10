import { usePage } from '@inertiajs/vue3';
export function can(permission: string): boolean {
    const page = usePage();
    const roles: string[] = page.props.auth.roles ?? [];
    const permissions: string[] = page.props.auth.permissions ?? [];
    if (roles.includes('Super Admin')) {
        return true;
    }
    return permissions.includes(permission);


}