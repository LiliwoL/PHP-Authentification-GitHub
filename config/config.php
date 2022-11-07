<?php

/**
 * Chargement des variables d'environnement
 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/..');
$dotenv->load();

// Base de données
define('DATABASE_URL', $_ENV['DATABASE_URL']);
define('DB_USER_TBL', $_ENV['DB_USER_TBL']);

// GitHub API configuration
define('CLIENT_ID', $_ENV['CLIENT_ID']);
define('CLIENT_SECRET', $_ENV['CLIENT_SECRET']);
define('REDIRECT_URL', $_ENV['REDIRECT_URL']);

// Start session
if(!session_id())
{
    session_start();
}

// Try to get the access token
if(isset($_SESSION['access_token']))
{
    $accessToken = $_SESSION['access_token'];
}