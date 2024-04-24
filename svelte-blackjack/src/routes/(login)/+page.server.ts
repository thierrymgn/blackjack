import { redirect, type Actions } from "@sveltejs/kit";

export const actions = {
    default: async ({ request, cookies }) => {
        const data = await request.formData();
        const formJSON  = Object.fromEntries(data.entries());

        return await fetch('http://symfony-blackjack:8000/login_check', {
            method: 'POST',
            body: JSON.stringify(formJSON),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if(response.status !== 200) {
                throw("Invalid credentials. Please try again.");
            }
            return response.json();
        })
        .then(data => {
            const token: string = data.token;
            cookies.set('token', token, { path: '/', secure: true, httpOnly: true});
            redirect(302, '/play');
        })
        .catch(() => {
            return {response: null, error: true};
        })
        
    }
} satisfies Actions;