<script lang="ts">
    import { gameStoreState } from "$lib/stores";
    import type { PageData } from './$types';

    export let data: PageData;
	
    async function startNewGame() {
        const result = await gameStoreState.createNewGame(data.token);
        if(result) {
           window.location.href = `/play/game/${result.id}`;
        }
    }
</script>

<button class="bg-secondary text-4xl px-6 py-3 rounded" on:click={startNewGame}>New game</button>
