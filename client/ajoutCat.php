<html>
    <head>
        <meta name="viewport"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <style>
            div.cadre {
                margin-top: 5em;
                margin-left: 25em;
                margin-right: 25em;
                background-color: white;
                border:solid;
                border-color: white;
                border-radius: 5px;
                box-shadow: 4px 4px 4px #aba496;
            }  

            form > p.ajout{
            text-align: center;
            padding-top: 1em;
            }
            
            div.form-group {
            margin: 2em;
            padding-left: 1em;
            padding-right: 1em;
            }

            div.bouton{
                display: flex;
                justify-content:center;
            }

            div.bouton > input.buton {
            display: flex;
            justify-content:center;
            width: 40%;
            background-color: #A99ED9;
            }

            @media (max-width: 1100px){
                body{
                    background-color: #d1cbc9;
                }

                div.cadre  {
                margin: 7em;
                background-color: white;
                border:solid;
                border-color: #A99ED9;
                border-radius: 5px;
                }

                div.bouton{
                    display: flex;
                    justify-content:space-around;
                }

                div.bouton > input.buton {
                display: flex;
                justify-content:center;
                width:80%;
                background-color: #A99ED9;
                }

            }
        </style>
    </head>

    <body>
    <?php

        if(isset($_POST['nomC']) && isset($_POST['tvaC'])){
            $nomC=$_POST['nomC'];
            $tvaC=$_POST['tvaC'];
    

            $req= $bdd->prepare("INSERT INTO alizonbdd._categorie(nom,nb_art_cat,tva) VALUES (?,0,?);"); 
            $catP=$req->execute([$nomC,$tvaC]);

            unset($_POST['nomC']);
            unset($_POST['tvaC']);

            echo 'catégorie créer';
            header("Location: ajoutCat.php");
            
        }else{
            echo <<<HTML
                <div class="cadre">
                    <form  action="ajoutCat.php" method="POST">
                        <p class="ajout">Ajout d'une catégorie</p>

                        <div class="form-group">
                            <label for ="nomProd" class="form-label"> Entrez le nom de la catégorie</label>
                            <input type="text" class="form-control" name="nomC" required>
                        </div>
                        
                        <div class="form-group">
                            <label for ="qteProd" class="form-label">Entrez le taux de tva appliqué dans cette catégorie</label>
                            <input type="number" min="0" class="form-control" name="tvaC" required>
                        </div>

                        <div class="bouton">
                            <input class="buton" type="submit" value="Ajouter la catégorie">
                        </div>
                    </form>
                </div>
            HTML;
        }
    ?>
    </body>
</html>