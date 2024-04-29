<script lang="ts">
    import { goto } from "$app/navigation";
    import { gameStoreState } from "$lib/stores";
    import type { PageData } from './$types';

    export let data: PageData;
	
    async function startNewGame() {
        const result = await gameStoreState.createNewGame(data.token);
        if(result !== null) {
           goto(`/play/game/${result.current.id}`);
        }
    }
</script>

<button class="bg-success text-primary-foreground text-4xl px-6 py-3 rounded" on:click={startNewGame}>New game</button>
