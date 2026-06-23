<?php
require_once 'connect.php';
require_once 'functions.php';

if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['userLVL'] == 0){
    header("Location: index.php");
    exit;
}

$resultMsg = '';
$success = false;

if(isset($_POST['add_news'])){
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    
    $author_id = (int)$_SESSION['userID']; 


    $img = saveFile('img', 'png, jpg, webp, gif', 10, "img/news");
    if ($img[0] == 1) {
        // profile = 1 dla zdjęcia profilowego
        $image_url = $img[1];
    } else {
        $image_url = "img/news/default.png";
    }

    if(empty($title) || empty($content)){
        $resultMsg = "Tytuł i treść wpisu są wymagane!";
    } else {
        $task = "INSERT INTO news (title, content, image_url, author_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($task);
        
        if($stmt){
            $stmt->bind_param("sssi", $title, $content, $image_url, $author_id);
            if($stmt->execute()){
                $success = true;
                $resultMsg = "Aktualność została pomyślnie dodana i jest już widoczna na stronie!";
            } else {
                $resultMsg = "Wystąpił błąd podczas zapisu do bazy: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $resultMsg = "Błąd przygotowania zapytania SQL.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <!-- Panel nawigacyjny -->
    <?php require_once("partials/nav.php")?>
    
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        <div class="row g-4 z-2">
            
            <!-- Boczny blok -->
            <aside class="col-lg-3">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px;">
                    <h5 class="text-center fw-bold">Zarządzanie</h5>
                    <hr>
                    <p class="small text-muted mb-3">Dodaj nowy wpis, aby poinformować o:</p>
                    <ul class="small text-muted ps-3">
                        <li>Udanych adopcjach</li>
                        <li>Nowych podopiecznych w stajni</li>
                        <li>Wydarzeniach z życia fundacji</li>
                    </ul>
                    <div class="d-grid mt-4">
                        <a href="news.php" class="btn btn-outline-primary btn-sm fw-bold">Przejdź do Aktualności</a>
                    </div>
                </div>
            </aside>
            
            <section class="col-lg-9">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    DODAJ POST
                </div>
                
                <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">

                        <!-- Alerty -->
                        <?php if($resultMsg !== ''){ ?>
                            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show m-3" role="alert">
                                <?= $resultMsg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php } ?>

                        <div class="card-body d-flex flex-column p-4">
                            <form class="row g-3 needs-validation" action="addNews.php" method="POST" enctype="multipart/form-data" novalidate>
                                
                                <div class="col-md-12">
                                    <label for="title" class="form-label fw-bold small">Tytuł wpisu</label>
                                    <input type="text" class="form-control" id="title" name="title" required placeholder="np. Stefan znalazł wspaniały nowy dom!">
                                    <div class="invalid-feedback">Podaj tytuł aktualności.</div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="col-md-6">
                                        <label for="img" class="form-label fw-bold small">Obraz</label>
                                        <input type="file" class="form-control" id="img" name="img" accept="image/*" >
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <label for="content" class="form-label fw-bold small">Treść wpisu</label>
                                    <textarea class="form-control" id="content" name="content" rows="10" required placeholder="Napisz tutaj treść swojego artykułu..."></textarea>
                                    <div class="invalid-feedback">Pole treści nie może być puste.</div>
                                </div>
                                
                                <div class="col-12 mt-5 text-end border-top pt-4">
                                    <button class="btn btn-success btn-lg px-5 shadow-sm" type="submit" name="add_news">
                                        <i class="bi bi-pencil-square"></i> Opublikuj
                                    </button>
                                </div>
                            </form>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </main>

    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
</body>
</html>