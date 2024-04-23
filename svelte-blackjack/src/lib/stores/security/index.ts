import { writable } from 'svelte/store';
import { get, type Writable } from 'svelte/store';
import SecurityStore from './define';

export default class SecurityStoreState {
    private store: Writable<SecurityStore>;

    public constructor() {
        this.store = writable<SecurityStore>(new SecurityStore());
    }

    public getStore(): Writable<SecurityStore> {
        return this.store;
    }

    public getValues(): SecurityStore {
        return get(this.store);
    }

    public logout(): void {
        this.store.update((store) => {
            store.clearToken()
            return store;
        });
    }
}