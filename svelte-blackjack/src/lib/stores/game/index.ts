import { writable } from 'svelte/store';
import type { Subscriber, Writable } from 'svelte/store';

class GameStoreState {
    private store;

    public constructor() {
        this.store = writable(null);
    }

    public subscribe(run: Subscriber<any>) {
		return this.store.subscribe(run);
	}
    
    public set(gameStore: any|null): void {
        this.store.set(gameStore);
    }

    public async createNewGame(token: string|undefined): Promise<any> {
        let game = null;
        this.store.subscribe((value: any|null) => game = value);

        game = await fetch('http://127.0.0.1:8888/game', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => {

                if(response.status !== 201) {
                    throw("Invalid credentials. Please try again.");
                }
                return response.json();
            })
            .then(data => {
                return data
            })
            .catch(() => {
                return null;
            });

        this.set(game);

        return game; 
    }


    
}

export const gameStoreState = new GameStoreState();