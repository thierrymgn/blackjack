import type { LayoutServerLoad } from './$types';

export const load: LayoutServerLoad = async ({ cookies }) => {
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