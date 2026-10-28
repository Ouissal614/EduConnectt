<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EduConnect</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0b3d91,#1f5fd1);
    overflow:hidden;
}

.loading-container{
    text-align:center;
    color:white;
    animation:fadeIn 1.2s ease;
}

.loading-container img{
    width:180px;
    margin-bottom:25px;
    animation:float 2s infinite ease-in-out;
}

.loading-container h1{
    font-size:42px;
    margin-bottom:10px;
}

.loading-container p{
    font-size:18px;
    opacity:0.9;
}

.loader{
    margin:25px auto 0;
    width:55px;
    height:55px;
    border:5px solid rgba(255,255,255,0.3);
    border-top:5px solid white;
    border-radius:50%;
    animation:spin 1s linear infinite;
}

/* =========================
   MEDIA QUERY (MOBILE)
========================= */
@media (max-width: 768px) {

    .loading-container img {
        width: 120px;
    }

    .loading-container h1 {
        font-size: 28px;
    }

    .loading-container p {
        font-size: 14px;
    }

    .loader {
        width: 40px;
        height: 40px;
        border-width: 4px;
    }
}

/* =========================
   ANIMATIONS
========================= */

@keyframes spin{
    100%{
        transform:rotate(360deg);
    }
}

@keyframes float{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-10px);
    }
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
</head>

<body>

<div class="loading-container">

    <img src="authentification/logo.png" alt="EduConnect Logo">

    <h1>EduConnect</h1>

    <p>Veuillez patienter...</p>

    <div class="loader"></div>

</div>

<script>

setTimeout(() => {
    window.location.href = "index.php";
}, 5000);

</script>

</body>
</html>