

<script lang="ts">
    import { Input } from "$lib/components/ui/input";
    import type { PageData } from './$types';
    import { page } from "$app/stores";
    import {gameStoreState, playerRoundStoreState} from "$lib/stores";
    import PlayingCard from "$lib/components/ui/playing-card/PlayingCard.svelte";

    export let data: PageData;

    let currentRound = data.response.rounds.splice(-1)[0];
    let currentPlayerRound = currentRound.playerRounds.filter((playerRound) => playerRound.user.username === 'test1')[0];
    let wage: number = 1;

    console.log("currentRound", currentRound);
    console.log("currentPlayerRound", currentPlayerRound);

    async function submitWage() {
        currentPlayerRound = await playerRoundStoreState.submitWage(wage, currentPlayerRound.id, data.token);
        currentRound = currentPlayerRound.round;
    }

    async function hit() {
        currentPlayerRound = await playerRoundStoreState.hit(currentPlayerRound.id, data.token);
        currentRound = currentPlayerRound.round;

        console.log(currentPlayerRound);
    }

    async function stand() {
        currentPlayerRound = await playerRoundStoreState.stand(currentPlayerRound.id, data.token);
        currentRound = currentPlayerRound.round;

        console.log(currentPlayerRound);
    }

    async function newRound() {
        await gameStoreState.newRound($page.params.uuid, data.token);
        currentRound = $gameStoreState.current.rounds.splice(-1)[0];
        currentPlayerRound = currentRound.playerRounds.filter((playerRound) => playerRound.user.username === 'test1')[0];

        console.log(currentPlayerRound);
    }

</script>

<div class="flex-row justify-center items-center w-10/12">

    {#if currentPlayerRound === null}
        <p>loading ...</p>
    {:else}
        <h1 class="text-3xl text-primary-foreground font-bold text-center py-6">Round n°{data.response.rounds.length + 1}</h1>

        {#if currentPlayerRound.status === 'created'}
            <h2 class="text-3xl text-primary-foreground font-bold text-center py-6">Wage</h2>

            <div class="flex flex-row justify-center items-center">
                <Input type='number' name='wager' id='wager' required min={1} bind:value={wage} class="w-3/12 mr-6"/>
                <button on:click={submitWage} class="bg-success text-primary-foreground px-6 py-3 rounded w-1/12">Wage</button>    
            </div>

        {:else}
            <div class="flex flex-col justify-center items-center w-full">
                <div class="flex flex-col justify-center items-center w-full  mb-16">
                    <div class="flex flex-row justify-between items-center flex-wrap w-full">
                        <div class="flex flex-col justify-center items-center w-1/3">
                            <h1 class="text-3xl font-bold text-primary-foreground">Your hand</h1>
                            <div class="flex flex-row justify-center items-center w-full">
                                {#each currentPlayerRound.currentCards as card}
                                    <PlayingCard suit={card[0]} value={card[1]} />
                                {/each}
                            </div>
                        </div>
                        <span class="text-destructive text-6xl text-center w-1/3">VS</span>
                        <div class="flex flex-col justify-center items-center w-1/3">
                            <h1 class="text-3xl font-bold text-primary-foreground">Dealer hand</h1>
                            <div class="flex flex-row justify-center items-center">
                                {#each currentRound.dealerCards as card}
                                    <PlayingCard suit={card[0]} value={card[1]} />
                                {/each}
                            </div>
                        </div>
                    </div>
                </div>
                {#if currentPlayerRound.status === 'playing'}
                    <div class="flex flex-row justify-center items-center">
                        <button on:click={hit} class="bg-success text-primary-foreground px-6 py-3 rounded mr-6">Hit !</button> 
                        <button on:click={stand} class="bg-destructive text-primary-foreground px-6 py-3 rounded">Stand !</button>     
                    </div>
                {:else if currentPlayerRound.status === 'busted'}
                    <p class="text-destructive text-6xl mb-16">You busted</p>
                    <div class="flex flex-row justify-center items-center">
                        <button on:click={newRound} class="bg-success text-primary-foreground px-6 py-3 rounded mr-6">Play again !</button> 
                    </div>
                {:else if currentPlayerRound.status === 'lost'}
                    <p class="text-destructive text-6xl mb-16">You lost</p>
                    <div class="flex flex-row justify-center items-center">
                        <button on:click={newRound} class="bg-success text-primary-foreground px-6 py-3 rounded mr-6">Play again !</button> 
                    </div>

                {:else}
                    <p class="text-success text-6xl">{currentPlayerRound.status}</p>
                    <div class="flex flex-row justify-center items-center">
                        <button on:click={newRound} class="bg-success text-primary-foreground px-6 py-3 rounded mr-6">Play again !</button> 
                    </div>
                {/if}
            </div>
        {/if}
    {/if}


</div>
