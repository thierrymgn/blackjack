<script>import "../../app.pcss";</script>

<nav class="flex fixed items-center justify-center flex-wrap w-full p-6 bg-gray-800">
    <div class="flex items-center justify-between flex-wrap w-10/12">
        <div class="flex items-center justify-start flex-shrink-0 w-1/3">
            <a class="text-white" href="/play">Accueil</a>
        </div>
        <div class="flex items-center justify-end flex-shrink-0 w-1/3">
            <a class="text-white mx-3" href="/play/games">Mes parties</a>
            <a class="text-white mx-3" href="/play/profile">Mon profil</a>
            <a class="text-white mx-3" href="/play/logout">Se déconnecter</a>
        </div>

    </div>
</nav>

<main class="bg-primary">
    <div class="min-h-screen flex items-center justify-center">
        <div class="flex items-center justify-center w-full">
            <slot></slot>
        </div>    
    </div>
</main>