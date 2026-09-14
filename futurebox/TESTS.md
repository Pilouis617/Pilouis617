# Vérifier FutureBox

La syntaxe PHP et les 11 tests de fonctions ont réussi dans GitHub Actions. Les parcours applicatifs et visuels ci-dessous restent à exécuter avec une base locale de test.

## Syntaxe et fonctions

- Exécuter `php tests/run.php` depuis le dossier futurebox.
- Vérifier les fichiers PHP avec `php -l chemin/du/fichier.php`.
- Lancer l’application selon le README.

## Parcours principal

1. Créer un compte. Vérifier que la liste est vide, sans fausse capsule.
2. Envoyer un formulaire incomplet, un e-mail invalide et deux mots de passe différents : les erreurs doivent être lisibles.
3. Créer une capsule avec un titre, un message, une photo PNG et une date dans deux minutes.
4. Revenir à la liste : les compteurs et le filtre Scellées doivent inclure cette capsule.
5. Consulter la capsule fermée : le titre et la date sont visibles, jamais le contenu.
6. Regarder le HTML reçu et le réseau : le message et la photo ne doivent pas être présents.
7. Attendre la date, puis cliquer sur Vérifier l’ouverture. Le message et la photo deviennent accessibles.
8. Se déconnecter puis se reconnecter : la capsule est conservée.
9. Créer une autre capsule sans photo.
10. Supprimer une capsule : la case de confirmation est obligatoire et les compteurs se mettent à jour.

## Isolation et contenu

- Créer un second compte dans une session différente.
- Essayer l’identifiant d’une capsule du premier compte dans `index.php?page=capsule&id=ID` et `index.php?page=photo&id=ID` : accès refusé.
- Essayer l’URL photo d’une capsule fermée depuis le bon compte : accès refusé jusqu’à la date.
- Modifier l’horloge du navigateur ou masquer le compte à rebours : le serveur doit toujours refuser le contenu avant l’échéance.
- Envoyer une modification sans jeton CSRF : refus.
- Saisir des balises HTML dans un message et un titre : elles doivent s’afficher comme du texte.
- Envoyer un faux fichier image et une image de plus de 3 Mo : refus.
- Ouvrir `/futurebox/database.sql` et `/futurebox/config.php` via Apache : accès refusé.

## Affichage

- Écran de 390 px de large : aucun débordement horizontal, formulaires utilisables.
- Zoom à 200 % : libellés et boutons lisibles.
- Navigation au clavier : focus visible et ordre cohérent.
- JavaScript désactivé : compte, création, lecture à l’échéance et suppression fonctionnent ; aperçu photo et compte à rebours ne sont pas nécessaires.
