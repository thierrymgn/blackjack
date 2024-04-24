<script lang="ts">
    import { enhance } from '$app/forms';
    import type { SubmitFunction } from '@sveltejs/kit';
    import type { PageData } from './$types';
	
	export let data: PageData;

    const postEditProfile: SubmitFunction = (input) => {
        return async ({result, update}) => {
            await update();
        }
    };
</script>

<form method="POST" action="/play/profile?edit" class="flex flex-col w-1/3 bg-ring p-6 rounded" use:enhance={postEditProfile}>
    <h1 class="text-3xl text-primary-foreground text-center">My profile</h1>

    <div class="flex justify-around items-center my-6">
        <label class="text-primary-foreground justify-items-start w-1/2" for="username">Username:</label>
        <input class="border justify-items-end w-1/2 py-1 rounded" type="text" id="username" name="username" required bind:value={data.username}/>    
    </div>

    <div class="flex justify-around items-center my-6">
        <label class="text-primary-foreground justify-items-start w-1/2" for="email">Mail:</label>
        <input class="border justify-items-end w-1/2 py-1 rounded" type="text" id="email" name="email" required bind:value={data.email}/>    
    </div>

    <div class="flex justify-around items-center my-6">
        <label class="text-primary-foreground justify-items-start w-1/2" for="wallet">Wallet:</label>
        <input class="border justify-items-end w-1/2 py-1 rounded read-only:" type="text" id="wallet" name="wallet" value={data.wallet} readonly/>  
    </div>

    <div class="flex justify-around items-center my-6">
        <label class="text-primary-foreground justify-items-start w-1/2" for="password">Password:</label>
        <input class="border justify-items-end w-1/2 py-1 rounded" type="password" id="password" name="password" required/>
    </div>

    <div class="flex justify-around items-center my-6">
        <button type="submit" class="px-8 py-2 text-xl bg-accent rounded">Edit</button>
    </div>
</form>
