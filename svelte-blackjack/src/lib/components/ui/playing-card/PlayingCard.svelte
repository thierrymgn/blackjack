<script lang="ts">
    import 'iconify-icon';
    export let value: string;
    export let suit: string;
</script>

<div class="h-64 w-32 bg-white rounded mr-6 text-black text-6xl">
    {value}
    <iconify-icon icon='bi:suit-{suit}' />
</div>