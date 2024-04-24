import { securityStoreState } from "$lib/stores";
import { redirect, type Actions } from "@sveltejs/kit";

export const actions = {
    default: async ({ request, cookies }) => {
        const data = await request.formData();
        const formJSON  = Object.fromEntries(data.entries());

        const token: string | null = await securityStoreState.login(formJSON);

        if(token === null) {
            return {error: true};
        }

        cookies.set('token', token, { path: '/', secure: true, httpOnly: true});
        redirect(302, '/play');
    }
} satisfies Actions;