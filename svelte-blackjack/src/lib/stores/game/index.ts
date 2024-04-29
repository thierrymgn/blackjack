import { writable } from 'svelte/store';
import type { Subscriber, Writable } from 'svelte/store';

class GameStoreState {
    private store: Writable<object>;

    public constructor() {
        this.store = writable<object>({
            list: [],
            current: null,
            lastUpdate: null
        });
    }

    public subscribe(run: Subscriber<any>) {
		return this.store.subscribe(run);
	}
    
    public setCurrent(currentGame: any|null): void {
        this.store.update((value: any|null) => {
            value.current = currentGame;
            return value;
        });
    }

    public setListOfGames(listOfGames: any|null): void {
        this.store.update((value: any|null) => {
            value.list = listOfGames;
            return value;
        });
        this.setLastUpdate();
    }

    public addGameToList(game: any): void {
        this.store.update((value: any|null) => {
            value.list = [...value.list, game];
            return value;
        });
        this.setLastUpdate();
    }

    private setLastUpdate(): void {
        this.store.update((value: any|null) => {
            value.lastUpdate = new Date();
            return value;
        });
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

        this.setCurrent(game);
        this.addGameToList(game);

        return game; 
    }

    public async fetchListOfGames(token: string|undefined): Promise<any> {
        let games = null;
        let lastUpdate = null;
        this.store.subscribe((value: any|null) => {
            games = value.list;
            lastUpdate = value.lastUpdate;
        });

        // if(games !== null && lastUpdate !== null && (new Date().getTime() - lastUpdate.getTime()) < 3600000) {
        //     return games;
        // }

        games = await fetch('http://symfony-blackjack:8000/user/profile', {
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
                return data.games;
            })
            .catch(() => {
                return null;
            });

        this.setListOfGames(games);

        return games; 
    }

    public async getGame(token:string, id:string): Promise<any> {
        let game = null;
        this.store.subscribe((value: any|null) => {
            game = value.current;
        });

        game = await fetch('http://symfony-blackjack:8000/game/'+id, {
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
                return data;
            })
            .catch(() => {
                return null;
            });

        this.setCurrent(game);

        return game;
    }

    public async newRound(id:string, token:string): Promise<any> {
        let game = null;
        this.store.subscribe((value: any|null) => {
            game = value.current;
        });

        game = await fetch('http://symfony-blackjack:8000/game/'+id+'/newround', {
                method: 'PATCH',
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
                return data;
            })
            .catch(() => {
                return null;
            });

        this.setCurrent(game);

        return game;
    }
    
}

export const gameStoreState = new GameStoreState();