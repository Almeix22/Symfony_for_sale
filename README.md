<h1> Symfony 6 « avancé » Projet symfony-for-sales</h1>
<h2> Auteur : François Axel & Arbër Jonuzi</h2>
<br>
<h2>Installation / Configuration </h2>
<p>Récuperer et installer le projet : <br>
git clone https://github.com/Almeix22/Symfony_for_sale <br>
</B></p>
<p> Ensuite il faut installer composer à l'aide de la commande : <br>
<B>composer install</B> 
<p> Et NPM à l'aide de la commande : <br>
<B>npm install</B></p>
<p>Une fois composer installé il est nécessaire de lancer le <B>dockerFile</B> pour se faire utiliser la commande <B>docker compose up</B></p>
<p>Une fois docker démarré il faut maintenant lancer la construction des assets Sass. pour cela lancer la commande : <B>npm run watch</B></p>
<p>Et enfin utilisez la commande : <B>composer start</B> pour lancer le serveur web local et accéder au site </p>
<p>Il peut être éventuellement nécessaire de recréer la base de donnée, pour se faire utiliser la commande : <strong>composer db </strong></p>
<p><B>Le site devrait normalement être accessible et entièrement fonctionnel</B></p>

<h2>Description des scripts </h2>
<h3>Script composer, afin de lancer les script utilisé la commande composer "nom_du_script"</h3>
<p>
<B>"start"</B>: "Lance le serveur web de test sans restriction de durée d'execution" <br>
<B>"test:cs"</B>: "lance la commande de vérification du code par PHP CS Fixer" <br>
<B>"fix:cs"</B>: " lance la commande de correction du code par PHP CS Fixer" <br>
<B>"test:yaml"</B>: "Lance la commande de vérification des fichiers YAML contenus dans le répertoire « config »" <br>
<B>"test:twig"</B>: "Lance la commande de vérification des fichiers Twig contenus dans le répertoire « templates » <br>
<B>"db":</B> "force la suppression de la base de données, si elle existe, créer la base de données, applique les migrations, charge les données factices" <br>

<h3>Script npm, afin de lancer les script utilisé la commande npm run "nom_du_script"</h3>

<B>"dev"</B>: lance la construction des assets <br>
<B>"watch"</B>: lance la construction des assets Sass <br>
<B>"test:codeception"</B>: lance les suites de tests de Codeception <br>
<B>"composer test"</B>: lance tous les tests décrits au dessus
</p>