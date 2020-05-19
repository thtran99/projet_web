# PROJET 2 DE WEB 2020 L3 Info (Tran Thibault)

Bienvenu sur La nouvelle plateforme d'apprentissage en ligne ZOOM ZOOM qui détrônera bientôt Moodle !
  
## Lancement du projet
  Une fois l'archive décompresser, il faut lancer le serveur WAMP sur Windows ou LAMP sur Linux. Dans le dossier, ouvrir un terminal et éxécuter : 
  * Pour créer la base de données :  php bin/console doctrine:migrations:migrate 
  * Pour créer de fausses données : php bin/console doctrine:fixtures:load
  * Pour lancer le serveur : symfony server:start
  
  Enfin dans votre navigateur web favori, il suffit écouter le port 8000 du localhost : [localhost:8000](http://localhost:8000/)


## Modification éventuelle de configuration

* Les identifiants de la base de données : Allez dans le document .env et modifier etu21703390 dans la ligne 32 : DATABASE_URL=mysql://etu21703390:etu21703390@127.0.0.1:3306/etu21703390?serverVersion=5.7

  (Dans l'ordre : db_user, db_password et db_name)


## Principe du Backend

* Un utilisateur : 
  * [Etudiant] : il s'inscrit à des cours. Il résoud des exercices ainsi il a des notes. 
  * [Enseignant] : il crée des cours et des exercices. 

* Un cours : contient des exercices que l'enseignant créé. Il contient une liste d'utilisateurs dont l'enseignant qui l'a crée et les élèves qui y sont inscrit. 

* Un exercice : est composé de "lignes" et possède un nombre de tentative.

* Une ligne : est composé d'un rang, d'une indentation et d'un contenu.

* Une notation : relie un utilisateur à un exercice et associe une note.


## Utilisation du Frontend

Nous avons créé de fausses données dont deux utilisateurs :
(dans le fichier src\DataFixtures\AppFixtures.php) 

* Pour vous connecter en tant qu'enseignant. Mail : prof@prof.prof Mdp : pass1234.

* Pour vous connecter en tant qu'étudiant : Mail : etu@etu.etu Mdp : pass1234.

### Les différentes pages du site : 

* Dans la barre de navigation :

  * [Inscription](http://localhost:8000/inscription) : Permet de s'inscrire en tant qu'étudiant ou enseignant.
      
  * [Connexion](http://localhost:8000/connexion) : Permet de se connecter à l'aide de son adresse mail et d'un mot de passe. 

  * [Tous les cours](http://localhost:8000/profile/cours) : Affiche les cours créé par tous les enseignants.

  * [Mes cours](http://localhost:8000/profile/mesCours) : Affiche les cours de l'utilisateur connecté. 

  * [Créer un cours](http://localhost:8000/editor/cours/new) : [Spécifique aux enseignants] Page de création d'un nouveau cours

  * [Déconnection](http://localhost:8000/deconnexion) : Déconnecte l'utilisateur afin de changer de compte par exemple.

* En naviguant :

  * localhost:8000/profile/cours/{idCours} : Accéde à la page d'un cours
    * [Enseignant] : Permet d'accéder au contenu du cours et de créer des exercices directement rattachés au cours
    * [Etudiant] : Permet de s'inscrire, désinscrire au cours et d'accéder aux différents exercices

  * localhost:8000/editor/cours/{idCours}/exercice/{idExercice} : Accéde à la page d'un exercice. 
    * [Enseignant] : Affiche la correction et la note des étudiants inscrits
    * [Etudiant] : Affiche la page de résolution de l'exercice -> Les lignes du code sont affichés dans un ordre aléatoire. L'étudiant doit remettre les lignes le bon ordre avec la bonne indentation.

  * localhost:8000/editor/cours/{idCours}/exercice/new : 
  [Spécifique aux enseignants] : Création d'un exercice -> entrer un titre, un enoncé et un nombre de ligne correspondant aux nombres de ligne total de votre exercice. Cliquer sur suivant.

  * localhost:8000/editor/cours/{idCours}/exercice/{idExercice}/newLines :
  [Spécifique aux enseignants] : Création du contenu de l'exercice -> à chaque ligne, entrer l'indentation et le contenu. Si le nombre de ligne ne correspond pas, cliquer sur retour. Sinon créer.

  * localhost:8000/editor/cours/{idCours}/exercice/{idExercice}/edit : 
  [Spécifique aux enseignants] : Modification de l'exercice -> Modifier titre, enoncé, contenu...




                  