<script lang="ts">
    import { enhance } from "$app/forms";
    import type { SubmitFunction } from "@sveltejs/kit";

    let displayAuthError: boolean = false;

    const postLogin: SubmitFunction = (input) => {
        displayAuthError = false;

        return async ({result, update}) => {
            await update();
            if(result.data.error !== undefined && result.data.error === true) {
                displayAuthError = true;
            }else{

            }
        }

    };
</script>

<form method="POST" action="/?login" use:enhance={postLogin}  class="flex flex-col w-1/3 bg-ring p-6 rounded">
    <h1 class="text-3xl text-primary-foreground text-center">Login</h1>

    <div class="flex justify-around items-center my-6">
        <label class="text-primary-foreground justify-items-start w-1/2" for="username">Username:</label>
        <input class="border justify-items-end w-1/2 py-1 rounded" type="text" id="username" name="username" required/>    
    </div>
    <div class="flex justify-around items-center my-6">
        <label class="text-primary-foreground justify-items-start w-1/2" for="password">Password:</label>
        <input class="border justify-items-end w-1/2 py-1 rounded" type="password" id="password" name="password" required/>
    </div>

    {#if displayAuthError}
        <p class="text-red-500 text-center">Invalid username or password</p>
    {/if}

    <div class="flex justify-around items-center my-6">
        <button type="submit" class="px-8 py-2 text-xl bg-accent rounded">Login</button>
    </div>
</form>
