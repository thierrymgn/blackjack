import { gameStoreState, securityStoreState } from '$lib/stores';
import { redirect } from '@sveltejs/kit';
import type { PageServerLoad } from './$types';

export const load: PageServerLoad = async ({ params, cookies }) => {
    const token = cookies.get('token');
    if(token === undefined) {
        securityStoreState.logout();
        redirect(302, '/');
    }


    const response = await gameStoreState.fetchListOfGames(token);
    
    return { response };
};