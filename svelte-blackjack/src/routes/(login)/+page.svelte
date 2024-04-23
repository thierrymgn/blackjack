<script lang="ts">
    import { enhance } from "$app/forms";
    import type { SubmitFunction } from "@sveltejs/kit";

    const postLogin: SubmitFunction = (input) => {

        return async ({result, update}) => {
            await update();
        }

    };
</script>

<form method="POST" action="/?login" use:enhance={postLogin}  class="flex flex-col w-1/3">
    <div class="flex justify-around items-center my-6">
        <label class="justify-items-start w-1/2" for="username">Username:</label>
        <input class="border justify-items-end w-1/2" type="text" id="username" name="username" />    
    </div>
    <div class="flex justify-around items-center my-6">
        <label class="justify-items-start w-1/2" for="password">Password:</label>
        <input class="border justify-items-end w-1/2" type="password" id="password" name="password" />
    </div>
    <div class="flex justify-around items-center my-6">
        <button type="submit" class="bg-primary px-8 py-4 text-xl">Login</button>
    </div>
</form>
