<?php
require_once 'connect.php';
require_once 'functions.php';

// Pobieranie aktualności z bazy danych (od najnowszych)
$task = "SELECT n.id, n.title, n.content, n.image_url, n.created_at, u.fname, u.lname 
         FROM news n
         JOIN users u ON n.author_id = u.id
         ORDER BY n.created_at DESC";
$query = $conn->query($task);
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        
        <div class="row mb-5 text-center">
            <div class="col">
                <h1 class="text-primary fw-bold">Aktualności</h1>
                <p class="text-muted">Dowiedz się, co słychać w naszej stajni i sprawdź najnowsze udane adopcje!</p>
                
                <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] != 0){ ?>
                <div class="mt-3 mb-4">
                    <a href="addNews.php" class="btn btn-success shadow-sm px-4 fw-bold">
                        Dodaj post
                    </a>
                </div>
                <?php } ?>
                
                <hr class="w-25 mx-auto">
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if($query && $query->num_rows > 0){ 
                while($row = $query->fetch_assoc()){ 
                    // Zdjęcie domyślne, jeśli do posta nie dodano obrazka
                    $imgSrc = !empty($row['image_url']) ? $row['image_url'] : 'img/default_news.png';
            ?>
            <div class="col">
                <article class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9 bg-secondary-subtle">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="object-fit-cover w-100 h-100" alt="Zdjęcie do artykułu">
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-3"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1" style="text-align: justify;">
                            <?= nl2br(htmlspecialchars(mb_strimwidth($row['content'], 0, 150, "..."))) ?>
                        </p>
                        <div class="mt-3 border-top pt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($row['created_at'])) ?><br>
                                <i class="bi bi-person-fill"></i> <?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?>
                            </small>
                            <div>
                                <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] != 0){ ?>
                                    <a href="editNews.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm border-0"><i class="bi bi-pencil-fill"></i></a>
                                <?php } ?>
                                <a href="readNews.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">Czytaj dalej</a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <?php } } else { ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Brak aktualności.</h4>
                <p>Nasi wolontariusze pracują w stajni. Wkrótce dodamy nowe informacje!</p>
            </div>
            <?php } ?>
        </div>

    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>