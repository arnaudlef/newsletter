Stack :

Symfony 8

PHP 8.3

Doctrine ORM

Symfony UX / Live Components

Mailer Symfony

Docker

Mailhog



Installation : 


Git clone via HTTPS : git clone https://github.com/arnaudlef/newsletter.git

Installer Docker Desktop (si windows) puis se mettre a la racine du projet et faire la commande suivante : docker compose up -d --build

Installer Composer dans le container PHP : docker compose exec php sh -lc "cd /var/www/html && composer install"

Créer la base de données : docker compose exec php sh -lc "cd /var/www/html && php bin/console doctrine:database:create"

Appliquer les migrations : docker compose exec php sh -lc "cd /var/www/html && php bin/console doctrine:migrations:migrate -n"

Seed la base avec 3 fake newsletters : docker compose exec php sh -lc "cd /var/www/html && php bin/console app:seed:newsletters"

Installer importmap : docker compose exec php sh -lc "cd /var/www/html && php bin/console importmap:install"

Copier .env.example et le renommer .env.local

Vider le cache : docker compose exec php sh -lc "cd /var/www/html && rm -rf var/cache/*"

Accéder au site : http://localhost:8080/subscribe



MailHog :


Accès via : http://localhost:8025/

Vérification de la reception du mail



Commande newsletter :


1. Commande pour seed la base de données avec 3 newsletters

docker compose exec php sh -lc "cd /var/www/html && php bin/console app:seed:newsletters"

2. Commande pour envoyer toutes les newsletters dont les abonnements ont étés validés

docker compose exec php sh -lc "cd /var/www/html && php bin/console app:sendAll:newsletters"

3. Commande pour envoyer une newsletter à tous les inscrits validés de celle-ci (avec "id" un nombre correspondant à l'id de la newsletter souhaitée)

docker compose exec php sh -lc "cd /var/www/html && php bin/console app:send:newsletters id"