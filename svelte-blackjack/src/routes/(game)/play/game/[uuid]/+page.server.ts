import { gameStoreState, securityStoreState } from '$lib/stores';
import { redirect } from '@sveltejs/kit';
import type { PageServerLoad } from './$types';

export const load: PageServerLoad = async ({ params, cookies }) => {
    const token = cookies.get('token');
    if(token === undefined) {
        securityStoreState.logout();
        redirect(302, '/');
    }

    const response = await gameStoreState.getGame(token, params.uuid);

    if(response === null) {
        securityStoreState.logout();
        redirect(302, '/');
    }
    
    return { response: response, token: token };
};