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

    $bdd->exec("set schema 'alizonbdd';");
    $mail=$_SESSION['adresseEmail'];
    $req1=$bdd->prepare("SELECT id_client from _client where email=?;");
    $req1->execute([$mail]);
    $client=$req1->fetch();
    $idCli=$client['id_client'];

    $req2=$bdd->prepare("SELECT id_commande from _commande where id_client =? and statut_commande='en cours';");
    $req2->execute([$idCli]);
    $commande=$req2->fetch();
    $idCom=$commande['id_commande'];

    $req3=$bdd->prepare("SELECT id_produit,nb_article from _panier where id_commande =?;");
    $req3->execute([$idCom]);
    $panier=$req3->fetchAll();

    foreach($panier as $key){
        $idProd=$key['id_produit'];
        $nbArt=$key['nb_article'];

        $selectStock=$bdd->prepare("SELECT stock from _produit WHERE id_produit=?");
        $selectStock->execute([$idProd]);
        $valStock=$selectStock->fetch();
        $stock=$valStock['stock'];
        $stock=$stock-$nbArt;

        $updateStock=$bdd->prepare("UPDATE _produit SET stock=? WHERE id_produit=?");
        $updateStock->execute([$stock,$idProd]);
    }

    $reqUpdate=$bdd->prepare("UPDATE _commande SET statut_commande='préparation' where id_commande=? ;");
    $reqUpdate->execute([$idCom]);

    header("Refresh:5 ; url=./commande.php", 5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | ALIZON</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />

    
</head>
<body>
    <?php
        include("./header.php");
    ?>
    <section style="min-height: 57vh;">
        <article id="confirmation" >
            <h1>CONFIRMATION</h1>
            <p>Votre commande a bien été prise en compte. </p>
            <p>Vous serez redirigé vers vos commande dans 5 secondes</p>

        </article>

    </section>

    <?php
        include("./footer.php")
    ?>
</body>
</html>

