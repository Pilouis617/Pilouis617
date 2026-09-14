# FutureBox

FutureBox permet d’écrire un message pour plus tard, d’y joindre une photo et de choisir sa date d’ouverture. Une fois la capsule scellée, son contenu reste inaccessible dans l’application jusqu’à cette date.

Le projet utilise PHP et MySQL, avec du HTML, du CSS et un peu de JavaScript. Il n’a pas de dépendance à installer avec Composer ou npm.

## Fonctionnalités

- Création de compte et connexion.
- Message, titre, photo facultative et date d’ouverture.
- Liste des capsules, filtres et compteurs calculés depuis la base.
- Compte à rebours indicatif, vérification de la date côté serveur.
- Lecture du message et de la photo uniquement après l’échéance.
- Suppression avec confirmation.
- Interface utilisable sur téléphone et sans JavaScript pour les actions essentielles.

## Installation avec XAMPP

Prérequis : PHP 8.1 ou supérieur, MySQL 5.7+ ou MariaDB 10.4+, extensions PDO MySQL, mbstring et fileinfo. Le projet vise un usage local.

1. Télécharge le dossier **futurebox** et place-le dans `C:\xampp\htdocs\futurebox`.
2. Démarre **Apache** et **MySQL** dans XAMPP.
3. Ouvre `http://localhost/phpmyadmin` puis importe **database.sql**.
4. Vérifie **config.php** : serveur `127.0.0.1`, base `futurebox`, utilisateur `root`, mot de passe vide dans une installation XAMPP standard.
5. Ouvre **http://localhost/futurebox/public/**.
6. Clique sur **Créer mon compte**, puis écris une première capsule.

Ne double-clique pas sur les fichiers PHP : ils doivent être servis par PHP.

Si mbstring ou fileinfo est désactivé, active l’extension correspondante dans php.ini, puis redémarre Apache. Pour les photos de 3 Mo, règle `upload_max_filesize = 4M` et `post_max_size = 8M`, puis redémarre Apache. Le contrôle applicatif conserve sa limite de 3 Mo.

### Avec le serveur intégré de PHP

Depuis le dossier futurebox, avec MySQL déjà démarré et la base importée :

```bash
php -S localhost:8000 -t public
```

Ouvre ensuite **http://localhost:8000**.

Le dossier public doit être la racine web. Pour l’URL XAMPP ci-dessus, les fichiers .htaccess protègent les fichiers internes ; conserve-les et laisse Apache prendre en compte les directives .htaccess. Ne lance pas un serveur statique à la racine du projet.

## Organisation

| Fichier / dossier | Rôle |
| --- | --- |
| public/index.php | Point d’entrée, traitement des formulaires et choix des pages |
| src/functions.php | Base de données, dates, contrôle d’accès, photos et protection des formulaires |
| templates/ | HTML des différentes pages |
| public/assets/ | CSS, JavaScript et icône |
| database.sql | Création de la base et des tables |
| config.php | Configuration locale |
| tests/run.php | Vérifications des dates et de l’échappement HTML |
| TESTS.md | Parcours de vérification complet |

## Quelques choix techniques

**Dates.** La saisie est interprétée en heure de Paris. La base conserve un timestamp UTC. Les heures inexistantes ou répétées lors des changements d’heure sont refusées pour éviter une échéance ambiguë.

**Ouverture.** Le compteur JavaScript est indicatif. PHP contrôle la date à chaque consultation. Le contenu d’une capsule fermée n’est pas envoyé au navigateur : les listes ne sélectionnent que les métadonnées, et l’accès à une photo repasse par une requête vérifiant le propriétaire et l’échéance.

**Photos.** Les images sont limitées à 3 Mo, identifiées avec fileinfo et vérifiées avec getimagesize. Elles sont stockées en base, sans URL publique vers un dossier d’uploads.

**Comptes.** Les mots de passe sont hachés avec password_hash et vérifiés avec password_verify. Les requêtes SQL sont préparées. Chaque modification exige un jeton CSRF et les sessions sont renouvelées à la connexion.

**Données.** Aucun contenu de démonstration n’est ajouté. Chaque compte commence avec une liste vide. Les suppressions sont définitives.

## Limites de cette version

- Application pour usage local : pas de récupération de mot de passe, de vérification d’e-mail, de limitation des tentatives de connexion ni de rappel automatique.
- Le propriétaire du serveur ou de la base peut lire les données : ce n’est pas un coffre chiffré.
- La disponibilité dépend du serveur, de son horloge et de la conservation de la base. Sauvegarde MySQL si tu veux garder tes capsules longtemps.
- Avant une mise en ligne publique, prévoir HTTPS, un compte MySQL dédié, limitation des connexions, sauvegardes et configuration serveur adaptée.
- GitHub Pages ne peut pas exécuter ce projet PHP/MySQL.

## Vérifier

```bash
php tests/run.php
```

Ces tests ne nécessitent pas de connexion MySQL. Le parcours dans **TESTS.md** complète les tests avec les comptes, l’ouverture et les photos.

La syntaxe PHP et les 11 vérifications de tests/run.php ont été validées par GitHub Actions. Le parcours complet avec MySQL et le rendu dans le navigateur restent à vérifier sur une installation locale.
