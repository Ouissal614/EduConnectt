<?php
session_start();
require_once("../config/db.php");
require_once("../utils/logger.php");

$message = "";

/* =========================
   CONFIG SECURITE
========================= */
$MAX_ATTEMPTS = 5;
$BLOCK_TIME = 120; // 2 minutes

/* =========================
   INIT SESSION
========================= */
if (!isset($_SESSION['admin_attempts'])) {
    $_SESSION['admin_attempts'] = 0;
}

if (!isset($_SESSION['admin_block_start'])) {
    $_SESSION['admin_block_start'] = 0;
}

/* =========================
   CHECK BLOCK
========================= */
$isBlocked = false;
$remaining = 0;

if ($_SESSION['admin_attempts'] >= $MAX_ATTEMPTS) {

    $elapsed = time() - $_SESSION['admin_block_start'];

    if ($elapsed < $BLOCK_TIME) {
        $isBlocked = true;
        $isBlocked = false;
        $remaining = $BLOCK_TIME - $elapsed;
    } else {

    $_SESSION['admin_attempts'] = 0;
    $_SESSION['admin_block_start'] = 0;

    $isBlocked = false;
}
}

/* =========================
   LOGIN PROCESS
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['connexion'])) {

    if ($isBlocked) {

        $message = "Trop de tentatives. Réessayez dans " . $remaining . " secondes.";

    } else {

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = "SELECT * FROM administrateur WHERE email_admin = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['mot_de_passe_admin'])) {

            /* RESET */
            $_SESSION['admin_attempts'] = 0;
            $_SESSION['admin_block_start'] = 0;

            /* LOG SUCCESS */
            logLogin($pdo, "admin", $admin['id_admin'], $email, "success");

            $_SESSION['admin'] = $admin['id_admin'];

            header("Location: ../admin/dashboard_admin.php");
            exit();

        } else {

            /* LOG FAILED */
            logLogin($pdo, "admin", null, $email, "failed");

            $_SESSION['admin_attempts']++;

            if ($_SESSION['admin_attempts'] >= $MAX_ATTEMPTS) {

                $_SESSION['admin_block_start'] = time();
                $message = "Trop de tentatives. Blocage 2 minutes.";

            } else {
                $message = "Email ou mot de passe incorrect.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion Administrateur - EduConnect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary: #1e3c72;
      --primary-light: #2a5298;
      --bg-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1a3a6c 100%);
      --white: #ffffff;
      --gray-bg: #f1f5f9;
      --gray-border: #e2e8f0;
      --text-dark: #0f172a;
      --radius: 14px;
      --shadow: 0 20px 40px -10px rgba(0,0,0,0.25);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    body {
      background: var(--bg-gradient);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    body::before {
      content: "";
      position: fixed;
      width: 420px;
      height: 420px;
      background: rgba(255,255,255,0.10);
      border-radius: 50%;
      top: -120px;
      left: -120px;
    }

    body::after {
      content: "";
      position: fixed;
      width: 320px;
      height: 320px;
      background: rgba(255,255,255,0.08);
      border-radius: 50%;
      bottom: -100px;
      right: -80px;
    }

    .container {
      display: flex;
      max-width: 1100px;
      width: 100%;
      min-height: 650px;
      gap: 40px;
      z-index: 1;
      position: relative;
    }

    /* ===== PANNEAU GAUCHE ===== */
    .left-panel {
      flex: 1;
      padding: 60px 40px;
      color: var(--white);
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 25px;
    }

    .logo {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      padding: 1rem 1.2rem;
      border-bottom: 1px solid rgba(255,255,255,0.15);
      margin-bottom: 0.5rem;
      text-align: center;
    }

    .logo img {
      width: 120px;
      height: 120px;
      object-fit: contain;
    }

    .left-panel h1 {
      font-size: 2.4rem;
      line-height: 1.2;
      font-weight: 700;
      text-shadow: 0 4px 18px rgba(0,0,0,0.25);
    }

    .subtitle {
      color: rgba(255,255,255,0.85);
      line-height: 1.6;
      font-size: 1rem;
    }

    .badge {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(10px);
      padding: 16px;
      border-radius: 10px;
      font-size: 0.9rem;
      color: var(--white);
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .illustration {
      margin-top: 20px;
      opacity: 0.9;
    }

    .illustration svg {
      animation: float 3s ease-in-out infinite;
    }

    /* ===== PANNEAU DROIT ===== */
    .right-panel {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      background: var(--white);
      padding: 40px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      border: 1px solid rgba(255,255,255,0.45);
      backdrop-filter: blur(8px);
      animation: slideUp 0.5s ease-out;
      transition: 0.3s ease;
    }

    .login-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 28px 55px -15px rgba(0,0,0,0.35);
    }

    .login-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
      color: var(--text-dark);
    }

    .login-header h2 {
      font-size: 1.35rem;
      font-weight: 600;
    }

    .admin-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      color: white;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.82rem;
      font-weight: 600;
      margin-bottom: 24px;
    }

    .error-msg {
      background: #fff5f5;
      color: #c53030;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 1rem;
      border-left: 3px solid #fc8181;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      margin-bottom: 7px;
      font-weight: 600;
      color: var(--text-dark);
      font-size: 0.9rem;
    }

    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 1.5px solid var(--gray-border);
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.2s;
      background: #f8fafc;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(30,60,114,0.08);
    }

    .form-group input:hover {
      border-color: #b7c4d8;
    }

    .form-group input::placeholder {
      color: #94a3b8;
    }

    .password-wrapper {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      padding: 5px;
      display: flex;
      align-items: center;
      color: #94a3b8;
      transition: all 0.2s;
      border-radius: 6px;
    }

    .toggle-password:hover {
      color: var(--primary);
      background: rgba(30,60,114,0.05);
    }

    .btn-login {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 8px;
    }

    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(30,60,114,0.3);
    }

    .btn-login:active {
      transform: scale(0.98);
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 18px;
      font-size: 0.85rem;
      color: #64748b;
      text-decoration: none;
      transition: color 0.2s;
    }

    .back-link:hover {
      color: var(--primary);
    }

    /* ===== ANIMATIONS ===== */
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 850px) {
      .container {
        flex-direction: column;
        gap: 0;
      }
      .left-panel {
        padding: 40px 20px 20px;
        text-align: center;
        align-items: center;
      }
      .illustration { display: none; }
      .right-panel { padding: 20px; }
      .login-card { padding: 30px 25px; }
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Panneau gauche -->
    <div class="left-panel">
      <div class="logo">
        <img src="logo.png" alt="EduConnect Logo">
      </div>

      <h1>Espace<br>Administrateur</h1>
      <p class="subtitle">Gérez les élèves, les parents, les notes et les notifications depuis votre tableau de bord.</p>

      <div class="badge">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <span>Accès réservé aux administrateurs autorisés de l'établissement</span>
      </div>

      <div class="illustration">
        <svg width="260" height="180" viewBox="0 0 260 180" fill="none">
          <rect x="20" y="30" width="140" height="90" rx="8" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
          <circle cx="60" cy="65" r="12" fill="rgba(255,255,255,0.8)"/>
          <rect x="40" y="85" width="40" height="5" rx="2.5" fill="rgba(255,255,255,0.6)"/>
          <rect x="40" y="95" width="30" height="5" rx="2.5" fill="rgba(255,255,255,0.4)"/>
          <rect x="75" y="120" width="30" height="8" fill="rgba(255,255,255,0.3)"/>
          <rect x="170" y="50" width="60" height="100" rx="12" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
          <circle cx="200" cy="75" r="10" fill="rgba(255,255,255,0.8)"/>
          <rect x="185" y="95" width="30" height="4" rx="2" fill="rgba(255,255,255,0.6)"/>
          <rect x="185" y="105" width="25" height="4" rx="2" fill="rgba(255,255,255,0.4)"/>
        </svg>
      </div>
    </div>

    <!-- Panneau droit -->
    <div class="right-panel">
      <div class="login-card">

        <div class="login-header">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3c72" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <h2>Connexion Administrateur</h2>
        </div>

        <div class="admin-badge">
          <i class="fa fa-user-shield"></i> Espace Admin
        </div>

        <?php if (!empty($message)): ?>
          <div class="error-msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   placeholder="Saisissez votre email" required>
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="password-wrapper">
              <input type="password" id="password" name="password"
                     placeholder="Saisissez votre mot de passe" required>
              <button type="button" class="toggle-password" onclick="togglePassword()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <!-- Déclenche isset($_POST['connexion']) dans le back -->
          <button type="submit" name="connexion" class="btn-login">
            <i class="fa fa-sign-in-alt"></i> Se connecter
          </button>

        </form>

        <a href="../index.php" class="back-link">← Retour à l'accueil</a>

      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const pwd = document.getElementById("password");
      pwd.type = pwd.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>