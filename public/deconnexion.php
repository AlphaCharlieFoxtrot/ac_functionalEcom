<?php
require_once '../config/config.php';
$_SESSION = [];

if (ini_get("session.use_cookies")){
    $params = session_get_cookie_params();
    setcookie(
        session_name(),//recupère le nom du cookie de session
        '',
        time() - 42000,//date d'expiration dans le passé
        $params["path"],//chemin du cookie
        $params["domain"],//domaine du cookie
        $params["secure"],//ne l'envoie pas si https n'est pas utilisé
        $params["httponly"]//empêche l'accès au cookie via javascript
    );
}

session_destroy();//demande a php de détruire la session cote serveur

header("Location: index.php");//redirection vers la page d'accueil
exit();