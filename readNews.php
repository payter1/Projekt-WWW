<?php
require_once 'connect.php';
require_once 'functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id === 0) {
    header("Location: news.php");
    exit;
}

// Pobranie danych wpisu wraz z autorem
$task = "SELECT n.*, u.fname, u.lname FROM news n JOIN users u ON n.author_id = u.id WHERE n.id = $id";
$query = mysqli_query($conn, $task);

if(!$query || mysqli_num_rows($query) === 0) {
    header("Location: news.php");
    exit;
}

$n = mysqli_fetch_assoc($query);
$imgSrc = !empty($n['image_url']) ? $n['image_url'] : 'img/default_news.png';
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4 p-md-5">
        
        <div class="row mb-4">
            <div class="col-12">
                <a href="news.php" class="btn btn-outline-secondary btn-sm mb-3">&laquo; Wróć do aktualności</a>
                
                <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] != 0){ ?>
                    <a href="editNews.php?id=<?= $n['id'] ?>" class="btn btn-warning btn-sm mb-3 ms-2">Edytuj ten wpis</a>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <header class="mb-4 text-center">
                    <h1 class="fw-bold text-primary mb-3"><?= htmlspecialchars($n['title']) ?></h1>
                    <div class="text-muted small">
                        <i class="bi bi-calendar3"></i> Opublikowano: <?= date('d.m.Y', strtotime($n['created_at'])) ?> | 
                        <i class="bi bi-person-fill"></i> Autor: <?= htmlspecialchars($n['fname'] . ' ' . $n['lname']) ?>
                    </div>
                </header>

                <div class="ratio ratio-21x9 bg-secondary-subtle rounded-4 overflow-hidden mb-5 shadow-sm">
                    <img src="<?= htmlspecialchars($imgSrc) ?>" class="object-fit-cover w-100 h-100" alt="Zdjęcie do artykułu">
                </div>

                <article class="fs-5 text-body-secondary lh-lg" style="text-align: justify;">
                    <?= nl2br(htmlspecialchars($n['content'])) ?>
                </article>
                
                <hr class="my-5">
                <div class="text-center">
                    <h5 class="fw-bold text-muted mb-3">Podziel się dobrą nowiną!</h5>
                    <button class="btn btn-outline-primary shadow-sm" onclick="navigator.clipboard.writeText(window.location.href); alert('Skopiowano link do schowka!');">
                        <i class="bi bi-share"></i> Skopiuj link
                    </button>
                </div>
            </div>
        </div>

    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>