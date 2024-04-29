import type { PageServerLoad } from './$types';

export const load: PageServerLoad = async ({ parent }) => {
    const {token} = await parent();
    if(token) {
        return {
            token: token
        }
    }
};