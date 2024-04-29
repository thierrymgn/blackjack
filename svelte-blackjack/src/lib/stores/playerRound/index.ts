import { writable } from 'svelte/store';
import type { Subscriber, Writable } from 'svelte/store';

class PlayerRoundStoreState {
    private store: Writable<object|null>;

    public constructor() {
        this.store = writable<object|null>(null);
    }

    subscribe(run: Subscriber<object|null>) {
		return this.store.subscribe(run);
	}


    public async submitWage(wage: number, id: string, token: string): Promise<string|null> {
        let response = await fetch('http://127.0.0.1:8888/player-round/'+id+'/wage', {
            method: 'PATCH',
            body: JSON.stringify({wager: wage}),
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => {
            if(response.status !== 200) {
                return null;
            }
            return response.json();
        })
        .then(data => {
            return data;
        })
        .catch(() => {
            return null;
        })

        console.log(response);
        return response;
    }

    public async hit(id: string, token: string): Promise<string|null> {
        let response = await fetch('http://127.0.0.1:8888/player-round/'+id+'/hit', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => {
            if(response.status !== 200) {
                return null;
            }
            return response.json();
        })
        .then(data => {
            return data;
        })
        .catch(() => {
            return null;
        })

        console.log(response);
        return response;
    }

    public async stand(id: string, token: string): Promise<string|null> {
        let response = await fetch('http://127.0.0.1:8888/player-round/'+id+'/stand', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => {
            if(response.status !== 200) {
                return null;
            }
            return response.json();
        })
        .then(data => {
            return data;
        })
        .catch(() => {
            return null;
        })

        console.log(response);
        return response;
    }
}

export const playerRoundStoreState = new PlayerRoundStoreState();