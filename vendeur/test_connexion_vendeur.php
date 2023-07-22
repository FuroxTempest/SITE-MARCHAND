
<?php
    session_start();
    
    include('../php/connect_params.php');
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

        $email_vendeur=$_POST['email'];
        $mdp_vendeur=$_POST['mdp'];


        try{
            $req=$bdd->prepare("SELECT mail,mdp from alizonbdd._vendeur where mail =?;");
            $req->execute([$email_vendeur]);
            $info_cli=$req->fetchAll();
            $row=$req->rowCount();
        }catch (PDOException $e){
            echo "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }

        
        if($row==1){
            if (password_verify($mdp_vendeur,$info_cli[0]['mdp'])) {
                $_SESSION['mailVendeur']=$_POST['mail'];
                $_SESSION['motDePasse']=$_POST['mdp'];

                $reqID = $bdd->prepare("SELECT id_vendeur FROM alizonbdd._vendeur WHERE mail = ?;");
                $reqID->execute(array($email_vendeur));
                $id_cli=$reqID->fetch();
                $_SESSION['id_vendeur']=$id_cli['id_vendeur'];

                header("Location: index.php");
                die();
            } else {
                
                echo '<script>alert("mauvais identifiants de connexion");window.location = "connexion_vendeur.php";</script>'.PHP_EOL;
                session_unset();
                
            }
            
        }else{
            echo '<script>alert("Utilisateur introuvable");window.location = "connexion_vendeur.php";</script>'.PHP_EOL;
        session_unset();
        //plutot pas utiliser le : session_destroy();
        
    }
}

 
