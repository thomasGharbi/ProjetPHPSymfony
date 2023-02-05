<h3>Environnement utilisé durant le développement</h3>


<ul>
    <li>PHP 8.0.11</li>
    <li>Symfony 5.3.16</li>
    <li>Composer 2.1.5</li>
    <li>PhpUnit 9.5</li>
    <li>Mysql 8.0.26</li>
    


## Installation

1.Clonez ou téléchargez le repository GitHub dans le dossier voulu : 
```bash
 git clone https://github.com/thomasGharbi/ProjetPHPSymfony.git
```


2.Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier `.env.local`qui devra être crée à la racine du projet en réalisant une copie du fichier `.env.`



3.Téléchargez et installez les dépendances back-end du projet avec [Composer](https://getcomposer.org/doc/00-intro.md) :

```bash
 composer install
```


4.Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :

```bash
 php bin/console doctrine:database:create
```



5.Créez les différentes tables de la base de données en appliquant les migrations :

```bash
 php bin/console doctrine:migrations:migrate
```


.6(Optionnel) Installer les fixtures pour avoir une démo de données fictives :

```bash
php bin/console doctrine:fixtures:load
```



7.Le projet est installé , vous pouvez désormais commencer à l'utiliser.


