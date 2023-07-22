<?php

    //On recupère la session
    session_start();

    //On vide la session
    $_SESSION = array();

    //On la détruit
    session_destroy();

    //Puis on rammène vers la page accueil
    header('Location: accueil.php');
?>