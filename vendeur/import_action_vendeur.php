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
    <title>Importer un catalogue de produit</title>
</head>
<body>
    <?php
        include("./head_vendeur.php");
    ?>
    <main>
        <h1><p>Importation d'un catalogue de produit</p></h1>
        <article>
            <p>Ici, vous pouvez déposer votre fichier <strong>.csv</strong> pour ajouter un catalogue de produit</p>
            <p>Les catégories seront créé <strong>automatiquement avec une TVA de 20%</strong></p>
            <form action="import_produit.php" method="POST" enctype="multipart/form-data">
                <label for="fichier">Sélectionnez votre fichier .csv</label></br>
                <input type="file" name="fichier"/><br/>
                <input type="submit" value="Valider"/>
            </form>
            <?php
               if(isset($_GET['msg']))
               {
                   $msg = $_GET['msg'];
                   if($msg==1)
                   {
                       echo '
                        <div class="notification_ok">
                            Le catalogue a été ajouté avec succès!
                        </div>
                        ';
                   }
                   else if($msg==2)
                   {
                       echo '
                        <div class="notification_non">
                            Le fichier n\'est pas un .csv!
                        </div>
                        ';
                   }
                   else if($msg==3)
                   {
                       echo '
                        <div class="notification_non">
                            Aucun fichier sélectionné!
                        </div>
                        ';
                   }
               }
            ?>
        </article>
    </main>
</body>
</html>