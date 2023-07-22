<?php

session_start();
unset($_SESSION['id_vendeur']);
header('Location: ./connexion_vendeur.php');

?>