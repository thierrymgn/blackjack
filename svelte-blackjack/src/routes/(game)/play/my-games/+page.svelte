<script lang="ts">
  import type { PageData } from './$types';
  import * as Table from "$lib/components/ui/table";
  import { Button } from "$lib/components/ui/button";

	export let data: PageData;
</script>

<div class="flex flex-col justify-between text-primary-foreground items-center w-10/12">
  <h1 class="text-3xl py-6">My recent games</h1>
  {#if data.response === null || data.response.length === 0}
      <p>No games found</p>
  {:else}
    <div class="rounded-md border w-10/12">
      <Table.Root>
        <Table.Caption>A list of your recent games.</Table.Caption>
        <Table.Header>
          <Table.Row>
            <Table.Head>Id</Table.Head>
            <Table.Head>Started at</Table.Head>
            <Table.Head>Status</Table.Head>
            <Table.Head>Actions</Table.Head>
          </Table.Row>
        </Table.Header>
        <Table.Body>
          {#each data.response as game}
            <Table.Row>
              <Table.Cell>{game.id}</Table.Cell>
              <Table.Cell>{game.creationDate}</Table.Cell>
              <Table.Cell>{game.status}</Table.Cell>
              <Table.Cell>
                <Button href={`/play/game/${game.id}`} class="bg-success">Resume</Button>
              </Table.Cell>
            </Table.Row>
          {/each}
        </Table.Body>
      </Table.Root>
    </div>
  {/if}
</div>


