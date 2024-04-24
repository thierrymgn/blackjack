import type { PageServerLoad } from './$types';

export const load: PageServerLoad = async ({ params, cookies }) => {
    const token = cookies.get('token');
    if(token === undefined) {
        return {
            status: 302,
            headers: {
                location: '/'
            }
        };
    }
    return { token: token }
};