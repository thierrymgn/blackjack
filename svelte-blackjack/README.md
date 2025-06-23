# Sveltekit Blackjack front

## Installation du projet

Après avoir lancé `docker compose up -d --build` pour créer les containers, lancer les commandes suivantes : 

```bash
# Pour installer les dépendances du projet
docker compose exec -u 1000 svelte-blackjack npm install

# Pour lancer le server web
docker compose exec -u 1000 svelte-blackjack npm run dev -- --host
```

L'application sera disponible sur `http://localhost:5173`.

