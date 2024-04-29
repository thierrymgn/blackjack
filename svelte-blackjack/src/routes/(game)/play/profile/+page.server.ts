import { securityStoreState, userStoreState } from '$lib/stores';
import { redirect, type Actions } from '@sveltejs/kit';
import type { PageServerLoad } from './$types';
import UserStore from '$lib/stores/user/define';

export const load: PageServerLoad = async ({ params, cookies }) => {
    const token = cookies.get('token');
    if(token === undefined) {
        securityStoreState.logout();
        redirect(302, '/');
    }

    const response = await userStoreState.fetchUser(token);

    if(response === null) {
        securityStoreState.logout();
        redirect(302, '/');
    }

    return { ...response }
};

export const actions = {
    default: async ({ request, cookies }) => {
        const data = await request.formData();
        const formJSON  = Object.fromEntries(data.entries());

        const payload = {
            username: formJSON.username,
            email: formJSON.email,
            password: formJSON.password,
        };

        await fetch('http://symfony-blackjack:8000/user/profile', {
            method: 'PATCH',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + cookies.get('token')
            }
        })
        .then(response => {
            if(response.status !== 200) {
                throw("Invalid credentials. Please try again.");
            }
            return response.json();
        })
        .then(data => {
            const userStore: UserStore = new UserStore(data.username, data.email, data.wallet);
            userStoreState.set(data);
        })
        .catch(() => {
            return {response: null, error: true};
        })
        
    }
} satisfies Actions;