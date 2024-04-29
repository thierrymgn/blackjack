import { writable } from 'svelte/store';
import type { Writable, Subscriber } from 'svelte/store';
import UserStore from './define';

class UserStoreState {
    private store: Writable<UserStore|null>;

    public constructor() {
        this.store = writable<UserStore|null>(null);
    }

    subscribe(run: Subscriber<UserStore|null>) {
		return this.store.subscribe(run);
	}

    public set(userStore: UserStore|null): void {
        this.store.set(userStore);
    }

    public async fetchUser(token: string): Promise<UserStore|null> {
        let user: UserStore|null = null;
        this.store.subscribe((value: UserStore|null) => user = value);
        if(token === null) {
            return null;
        }

        // if(user !== null) {
        //     return user;
        // }

        user = await fetch('http://symfony-blackjack:8000/user/profile', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => {
                if(response.status !== 200) {
                    throw("Invalid credentials. Please try again.");
                }
                return response.json();
            })
            .then(data => {
                return new UserStore(data.username, data.email, data.wallet);
            })
            .catch(() => {
                return null;
            });

        this.set(user);
        return user;
    }
}

export const userStoreState = new UserStoreState();