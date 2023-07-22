<!--Question secrête et réponse-->
<div class="form"  style="margin-top:50px;">
    <div class="mb-3" id="cases_connex">
        <!--Les question sont proposés dans une liste déroulante-->
        <select id="quest" name="questF" class="form-select form-select-lg mb-3" placeholder="Sélectionnez votre question" required>
            <?php
                //Si l'utilisateur a déjà sélectionné sa question
                if(isset($_POST['questF'])){
                    //on lui reselectionne sa question
                    foreach($bdd->query("select id_quest,quest from alizonbdd._question;") as $quests){
                        if($quests['id_quest']==intval($_POST['questF'])){
                            echo "<option value=$quests[id_quest] selected>$quests[quest]</option>";
                        }
                    }
                    foreach($bdd->query("select id_quest,quest from alizonbdd._question;") as $quests){
                        if($quests['id_quest']!=intval($_POST['questF'])){
                            echo "<option value=$quests[id_quest] >".$quests['quest']."</option>";
                        }
                    }
                    //sinon on affiche la version non selectionnée
                }else{
                    echo"<option selected value=\"false\">--Question secrète--</option>";
                    foreach($bdd->query("select id_quest,quest from alizonbdd._question;") as $quests){
                        echo "<option value=\"$quests[id_quest]\">$quests[quest]</option>";
                    }
                }
            ?>
        </select>
        <?php
            //si il n'a pas selectionné de question, on lui affiche ce message
            if($_POST['questF']=='false'){
                echo "<label style=\"color: red;\">Veuillez sélectionner une question</label>";
            }
        ?>
        <span id="questErr"></span>
    </div>
    
        <?php
            //s'il a déjà donné sa réponse
            if(isset($_POST['repF'])){
                //on lui prérempli
                echo"<div class=\"form-floating mb-3\" id=\"cases_connex\">";
                echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"rep\" name=\"repF\" placeholder=\"Votre réponse\" value=\"". $_POST['repF']."\" required>";
                echo "<label for=rep>Votre réponse</label>";
            }else{
                //sinon le champs reste vide
                echo"<div class=\"form-floating mb-3\" id=\"cases_connex\">";
                echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"rep\" name=\"repF\" placeholder=\"Votre réponse\" required>";
                echo "<label for=rep>Votre réponse</label>";
            }
        ?>
        <span id="repErr"></span>
    </div>
</div>