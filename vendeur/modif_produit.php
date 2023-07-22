<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./style_vendeur.css">
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
            //selection produits
            $req = $bdd->query('SELECT id_produit, nomprod, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie FROM _produit WHERE id_produit = '.$_POST['id_produit'].';');
            $req->execute();
            $produit = $req->fetch();
            echo '<title>'.ucfirst($produit['nomprod']).' | Modification</title>';
        }
        catch (PDOException $e)
        {
            print "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }
    ?>    
</head>
<body>
    <?php include('head_vendeur.php'); ?>
        <main>
            <h1>
                <?php echo '<p>'.ucfirst($produit['nomprod']).'</p>'; ?>
                <a href="./detail_produit_vendeur.php?id_produit=<?php echo $produit['id_produit'];?>" class="return_back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </a>
            </h1>
            <article>
                <form action='action_modif_produit.php' method='POST' class='detail_prod'>
                    <table>
                        <tbody>

                            <?php
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

                                $req = $bdd->query("SELECT id_cat, nom from _categorie;");
                                $req->execute();
                                $listeCat = $req->fetchAll();

                                $req = $bdd->query("SELECT nom, tva from _categorie WHERE id_cat = ".$produit['id_categorie'].";");
                                $req->execute();
                                $cat_produit = $req->fetch();

                                foreach($produit as $key=>$elt)
                                {
                                    if($key == 'volume' || $key == 'poids')
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td><input type="number" min="0" step="1" name="'.$key.'" value="'.$produit[$key].'"/></td>
                                        </tr>';
                                    }
                                    else if($key == 'id_categorie')
                                    {
                                        echo '
                                        <tr>
                                            <td>Catégorie</td>
                                            <td id="modification">';

                                        echo '<select name="'.$key.'" id="select">'.PHP_EOL;
                                        foreach($listeCat as $nameCat)
                                        {
                                            if($nameCat['id_cat'] == $produit['id_categorie'])
                                            {
                                                echo '<option value="'.$nameCat['nom'].'" selected>'.$nameCat['nom'].'</option>'.PHP_EOL;
                                            }
                                            else
                                            {
                                                echo '<option value="'.$nameCat['nom'].'">'.$nameCat['nom'].'</option>'.PHP_EOL;
                                            }
                                        }
                                        echo '    <option value="autre">Créer une nouvelle catégorie</option>';
                                        echo '</select>';

                                        echo'    
                                            </td>
                                        </tr>
                                        ';
                                    }
                                    else if($key == 'quantite' || $key == 'stock')
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td><input type="number" min="0" step="1" name="'.$key.'" value="'.$produit[$key].'" required/></td>
                                        </tr>';
                                    }
                                    else if($key =='prix_ttc')
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td><input type="number" min="0" step="0.01" name="'.$key.'" value="'.$produit[$key].'" required/></td>
                                        </tr>';
                                        echo '
                                        <tr>
                                            <td>'.$string_produit['prix_ht'].'</td>
                                            <td>'.number_format($produit[$key] / $cat_produit['tva'], 2, ",", " ").'</td>
                                        </tr>';
                                    }
                                    else if($key == 'id_produit')
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td>'.$produit[$key].'</td>
                                        </tr>';
                                    }
                                    else
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td><input type="text" name="'.$key.'" value="'.$produit[$key].'" required/></td>
                                        </tr>';
                                    }
                                }
                            ?>

                           
                        </tbody>
                    </table>
                    <input type="submit" id="submit" value="Enregistrer"/>
                </form>
        </article>
    </main>

</body>

<script>

    // Def

    var select = document.getElementById("select");
    var autre = document.createElement("input");
    var submit = document.getElementById("submit");
    var tdModif = document.getElementById("modification");
    autre.type = "text";
    autre.name = "nomNewCat";
    autre.id = "autre";
    autre.placeholder = "Entrez le nom de la nouvelle catégorie";

    // function

    function disableSubmit()
    {
        submit.disabled = true;
        submit.style = "background-color : #DDDDDD;"
    }

    function activateSubmit()
    {
        submit.disabled = false;
        submit.style = "background-color : #F28F16;"
    }

    function insertAfter(newNode, existingNode)
    {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

    function onChangeSelect()
    {
        if(select.options[select.selectedIndex].value == "autre")
        {
            disableSubmit();
            insertAfter(autre, select);
        }
        else
        {
            if(autre.parentNode == tdModif)
            {
                tdModif.removeChild(autre);
                autre.value = "";
                if(submit.disabled)
                {
                    activateSubmit();
                }
            }
        }
    }

    function onInputButton()
    {
        if(autre.value == "" && !submit.disabled)
        {
            disableSubmit();
        }
        else if(autre.value != "" && submit.disabled)
        {
            activateSubmit();
        }
    }

    // Listener

    select.onchange = onChangeSelect;
    autre.oninput = onInputButton;
    onChangeSelect();

</script>

</html>