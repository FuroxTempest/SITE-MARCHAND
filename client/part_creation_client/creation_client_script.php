<script>
    const nom = document.getElementById("nom");
    const nomErr = document.getElementById("nomErr");

    const prenom = document.getElementById("prenom");
    const prenomErr = document.getElementById("prenomErr");

    const email = document.getElementById("email");
    const emailErr = document.getElementById("emailErr");

    const tel = document.getElementById("tel");
    const telErr = document.getElementById("telErr");

    const rue = document.getElementById("rue");
    const code = document.getElementById("code");
    const ville = document.getElementById("ville");
    const rueErr = document.getElementById("rueErr");
    const codeErr = document.getElementById("codeErr");
    const villeErr = document.getElementById("villeErr");

    const psw = document.getElementById("psw");
    const pswConf = document.getElementById("pswConf");
    const pswErr = document.getElementById("pswErr");
    const pswConfErr = document.getElementById("pswConfErr");
    const pswMajErr = document.getElementById("pswMajErr");
    const pswMinErr = document.getElementById("pswMinErr");
    const pswNumErr = document.getElementById("pswNumErr");
    const pswSpeErr = document.getElementById("pswSpeErr");

    const quest = document.getElementById("quest");
    const rep = document.getElementById("rep");
    const questErr = document.getElementById("questErr");
    const repErr = document.getElementById("repErr");

    email.addEventListener("input", (event) => {
        if (email.validity.valid) {
            emailErr.textContent = "";
            email.style.border = "solid black 1px";
            email.classList.remove('focusErr');
            enableButton();
        } else  if(email.value === ""){
            emailErr.textContent = "Il faut saisir une adresse";
            email.style.border = "solid red 1px";
            email.classList.add('focusErr');
            disableButton();
            email.invalid= "true";
        }else if(email.value.search(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/i)===-1 ){
            emailErr.textContent = "L'adresse est invalide";
            email.style.border = "solid red 1px";
            email.classList.add('focusErr');
            disableButton();
            email.invalid= "true";
        }
    });

    psw.addEventListener("input", (event) => {
        if(psw.value === ""){
            psw.style.border = "solid red 1px";
            psw.classList.add('focusErr');
            disableButton();
            pswErr.textContent = "Le mot de passe est obligatoire";

            pswMajErr.textContent = "";
            pswMinErr.textContent = "";
            pswNumErr.textContent = "";
            pswSpeErr.textContent = "";

        }else{
            psw.style.border = "solid black 1px";
            psw.classList.remove('focusErr');
            enableButton();
            pswErr.textContent = "";
                pswSpeErr.textContent = "";
                pswNumErr.textContent = "";
                pswMinErr.textContent = "";
                pswMajErr.textContent = "";
        }
    });

    pswConf.addEventListener("input", (event) => {
        if(pswConf.value !== psw.value){
            pswConf.style.border = "solid red 1px";
            pswConf.classList.add('focusErr');
            disableButton()
            pswConfErr.textContent = "Les mots de passe ne sont pas identiques";
        }else if (pswConf.validity.valid) {
            pswConf.style.border = "solid black 1px";
            pswConf.classList.remove('focusErr');
            enableButton();
            pswConfErr.textContent = "";
        }else  if(pswConf.value === ""){
            pswConf.style.border = "solid red 1px";
            pswConf.classList.add('focusErr');
            disableButton();
            pswConfErr.textContent = "Le mot de passe de confirmation est obligatoire";
        }
    });

    prenom.addEventListener("input", (event) => {
        if (prenom.validity.valid) {
            prenom.style.border = "solid black 1px";
            prenom.classList.remove('focusErr');
            enableButton();
            prenomErr.textContent = "";
        }else  if(prenom.value === ""){
            prenom.style.border = "solid red 1px";
            prenom.classList.add('focusErr');
            disableButton();
            prenomErr.textContent = "Le prenom est obligatoire";
        }
    });

    nom.addEventListener("input", (event) => {
        if (nom.validity.valid) {
            nom.style.border = "solid black 1px";
            nom.classList.remove('focusErr');
            enableButton();
            nomErr.textContent = "";
        }else  if(nom.value === ""){
            nom.style.border = "solid red 1px";
            nom.classList.add('focusErr');
            disableButton();
            nomErr.textContent = "Le nom est obligatoire";
        } 
    });

    tel.addEventListener("input", (event) => {
        if (tel.validity.valid) {
            tel.style.border = "solid black 1px";
            tel.classList.remove('focusErr');
            enableButton();
            telErr.textContent = "";
        }else  if(tel.value === ""){
            tel.style.border = "solid red 1px";
            tel.classList.add('focusErr');
            disableButton();
            telErr.textContent = "Le numéro de télephonne est obligatoire";
        } else  if(tel.value.search(/^\+(?:[0-9]?){6,14}[0-9]$/i)===-1 ||  tel.value.search(/(0|\\+33|0033)[1-9][0-9]{8}/i)===-1){
            tel.style.border = "solid red 1px";
            tel.classList.add('focusErr');
            disableButton();
            telErr.textContent = "Le numéro de télephonne est invalide";
        }
    });

    rue.addEventListener("input", (event) => {
        if (rue.validity.valid) {
            rue.style.border = "solid black 1px";
            rue.classList.remove('focusErr');
            enableButton();
            rueErr.textContent = "";
        }else  if(rue.value === ""){
            rue.style.border = "solid red 1px";
            rue.classList.add('focusErr');
            disableButton();
            rueErr.textContent = "L'adresse est obligatoire";
        }
    });

    code.addEventListener("input", (event) => {
        if (code.validity.valid) {
            code.style.border = "solid black 1px";
            code.classList.remove('focusErr');
            enableButton();
            codeErr.textContent = "";
        }else  if(code.value === ""){
            code.style.border = "solid red 1px";
            code.classList.add('focusErr');
            disableButton();
            codeErr.textContent = "Le code postal est obligatoire";
        } 
    });

    ville.addEventListener("input", (event) => {
        if (ville.validity.valid) {
            ville.style.border = "solid black 1px";
            ville.classList.remove('focusErr');
            enableButton();
            villeErr.textContent = "";
        }else  if(code.value === ""){
            ville.style.border = "solid red 1px";
            ville.classList.add('focusErr');
            disableButton();
            villeErr.textContent = "Le code postal est obligatoire";
        } 
    });

    quest.addEventListener("input", (event) => {
        if (quest.options[quest.selectedIndex].text === "--Question secrète--"){
            quest.style.border = "solid red 1px";
            quest.classList.add('focusErr');
            disableButton();
            questErr.textContent = "La question secrète est obligatoire";
        } else{
            quest.style.border = "solid black 1px";
            quest.classList.remove('focusErr');
            enableButton();
            questErr.textContent = "";
        }
    });

    rep.addEventListener("input", (event) => {
        if (rep.validity.valid) {
            rep.style.border = "solid black 1px";
            rep.classList.remove('focusErr');
            enableButton();
            repErr.textContent = "";
        }else  if(rep.value === ""){
            rep.style.border = "solid red 1px";
            rep.classList.add('focusErr');
            disableButton();
            repErr.textContent = "La réponse secrète est obligatoire";
        } 
    });

    //------------------------------------------FONCTIONS---------------------------------------------


    function enableButton(){
        if(email.validity.valid && nom.validity.valid && prenom.validity.valid && tel.validity.valid && rue.validity.valid && code.validity.valid && ville.validity.valid && psw.validity.valid && pswConf.validity.valid && quest.validity.valid && rep.validity.valid){
            document.getElementById("inscr").disabled = false;
        }
    }

    function disableButton(){
        if(!email.validity.valid || !nom.validity.valid || !prenom.validity.valid || !tel.validity.valid || !rue.validity.valid || !code.validity.valid || !ville.validity.valid || !psw.validity.valid || !pswConf.validity.valid || !quest.validity.valid || !rep.validity.valid){
            document.getElementById("inscr").disabled = true;
        }
    }
</script>