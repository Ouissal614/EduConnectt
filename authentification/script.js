document.addEventListener("DOMContentLoaded", () => {
  const roleButtons = document.querySelectorAll(".role-btn");
  const identifiantInput = document.getElementById("identifiant");
  const loginForm = document.getElementById("loginForm");
  const labelIdentifiant = document.getElementById("label-identifiant");

  roleButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      roleButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const role = btn.dataset.role;

      if (role === "parent") {
        // Champ login pour le back login_parent.php
        identifiantInput.name = "login";
        identifiantInput.type = "text";
        identifiantInput.placeholder = "Login";
        labelIdentifiant.textContent = "Identifiant";
        loginForm.action = "authentification/login_parent.php";
      } else {
        // Champ email pour le back login_admin.php
        identifiantInput.name = "email";
        identifiantInput.type = "email";
        identifiantInput.placeholder = "Email";
        labelIdentifiant.textContent = "Email";
        loginForm.action = "authentification/login_admin.php";
      }
    });
  });

  // Toggle affichage mot de passe
  const toggleBtn = document.querySelector(".toggle-password");
  const passwordInput = document.getElementById("password");

  toggleBtn.addEventListener("click", () => {
    passwordInput.type = passwordInput.type === "password" ? "text" : "password";
  });

  // Validation avant soumission
  loginForm.addEventListener("submit", (e) => {
    const identifiant = identifiantInput.value.trim();
    const password = passwordInput.value.trim();
    if (!identifiant || !password) {
      e.preventDefault();
      alert("Veuillez remplir tous les champs.");
    }
  });
});