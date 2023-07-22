<script>
  let passwordInput = document.getElementById("password-input");
  let passwordToggle = document.getElementById("password-toggle");

  passwordToggle.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      passwordToggle.src = "../images/les-yeux-croises.png";
    } else {
      passwordInput.type = "password";

      passwordToggle.src = "../images/oeil.png";
    }
  });
</script>