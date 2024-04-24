import type { Handle } from '@sveltejs/kit';
import { redirect } from '@sveltejs/kit';

export const handle = (async ({ event, resolve }) => {

    const token: string | undefined = event.cookies.get('token');
    
    if (token !== undefined) {
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