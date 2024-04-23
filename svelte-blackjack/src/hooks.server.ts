import type { Handle } from '@sveltejs/kit';
import { redirect } from '@sveltejs/kit';
import { SecurityStoreState } from '$lib/stores';
import type SecurityStore from '$lib/stores/security/define';


export const handle = (async ({ event, resolve }) => {

    const token: string | undefined = event.cookies.get('token');

    let securityStoreState: SecurityStore = new SecurityStoreState().getValues();

    if(token === undefined) {
        securityStoreState.clearToken()
    }

    if (token !== undefined && securityStoreState.getToken() !== token) {
        securityStoreState.setToken(token);
    }

    if (securityStoreState.getToken() !== null) {
        if (event.url.pathname.startsWith('/play')) {
            return await resolve(event);
        }

        return redirect(302, '/play');
    }

    if (event.url.pathname.startsWith('/play')) {
        return redirect(302, '/');
    }

    return await resolve(event); 
}) satisfies Handle;