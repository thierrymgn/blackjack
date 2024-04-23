import { redirect } from '@sveltejs/kit';
import { SecurityStoreState } from '$lib/stores/index.js';

export async function load({cookies}){
    const securityStoreState = new SecurityStoreState();
    securityStoreState.logout();
    cookies.delete('token', { path: '/' });

    redirect(302, '/');
}