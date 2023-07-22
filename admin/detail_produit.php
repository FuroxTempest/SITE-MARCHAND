<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./style_admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />
    <?php
        include('../php/connect_params.php');
        try
        {
            $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
            [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
            );
            $bdd->exec("set schema 'alizonbdd';");
            //selection des catgorie
            $req = $bdd->query('SELECT id_produit, nomprod, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie FROM _produit WHERE id_produit = '.$_GET['id_produit'].';');
            $req->execute();
            $produit = $req->fetch();
            echo '<title>'.ucfirst($produit['nomprod']).' | Détails</title>';
        }
        catch (PDOException $e)
        {
            print "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }
    ?>    
</head>
<body>
    <?php include('head_admin.php'); ?>
        <main class="main_detail_produit">
            <h1 class="text-center mb-5 fw-bold"><?php echo (ucfirst($produit["nomprod"])) ?></h1>
            <article>
                <p>
                    <div class="container row p-0 m-0">
                        <?php
                            foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $img)
                            {
                                echo '<img src="'.$img.'" class="col-4" id="image_produit"/>';
                            }
                        ?>
                    </div>
                </p>
                <form action='modif_produit.php' method='POST' class='detail_prod'>
                    <table>
                        <tbody>

                            <?php
                                // Modifier un produit

                                echo '<input type="hidden" name="id_produit" value="'.$produit['id_produit'].'"/>';
                                $string_produit = array(
                                'id_produit'=>'ID du produit',
                                'nomprod'=>'Nom du produit',
                                'prix_ht'=>'Prix HT (en €)',
                                'prix_ttc'=>'Prix TTC (en €)',
                                'descriptif'=>'Description',
                                'quantite'=>'Quantité de conditionnement',
                                'poids'=>'Poids (en g)',
                                'volume'=>'Volume (en mL)',
                                'stock'=>'Stock',
                                'id_categorie'=>'Catégorie'
                                );

                                $req = $bdd->query("SELECT nom, tva from _categorie WHERE id_cat = ".$produit['id_categorie'].";");
                                $req->execute();
                                $cat_produit = $req->fetch();

                                foreach($produit as $key=>$elt)
                                {
                                    if($key == 'volume' || $key == 'poids')
                                    {
                                        if($key == 'volume' && $produit[$key] != null)
                                        {
                                            echo '
                                            <tr>
                                                <td>'.$string_produit[$key].'</td>
                                                <td>'.$produit[$key].'</td>
                                            </tr>';
                                        }
                                        else if($key =='poids' && $produit[$key] != null)
                                        {
                                            echo '
                                            <tr>
                                                <td>'.$string_produit[$key].'</td>
                                                <td>'.$produit[$key].'</td>
                                            </tr>';
                                        }
                                    }
                                    else if($key == 'id_categorie')
                                    {
                                        echo '
                                        <tr>
                                            <td>Catégorie</td>
                                            <td>'.$cat_produit['nom'].'</td>
                                        </tr>
                                        ';
                                    }
                                    else if($key == 'prix_ttc')
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td>'.number_format($produit[$key], 2, ",", " ").'</td>
                                        </tr>';
                                        echo '
                                        <tr>
                                            <td>'.$string_produit['prix_ht'].'</td>
                                            <td>'.number_format($produit[$key] / $cat_produit['tva'], 2, ",", " ").'</td>
                                        </tr>';
                                    }
                                    else
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td>'.$produit[$key].'</td>
                                        </tr>';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
        </article>
    </main>
</body>
</html>