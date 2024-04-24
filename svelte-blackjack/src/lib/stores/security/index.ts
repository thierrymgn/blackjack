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
}

export const securityStoreState = new SecurityStoreState();