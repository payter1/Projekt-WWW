<?php
require_once 'connect.php';
require_once 'functions.php';

if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['userLVL'] == 0){
    header("Location: index.php");
    exit;
}

$idEdytowanego = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($idEdytowanego === 0) {
    header("Location: news.php");
    exit;
}

$resultMsg = '';
$success = false;

if(isset($_POST['update_news'])){
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    
    if(empty($title) || empty($content)){
        $resultMsg = "Tytuł i treść wpisu są wymagane!";
    } else {
        mysqli_begin_transaction($conn);
        try {
            $newImgSrc = null;
            
            if (isset($_FILES['newsImg']) && $_FILES['newsImg']['error'] === UPLOAD_ERR_OK) {
                $imgUpload = saveFile('newsImg', 'png, jpg, webp, gif', 10, "img/news/$idEdytowanego");
                if ($imgUpload[0] == 1) {
                    $newImgSrc = $imgUpload[1];
                } else {
                    throw new Exception(implode(" ", $imgUpload[1]));
                }
            }

            if ($newImgSrc) {
                $updateTask = "UPDATE news SET title = ?, content = ?, image_url = ? WHERE id = ?";
                $stmt = $conn->prepare($updateTask);
                if($stmt){
                    $stmt->bind_param("sssi", $title, $content, $newImgSrc, $idEdytowanego);
                    if(!$stmt->execute()) throw new Exception("Błąd zapisu do bazy.");
                    $stmt->close();
                } else {
                    throw new Exception("Błąd przygotowania zapytania SQL.");
                }
            } else {
                $updateTask = "UPDATE news SET title = ?, content = ? WHERE id = ?";
                $stmt = $conn->prepare($updateTask);
                if($stmt){
                    $stmt->bind_param("ssi", $title, $content, $idEdytowanego);
                    if(!$stmt->execute()) throw new Exception("Błąd zapisu do bazy.");
                    $stmt->close();
                } else {
                    throw new Exception("Błąd przygotowania zapytania SQL.");
                }
            }

            mysqli_commit($conn);
            $success = true;
            $resultMsg = "Zapisano zmiany we wpisie!";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $resultMsg = $e->getMessage();
        }
    }
}

$task = "SELECT * FROM news WHERE id = $idEdytowanego";
$query = mysqli_query($conn, $task);

if(!$query || mysqli_num_rows($query) === 0) {
    header("Location: news.php");
    exit;
}

$n = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <a href="readNews.php?id=<?= $idEdytowanego ?>" class="btn btn-outline-secondary btn-sm mb-2">&laquo; Wróć do podglądu wpisu</a>
                <h2 class="h3 fw-bold mb-0 text-primary">Edycja wpisu: <?= htmlspecialchars($n['title']) ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <article class="card h-100 shadow-sm border-0 position-relative">

                    <?php if($resultMsg !== ''){ ?>
                        <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show m-3" role="alert">
                            <?= $resultMsg ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>

                    <div class="card-body d-flex flex-column p-4">
                        <form class="row g-3 needs-validation" action="editNews.php?id=<?= $idEdytowanego ?>" method="POST" enctype="multipart/form-data" novalidate>
                            
                            <div class="col-md-12">
                                <label for="title" class="form-label fw-bold small">Tytuł wpisu</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($n['title']) ?>" required>
                                <div class="invalid-feedback">Podaj tytuł aktualności.</div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <label for="newsImg" class="form-label fw-bold small">Zmień zdjęcie główne wpisu</label>
                                <input type="file" class="form-control" id="newsImg" name="newsImg" accept="image/*">
                                <div class="form-text">Zostaw to pole puste, jeśli nie chcesz zmieniać obecnego zdjęcia.</div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <label for="content" class="form-label fw-bold small">Treść wpisu</label>
                                <textarea class="form-control" id="content" name="content" rows="15" required><?= htmlspecialchars($n['content']) ?></textarea>
                                <div class="invalid-feedback">Pole treści nie może być puste.</div>
                            </div>
                            
                            <div class="col-12 mt-5 text-end border-top pt-4">
                                <button class="btn btn-success btn-lg px-5 shadow-sm fw-bold" type="submit" name="update_news">
                                    Zapisz zmiany
                                </button>
                            </div>
                        </form>
                    </div>
                </article>
            </div>
        </div>

    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
</body>
</html>