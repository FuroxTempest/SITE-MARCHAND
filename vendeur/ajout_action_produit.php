<!DOCTYPE html>
<html lang="en">
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
    <title>Ajouter un produit</title>
</head>
<body>
    <?php
        include('../php/connect_params.php');
        include('head_vendeur.php');
        try
        {
            $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
            [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
            );
            $bdd->exec("set schema 'alizonbdd';");
            //selection des catgorie
            $sth = $bdd->prepare("SELECT nom FROM _categorie;");
            $sth->execute();
            $listeCat= $sth->fetchAll();
        }
        catch (PDOException $e)
        {
            print "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }
        echo <<<html
        <main>
            <h1><p>Ajout d'un produit</p></h1>
            <article>
                <p><strong>Les champs avec une astérisque (*) sont obligatoire</strong></p>
                <form action="ajout_produit.php" method="POST" enctype="multipart/form-data">
                    <label for =\"nomProd\">Nom du produit*</label>
        html;

        if(isset($_POST['nomP']))
        {
            echo "<input type=\"text\" name=\"nomP\" value=\"".$_POST['nomP']."\" required>";
        }
        else
        {
            echo "<input type=\"text\" name=\"nomP\" required>";        
        }
     
        echo "<label for =\"qteProd\">Quantité de conditionnement*</label>";
        if(isset($_POST['qteP']))
        {
            echo "<input type=\"number\" min=\"0\" name=\"qteP\" value=\"".$_POST['qteP']."\" required>";
        }
        else
        {
            echo "<input type=\"number\" min=\"0\" name=\"qteP\" required>";
        }

        echo "<label for =\"poidProd\">Poids</label>";
        if(isset($_POST['poidP']))
        {
            echo "<input type=\"number\" step=\"0.01\" min=\"0\" name=\"poidP\" value=\"".$_POST['poidP']."\">";
        }
        else
        {
            echo "<input type=\"number\" step=\"0.01\" min=\"0\" name=\"poidP\">";
        }

        echo "<label for =\"volumeProd\">Volume</label>";
        if(isset($_POST['volumeP']))
        {
            echo "<input type=\"number\" step=\"0.01\" min=\"0\" name=\"volumeP\" value=\"".$_POST['volumeP']."\" >";
        }
        else
        {
            echo "<input type=\"number\" step=\"0.01\" min=\"0\" name=\"volumeP\">";
        }

        echo "<label for =\"PrixProd\">Prix de vente TTC*</label>";
        if(isset($_POST['prixP']))
        {
            echo "<input type=\"number\" step=\"0.01\" name=\"prixP\" value=\"".$_POST['prixP']."\" required>";
        }
        else
        {
            echo "<input type=\"number\" step=\"0.01\" name=\"prixP\" required>";
        }

        echo "<label for =\"DescriptifProd\">Descriptif du produit*</label>";
        if(isset($_POST['descP']))
        {
            echo "<input type=\"text\"  name=\"descP\" value=\"".$_POST['descP']."\" required>";
        }
        else
        {
            echo "<input type=\"text\"  name=\"descP\" required>";
        }

        echo "<label for =\"stockProd\">Stock disponible*</label>";
        if(isset($_POST['stockP']))
        {
            echo "<input type=\"number\" min=\"0\" name=\"stockP\" value=\"".$_POST['stockP']."\" required>";
        }
        else
        {
            echo "<input type=\"number\" min=\"0\" name=\"stockP\" required>";
        }

        // IMAGE

        echo "<label for =\"imagesP\">Images du produit (1 minimum)*</label>";
            echo "<input type=\"file\" multiple name=\"imagesP[]\" accept=\"image/*\">";

        //affichage de la liste des catégorie
        echo '<label for ="categProd"> choisissez la catégorie du produit </label>'.PHP_EOL;
        echo '<select name="catP" id="select" required>'.PHP_EOL;
        foreach($listeCat as $nameCat)
        {
            echo '<option value="'.$nameCat['nom'].'">'.$nameCat['nom'].'</option>'.PHP_EOL;
        }
        echo '    <option value="autre">Créer une nouvelle catégorie</option>';
        echo '</select>'.PHP_EOL;
        echo <<<html
                    <input type="submit" value="Ajouter le produit" id="submit">
                </form>
            </article>
        </main>
        html;
        if($_GET['msg']==1) // OK
        {
            echo '
            <div class="notification_ok">
                Le produit a été ajouté avec succès
            </div>
            ';
        }
        else if($_GET['msg']==2) // pas d'image
        {
            echo '
            <div class="notification_non">
                Il faut minimum une image par produit
            </div>
            ';
        }
        else if($_GET['msg']==3) // trop volumineux
        {
            echo '
            <div class="notification_non">
                Un des fichiers des trop volumineux (15Mo max)
            </div>
            ';
        }
    ?>
    <script>

        // Def

        var select = document.getElementById("select");
        var form = document.getElementsByTagName("form")[0];
        var autre = document.createElement("input");
        var autreLabel = document.createElement("label");
        var submit = document.getElementById("submit");
        autre.type = "text";
        autre.name = "nomNewCat";
        autre.id = "autre";
        autreLabel.for = "nomNewCat";
        autreLabel.textContent = "Nom de la nouvelle catégorie*";

        // function

        function disableSubmit()
        {
            submit.disabled = true;
            submit.style = "background-color : grey;"
        }

        function activateSubmit()
        {
            submit.disabled = false;
            submit.style = "background-color : #FFDC60;"
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
                insertAfter(autreLabel, select);
            }
            else
            {
                if(autre.parentNode == form)
                {
                    form.removeChild(autreLabel);
                    form.removeChild(autre);
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
</body>
</html>  