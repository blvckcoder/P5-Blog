# Blog PHP POO

## Description

Ce projet est un blog professionnel développé avec PHP en programmation orientée objet (POO).

## Prérequis

- PHP 8.2 ou plus récent.
- Un serveur MySQL.
- Composer pour la gestion des dépendances PHP.
- Un serveur web tel qu'Apache ou Nginx.

## Installation

### Étape 1: Clonage du projet

Pour commencer, clonez le dépôt sur votre machine locale :

```
git clone https://github.com/blvckcoder/P5-Blog.git
```

### Étape 2: Installation des dépendances

Déplacez-vous dans le répertoire du projet cloné et installez les dépendances avec Composer :

```
cd répertoire
composer install
```
### Étape 3: Configuration des variables d'environnement

Créez un fichier .env dans le dossier public, ouvrez le et modifiez les valeurs pour correspondre à votre environnement (base de données, etc.) :

```
DB_HOST=localhost
DB_NAME=nom_de_votre_base
DB_USER=utilisateur
DB_PASS=mot_de_passe
```

### Étape 4: Installation de la base de données

Importez le fichier SQL fourni pour créer la structure de la base de données. Vous pouvez utiliser PHPMyAdmin ou la ligne de commande :

``` 
mysql -u utilisateur -p nom_de_votre_base < chemin_vers_le_fichier_sql
```
### Étape 5: Configuration du serveur web

Configurez votre serveur web pour pointer vers le dossier public du projet. Assurez-vous que le mod_rewrite est activé pour Apache.

## Utilisation

Après l'installation, ouvrez votre navigateur et accédez à l'URL configurée pour votre projet.

## Contribuer

Les contributions au projet sont les bienvenues. Merci de suivre les étapes classiques : Fork -> Patch -> Push -> Pull Request.

## Licence

Ce projet est sous licence MIT. Pour plus d'informations, voir le fichier LICENSE.