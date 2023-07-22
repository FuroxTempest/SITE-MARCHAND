<?php
    session_start();

    include('./connect_params.php');
    try{
        $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
        );
    }catch (PDOException $e){
        print "Erreur ! : " . $e->getMessage() . "<br/>";
        die();
    }

    if (isset($_POST['email']) && isset($_POST['mdp'])){

        // le mdp est t'il le bon ?
        $email=$_POST['email'];
        $mdp=$_POST['mdp'];
        try{
            $req=$bdd->prepare("SELECT email,mdp from alizonbdd._client where email =?;");
            $req->execute([$email]);
            $info_cli=$req->fetchAll();
            $row=$req->rowCount();
        }catch (PDOException $e){
            echo "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }
        if($row==1){
            //oui: on mémorise l'id
            if (password_verify($mdp,$info_cli[0]['mdp'])) {
                $_SESSION['adresseEmail']=$_POST['email'];
                $_SESSION['motDePasse']=$_POST['mdp'];
                $req1=$bdd->prepare("SELECT nom,prenom from alizonbdd._client where email =?;");
                $req1->execute([$email]);
                $result = $req1->fetch();
                $_SESSION['nom']=$result['nom'];
                $_SESSION['prenom']=$result['prenom'];

                $reqID = $bdd->prepare("SELECT id_client FROM alizonbdd._client WHERE email = ?;");
                $reqID->execute(array($email));
                $id_cli=$reqID->fetch();
                $_SESSION['id']=$id_cli['id_client'];

                header("Location: accueil.php");
                die();
            } else {
                header("Location: connexion.php?mauvais_id=false");
                session_unset();
            }
            
        }else{
            header("Location: connexion.php?mauvais_id=false");
        session_unset();
        //plutot pas utiliser le : session_destroy();
    }
}

?>