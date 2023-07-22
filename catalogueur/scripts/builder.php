
    <?php

        $data_file = "../samples/exemple.json";
        $json_str=file_get_contents($data_file);
        $data = json_decode($json_str, true, 512, JSON_HEX_TAG);
        

        $nombre_produit_par_page = 3;
        $nombre_produit_par_page_vendeur = 1; //nombre de produit pouvant être affiché sur la même page que la présentation d'un vendeur

        $nombre_vendeur = 0;
        $multi_vendeur = false;

        foreach($data as $vendeur){
            $nombre_vendeur ++;
        }

        if($nombre_vendeur>1){
            $multi_vendeur = true;
        }
        ob_start();
    ?>


<html>
    <head>
            <link rel="stylesheet" href="builder.css">
    </head>
    <body>
        <div>
            <header>
                <img src="http://localhost:8080/images/logo_alizon.png">
                <h1>
                    Catalogue des produits
                </h1>
                <br>



    <?php
        if($multi_vendeur){
    ?>



                <h2>
                    Multi-vendeur
                </h2>
                <br>



    <?php

            foreach($data as $vendeur){
                echo "<br><h2> $vendeur[nom] </nom>";
            }
        }
    ?>


            </header>
        </div>



    <?php
        $page_produit = false;
        foreach($data as $vendeur){
            $page_vendeur = true;
            $nombre_produit_sur_page=0;
    ?>



        <div>
            <section class="vendeur">
                
                <article>

                    <?php
                        echo "<img src=\"$vendeur[logo]\">";
                    
                        echo "<h2>$vendeur[nom]</h2>";
                    ?>
                </article>
    
                <article>
                    <?php
                        echo "<p>Raison sociale : $vendeur[raison_sociale]</p>";
                        echo "<p>Numéro de siret : $vendeur[siret]</p>";
                        echo "<p>Numéro de TVA communautaire : $vendeur[numero_TVA]</p>";
                        echo "<p>Adresse : $vendeur[adresse]</p>";
                        echo "<p>Contact : $vendeur[contact]</p>";
                        echo "<p>Note : $vendeur[note]</p>";
                    ?>
                </article>
    
                <article>
                    <p>
                        Description : description
                    </p>
                </article>
            </section>


            <?php
                foreach($vendeur['produits'] as $produits){
                    if($page_vendeur){
                        while($nombre_produit_sur_page<$nombre_produit_par_page_vendeur){
                            $nombre_produit_sur_page+=1;
            ?>



            <section class="produit">
                <?php
                    echo "<img src=\"$produits[image]\">";
                
                    echo "<article>";
                    echo "    <h3>$produits[nom_produit]</h3>";
                    echo "    <p>Prix hors taxe : $produits[prixHT] €</p>";
                    echo "    <p>Prix toutes taxes comprises : $produits[prixTTC] €</p>";
                    echo "</article> "; 
                    
                    echo "<article>";
                    echo "    <p>Description : $produits[description] </p>";
                    echo "</article>";
                ?>
            </section>

            <?php
                        }
                        $nombre_produit_sur_page=0;
                        $page_vendeur=false;
            ?>
        </div>



            <?php
                    }else if(!$page_vendeur && !$page_produit){
                        $page_produit=true;
                        $nombre_produit_sur_page+=1;
            ?>

                        <div>
                            <section class="produit">
                            <?php
                                echo "<img src=\"$produits[image]\">";
                            
                                echo "<article>";
                                echo "    <h3>$produits[nom_produit]</h3>";
                                echo "    <p>Prix hors taxe : $produits[prixHT] €</p>";
                                echo "    <p>Prix toutes taxes comprises : $produits[prixTTC] €</p>";
                                echo "</article> "; 
                                
                                echo "<article>";
                                echo "    <p>Description : $produits[description] </p>";
                                echo "</article>";
                            ?>
                            </section>


            <?php
                    }else{
                        while($nombre_produit_sur_page<$nombre_produit_par_page){
                            $nombre_produit_sur_page+=1;
            ?>
                            <section class="produit">
                            <?php
                                echo "<img src=\"$produits[image]\">";
                            
                                echo "<article>";
                                echo "    <h3>$produits[nom_produit]</h3>";
                                echo "    <p>Prix hors taxe : $produits[prixHT] €</p>";
                                echo "    <p>Prix toutes taxes comprises : $produits[prixTTC] €</p>";
                                echo "</article> "; 
                                
                                echo "<article>";
                                echo "    <p>Description : $produits[description] </p>";
                                echo "</article>";
                            ?>
                            </section>
            <?php
                        }
                        $nombre_produit_sur_page=0;
                        $page_produit = false;

                        ?>

                    </div>

                        <?php
                    }
                }
            ?>

    <?php
        }
    ?>

</html>

    <?php
        file_put_contents('catalogue.html', ob_get_contents());

        ob_end_flush();
    ?>
