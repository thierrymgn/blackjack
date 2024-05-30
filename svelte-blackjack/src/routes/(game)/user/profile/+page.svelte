<script lang="ts">

    let user = null;

    async function getUser() {
        return fetch('http://127.0.0.1:8888/user/profile', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Bearer': 'Bearer ' + localStorage.getItem('token') || ''
            }}).then(response => response.json())
            .then(data => {
                user = data;
            });
    }
</script>


{#await getUser()}

    <p>Loading...</p>

{:then}
<h1>Welcome {user.username} !</h1>

{/await}
