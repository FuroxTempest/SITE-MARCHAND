<?php
//réception du message d'erreur mail déjà utilisé
    if(!isset($_POST['mailD'])){
        if(isset($_POST['emailF']) && isset($_POST['nomF']) && isset($_POST['prenomF']) && isset($_POST['telF']) && isset($_POST['rueF']) && isset($_POST['codeF']) && isset($_POST['villeF']) && isset($_POST['pswF']) && ($_POST['pswF']==$_POST['pswFConf']) && isset($_POST['questF']) && ($_POST['questF']!='false') && isset($_POST['repF'])){
            echo "<form action=\"./verif_crea_cli.php\" method=\"POST\" name=\"form_cli\">

                <input type=hidden name=\"nomF\" value=\"" . $_POST['nomF'] . "\">
                <input type=hidden name=\"prenomF\" value=\"" . $_POST['prenomF'] . "\">
                <input type=hidden name=\"telF\" value=\"" . $_POST['telF'] . "\">
                <input type=hidden name=\"rueF\" value=\"" . $_POST['rueF'] . "\">
                <input type=hidden name=\"codeF\" value=\"" . $_POST['codeF'] . "\">
                <input type=hidden name=\"villeF\" value=\"" . $_POST['villeF'] . "\">
                <input type=hidden name=\"emailF\" value=\"" . $_POST['emailF'] . "\">
                <input type=hidden name=\"pswF\" value=\"" . $_POST['pswF'] . "\">
                <input type=hidden name=\"questF\" value=\"" . $_POST['questF'] . "\">
                <input type=hidden name=\"repF\" value=\"" . $_POST['repF'] . "\">
            </form>
            <script type=\"text/javascript\">
                document.forms[\"form_cli\"].submit();
            </script>";
        }
    }
?>