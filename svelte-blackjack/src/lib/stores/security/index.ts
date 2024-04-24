import { writable } from 'svelte/store';
import type { Subscriber, Writable } from 'svelte/store';
import SecurityStore from './define';

class SecurityStoreState {
    private store: Writable<SecurityStore>;

    public constructor() {
        this.store = writable<SecurityStore>(new SecurityStore());
    }

    subscribe(run: Subscriber<SecurityStore>) {
		return this.store.subscribe(run);
	}

    public logout(): void {
        this.store.update((store) => {
            if(store === null) {
                throw("Security store is null.");
            }
            store.clearToken()
            return store;
        });
    }

    public async login(formJSON: object): Promise<string|null> {
        return await fetch('http://symfony-blackjack:8000/login_check', {
            method: 'POST',
            body: JSON.stringify(formJSON),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if(response.status !== 200) {
                return null;
            }
            return response.json();
        })
        .then(data => {
            return data.token;
        })
        .catch(() => {
            return null;
        })
    }
}

export const securityStoreState = new SecurityStoreState();