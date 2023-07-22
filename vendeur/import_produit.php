<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="./style_vendeur.css">
    </head>
    <body>
<?php
    session_start();
    include("../php/connect_params.php");
        if(!empty($_FILES['fichier']['tmp_name']))
    {
        if(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION) == "csv")
        {
            $fichier = fopen($_FILES['fichier']['tmp_name'],"r");
            $i=0;
            while(!feof($fichier))
            {
                $csv[$i] = fgetcsv($fichier,null,",",'"');
                $i++;
            }
            fclose($fichier);
            
            try
            {
                
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                $dbh->exec("set schema 'alizonbdd';");
                foreach($csv as $nbr=>$line)
                {
                    if($nbr!=0)
                    {
                        $elements = [];
                        foreach($line as $count=>$elt)
                        {
                            if($elt == '')
                            {
                                $elements[$count]=NULL;
                            }
                            else
                            {
                                $elements[$count]=$elt;
                            }
                        }
                        array_push($elements, $_SESSION['id_vendeur']);
                        $elements[0] = strtolower($elements[0]);
                        $elements[7] = strtolower($elements[7]);
                        $stmt = $dbh->prepare("insert into _import_produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, nom_cat, id_vendeur) values(?,?,?,?,?,?,?,?,?);");
                        $stmt->execute($elements) or die(print_r($stmt->errorInfo(), true));
                    }
                }
            }
            catch (PDOException $e)
            {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
            $dbh = null;
            header("Location: ./import_action_vendeur.php?msg=1");
        }
        else
        {
            header("Location: ./import_action_vendeur.php?msg=2");
        }
    }
    else
    {
        header("Location: ./import_action_vendeur.php?msg=3");
    }
?>
    </body>
</html>