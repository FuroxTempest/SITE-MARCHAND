<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat barre de recherche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./style_admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />
    <title>Vendeur | Recherche Client</title>
</head>
<body >    
<nav class="navbar p-3 p-lg-2 justify-content-center">
        <!-- barre de recherche -->
        <form id="form_barre_recherche" class="d-flex col-6 border border-dark border-3 ml-2" name="form_recherche" method="get" action="recherche_client.php" role="search" >
            <input id="search" name="keyword" class="form-control" type="text" placeholder="Rechercher un client" autocomplete="off"  aria-label="Search">
            <button class="btn p-0 border-0" type="submit" id="button-addon1" name=""><img id="recherche"  src="../images/loupe.png" alt="loupe recherche" /></button>
        </form>
    </nav>
    <?php
        //1) paramètre de la bdd
        include("../php/connect_params.php");
        //2) ouvrir la bdd
        $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
        );
        
        $bdd->exec("set schema 'alizonbdd';");

        include("./head_admin.php");

        $keyword=$_GET['keyword'];
        $keyword = strToLower(trim($keyword));

        if(!empty($keyword)){

            $word=explode(" ",trim($keyword));
            for($i=0; $i<count($word); $i++){
                $kw[$i]="nom like '%".$word[$i]."%' OR prenom like '%".$word[$i]."%'";
            }
                $tab=array();

                $client=$bdd->prepare("SELECT nom, prenom, email, rue, code, ville, telephone, mdp, id_client from alizonbdd._client where ".implode(" or ",$kw)."");            
                $client->setFetchMode(PDO::FETCH_ASSOC);
                $client->execute();

                foreach($client as $row){
                    array_push($tab, $row);
                }      
            $afficher="oui";  
        }
    ?>    
        <?php if(@$afficher=="oui"){ ?>
                <div class="nbr_res container">
                    <h3 class=""><?=count($tab)." ".(count($tab)>1?"résultats trouvés pour &nbsp"."\"$keyword\""."":"résultat trouvé pour &nbsp"."\"$keyword\""."") ?></h3>
                </div>
                <section class="liste_cli liste_cli2">
                    <?php
                        foreach($tab as $client){
                    ?>
                    <div class="card-body">
                        <?php
                            echo "<div class=\"titre_carte\">$client[nom] $client[prenom]</div>";
                            echo "<p class=\"card-text\">mail: $client[email]</p>";
                            echo "<p class=\"card-text\">adresse: $client[adresse]</p>";
                            echo "<p class=\"card-text\">tel: $client[telephone]</p>";
                        ?>
                        <form action="./detail_client.php" method="post" class="">
                        <?php
                            echo "<input type=\"hidden\" name=\"idclient\" value=\"".$client['id_client']."\">";
                        ?>
                        <button type="submit" class="btn btn-secondary btn-lg">Modifier</button>
                        </form>
                    </div>
                    <?php } ?> 
            </section>
       <?php } ?>      
</body>
</html>
