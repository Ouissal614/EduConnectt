<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduConnect</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #0b3d91, #1f5fd1);
      overflow: hidden;
    }

    .container {
      text-align: center;
      color: white;
      animation: fadeIn 1.2s ease;
    }

    .container img {
      width: 180px;
      margin-bottom: 25px;
      animation: float 2s infinite ease-in-out;
    }

    .container h1 {
      font-size: 42px;
      margin-bottom: 10px;
    }

    .container p {
      font-size: 18px;
      opacity: 0.9;
      margin-bottom: 40px;
    }

    .btn-group {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-choice {
      display: inline-block;
      padding: 14px 32px;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .btn-parent {
      background: white;
      color: #0b3d91;
      border: 2px solid white;
    }

    .btn-parent:hover {
      background: transparent;
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .btn-admin {
      background: transparent;
      color: white;
      border: 2px solid white;
    }

    .btn-admin:hover {
      background: white;
      color: #0b3d91;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* =========================
       MEDIA QUERY (MOBILE)
    ========================= */
    @media (max-width: 768px) {

      .container img {
        width: 120px;
      }

      .container h1 {
        font-size: 28px;
      }

      .container p {
        font-size: 14px;
        margin-bottom: 25px;
      }

      .btn-group {
        flex-direction: column;
        gap: 15px;
      }

      .btn-choice {
        width: 220px;
        font-size: 14px;
        padding: 12px 20px;
      }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Cercles décoratifs */
    body::before {
      content: "";
      position: fixed;
      width: 420px;
      height: 420px;
      background: rgba(255,255,255,0.08);
      border-radius: 50%;
      top: -120px;
      left: -120px;
    }

    body::after {
      content: "";
      position: fixed;
      width: 320px;
      height: 320px;
      background: rgba(255,255,255,0.06);
      border-radius: 50%;
      bottom: -100px;
      right: -80px;
    }
    

  </style>
</head>

<body>
  <div class="container">
    <img src="authentification/logo.png" alt="EduConnect Logo">
    <h1>EduConnect</h1>
    <p>Choisissez votre espace :</p>

    <div class="btn-group">
      <a href="authentification/login_parent.php" class="btn-choice btn-parent">
        👤 Connexion Parent
      </a>
      <a href="authentification/login_admin.php" class="btn-choice btn-admin">
        🛡️ Connexion Administrateur
      </a>
    </div>
  </div>
</body>
</html>