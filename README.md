# Authentification d'une application avec GitHub


## Base de données

Il est nécessaire de garder les informations de l'utilisateur qui réussit à se connecter via Github.
Pour cela, on crée une table **users** dans la base.

*sql/users.sql*
```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oauth_provider` enum('github','facebook','google','twitter') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'github',
  `oauth_uid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

## Classe Client OAuth de connexion

*client/Github_OAuth_Client.php*

C'est une classe PHP qui va gérer les appels REST API à gitHub.

Méthodes:

* getAuthorizeURL() – Generate URL to authorize with the Github account.
* getAccessToken() – Exchange oauth code and get access token from Github OAuth API.
* apiRequest() – Make an API call and retrieve the access token from Github OAuth API.
* getAuthenticatedUser() – Execute the cURL request to get the authenticated user account data from Github User API.


***

## Classe de gestion des utilisateurs


## Fichier de configuration

Database constants:

    DB_HOST – Specify the database host.
    DB_USERNAME – Specify the database username.
    DB_PASSWORD – Specify the database password.
    DB_NAME – Specify the database name.
    DB_USER_TBL – Specify the table name where the user’s account data will be stored.

GitHub API constants:

    CLIENT_ID – Specify the GitHub App Client ID.
    CLIENT_SECRET – Specify the GitHub App Client Secret.
    REDIRECT_URL – Specify the Authorization callback URL.

Call GitHub API:

    The Github OAuth PHP Client library is used to connect with Github API and working with OAuth client.
    Initialize Github_OAuth_Client class and pass Client ID, Secret, and Callback URL to connect with Github API and work with SDK.


