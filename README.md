Stack :

Symfony 8

PHP 8.3

Doctrine ORM

Symfony UX / Live Components

Mailer Symfony

Docker

Mailhog



Installation : 





Lancement Docker : 




MailHog :

Accès via http://localhost:8025/

Vérification de la reception du mail



Commande newsletter :


1. Commande pour seed la base de données avec 3 newsletters

docker compose exec php sh -lc "cd /var/www/html && php bin/console app:seed:newsletters"

2. Commande pour envoyer toutes les newsletters dont les abonnements ont étés validés

docker compose exec php sh -lc "cd /var/www/html && php bin/console app:sendAll:newsletters"

3. Commande pour envoyer une newsletter à tous les inscrits validés de celle-ci (avec "id" un nombre correspondant à l'id de la newsletter souhaitée)

docker compose exec php sh -lc "cd /var/www/html && php bin/console app:send:newsletters id"