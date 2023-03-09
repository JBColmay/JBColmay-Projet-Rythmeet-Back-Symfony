# Comment installer le back 

## Cloner le repo du back

## L’ouvrir dans une autre fenêtre avec VScode

## -ouvrir le terminal et lancer la commande suivante:
	composer install

## lancer le serveur en commande dans le terminal :
    php -S 0.0.0.0:8000 -t public

## Aller dans adminer

## Créer une BDD : rythmeet

## Ajouter un user avec tous les droits : 
	- username : rythmeet
	- password : rythmeet

## Paramètrage de doctrine  

## créer un fichier .env.local

## ajouter le DATABASE URL dans .env.local 

on verifie la version de MariaDB en ligne de commande dans le terminal :
    mysql -V

ça va nous donner un résultat comme celui çi en fonction de la version : 
mysql  Ver 15.1 Distrib 10.3.37-MariaDB, for debian-linux-gnu (x86_64) using readline 5.2

ce qui se traduit par mariadb-10.3.37 pour paramétrer doctrine.

On va ensuite prendre depuis le fichier .env la ligne de code suivant que nous allons copier dans le .env.local : 
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"

On va remplacer les éléments nous concernant en fonction de la BDD et de mariaDB :
 DATABASE_URL="mysql://rythmeet:rythmeet@127.0.0.1:3306/rythmeet?serverVersion=mariadb-10.3.25"

## Valider la BDD
Dans le terminal faire la commande suivante pour valider la BDD : 
    bin/console doctrine:schema:validate


## Génération de la structure de la BDD : migration
Dans le terminal faire la commande :
    bin/console make:migration ou bin/console ma:mi



## Exécution de la migration
Dans le terminal faire la commande :
bin/console doctrine:migrations:migrate ou - bin/console d:m:m


## Commande lexikJWT à faire
php bin/console lexik:jwt:generate-keypair