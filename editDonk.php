<?php
require_once 'connect.php';
require_once 'functions.php';

if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['userLVL'] == 0){
    header("Location: index.php");
    exit;
}

$idEdytowanego = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($idEdytowanego === 0) {
    header("Location: index.php");
    exit;
}

$resultMsg = '';
$success = false;

if(isset($_POST['update_donkey'])){
    $name = htmlspecialchars(trim($_POST['donkeyName']), ENT_QUOTES, 'UTF-8');
    $bday = $_POST['bDay'];
    $sex = !empty($_POST['sex']) ? $_POST['sex'] : null;
    $colorId = !empty($_POST['colorId']) ? (int)$_POST['colorId'] : null;
    $guardianId = !empty($_POST['guardianId']) ? (int)$_POST['guardianId'] : null;
    $info = !empty(trim($_POST['donkeyInfo'])) ? trim($_POST['donkeyInfo']) : null;
    $status = isset($_POST['isActive']) ? 1 : 0;

    if(empty($name) || empty($bday)){
        $resultMsg = "Imię oraz data urodzenia są wymagane!";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $updateTask = "UPDATE donkeys SET `name` = ?, `bday` = ?, `sex` = ?, `color` = ?, `guardian` = ?, `info` = ?, `status` = ? WHERE `id` = ?";
            $stmt = $conn->prepare($updateTask);
            
            if ($stmt) {
                $stmt->bind_param("sssiisii", $name, $bday, $sex, $colorId, $guardianId, $info, $status, $idEdytowanego);

                if (!$stmt->execute()) {
                    throw new Exception("Wystąpił błąd podczas aktualizacji danych osiołka w bazie.");
                }
                $stmt->close();
            } else {
                throw new Exception("Błąd przygotowania zapytania SQL.");
            }
            
            if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
                $resetProfileImg = "UPDATE img SET profile = NULL WHERE id_donk = $idEdytowanego AND profile = 1";
                mysqli_query($conn, $resetProfileImg);

                $profilePic = saveFile('profilePic', 'png, jpg, jpeg, webp, gif', 10, "img/donkeys/$idEdytowanego/prof");
                if ($profilePic[0] == 1) {
                    $newSrc = $profilePic[1];
                    $insertProfImg = "INSERT INTO img (`src`, `id_donk`, `profile`) VALUES ('$newSrc', $idEdytowanego, 1)";
                    if(!mysqli_query($conn, $insertProfImg)){
                        throw new Exception("Błąd przypisywania nowego zdjęcia profilowego do bazy.");
                    }
                } else {
                    throw new Exception("Nie udało się zapisać nowego zdjęcia profilowego na serwerze.");
                }
            }
            
            if (isset($_FILES['extraPics']) && $_FILES['extraPics']['error'][0] === UPLOAD_ERR_OK) {
                $extraPics = saveFile('extraPics', 'png, jpg, jpeg, webp, gif', 10, "img/donkeys/$idEdytowanego");
                if ($extraPics[0] == 1) {
                    $extraPicsHrefs = explode(';', $extraPics[1]);
                    $imgToTask = '';
                    foreach ($extraPicsHrefs as $picHref) {
                        if (!empty($picHref)) {
                            $imgToTask .= "('$picHref', $idEdytowanego, NULL),";
                        }
                    }
                    $imgToTask = rtrim($imgToTask, ',');
                    
                    if (strlen($imgToTask) > 0) {
                        $taskImg = "INSERT INTO img (`src`, `id_donk`, `profile`) VALUES $imgToTask";
                        if (!mysqli_query($conn, $taskImg)) {
                            throw new Exception("Błąd podczas przypisywania zdjęć dodatkowych w bazie danych.");
                        }
                    }
                }
            }
        
            mysqli_commit($conn);
            $success = true;
            $resultMsg = "Profil osiołka i zdjęcia zostały pomyślnie zaktualizowane!";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $resultMsg = $e->getMessage();
        }
    }
}

$task = "SELECT * FROM donkeys WHERE id = $idEdytowanego";
$query = mysqli_query($conn, $task);

if(!$query || mysqli_num_rows($query) === 0) {
    header("Location: index.php");
    exit;
}

$d = mysqli_fetch_assoc($query);

$colorsQuery = mysqli_query($conn, "SELECT * FROM colors ORDER BY name ASC");
$usersQuery = mysqli_query($conn, "SELECT id, fname, lname, login FROM users WHERE active = 1 AND lvl IN (1,2) ORDER BY lname ASC");

?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <a href="donkProfile.php?id=<?= $idEdytowanego ?>" class="btn btn-outline-secondary btn-sm mb-2">&laquo; Wróć do profilu</a>
                <h2 class="h3 fw-bold mb-0 text-primary">Edycja Osiołka: <?= htmlspecialchars($d['name']) ?></h2>
            </div>
            <div class="text-end">
                <span class="badge bg-<?= ($d['status'] == 1) ? 'success' : 'secondary' ?> fs-6 px-3 py-2 rounded-pill shadow-sm">
                    <?= ($d['status'] == 1) ? 'Aktywny (Widoczny)' : 'Nieaktywny (Ukryty)' ?>
                </span>
            </div>
        </div>

        <?php if($resultMsg !== ''){ ?>
            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                <?= $resultMsg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form class="row g-3 needs-validation" action="editDonk.php?id=<?= $idEdytowanego ?>" method="POST" enctype="multipart/form-data" novalidate>
            <div class="col-lg-10 mx-auto">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="donkeyName" class="form-label fw-bold small">Imię Osła</label>
                                <input type="text" class="form-control" id="donkeyName" name="donkeyName" value="<?= htmlspecialchars($d['name']) ?>" required>
                                <div class="invalid-feedback">Wprowadź imię osła.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="bDay" class="form-label fw-bold small">Data urodzenia</label>
                                <input type="date" class="form-control" id="bDay" name="bDay" max="<?= date('Y-m-d'); ?>" value="<?= htmlspecialchars($d['bday']) ?>" required>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div>
                                    <label class="form-label d-block mb-2 fw-bold small">Płeć</label>
                                    <input type="radio" class="btn-check" name="sex" id="fem" value="F" autocomplete="off" <?= ($d['sex'] == 'F') ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-danger" for="fem">Samica</label>

                                    <input type="radio" class="btn-check" name="sex" id="male" value="M" autocomplete="off" <?= ($d['sex'] == 'M') ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="male">Samiec</label>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mt-4 bg-light p-2 rounded border w-100">
                                    <input class="form-check-input ms-1 me-2" type="checkbox" id="isActive" name="isActive" value="1" <?= ($d['status'] == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="isActive">
                                        Aktywny (Pokazuj na stajni)
                                    </label>
                                </div>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-6">
                                <label for="profilePic" class="form-label fw-bold small">Zmień zdjęcie profilowe</label>
                                <input type="file" class="form-control" id="profilePic" name="profilePic" accept="image/*">
                                <div class="form-text">Zostaw puste, aby zachować obecne zdjęcie główne.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="extraPics" class="form-label fw-bold small">Dodaj zdjęcia dodatkowe (do karuzeli)</label>
                                <input type="file" class="form-control" id="extraPics" name="extraPics[]" accept="image/*" multiple>
                                <div class="form-text">Możesz zaznaczyć i dodać kilka zdjęć (Ctrl / Cmd).</div>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-6">
                                <label for="color" class="form-label fw-bold small">Kolor (Maść)</label>
                                <select class="form-select" id="color" name="colorId">
                                    <option disabled value="">Wybierz kolor...</option>
                                    <?php while($color = mysqli_fetch_assoc($colorsQuery)){ ?>
                                        <option value="<?= $color['id'] ?>" <?= ($d['color'] == $color['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($color['name']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="guardian" class="form-label fw-bold small">Przypisany Opiekun</label>
                                <select class="form-select" id="guardian" name="guardianId">
                                    <option value="">-- Brak przypisanego opiekuna --</option>
                                    <?php while($user = mysqli_fetch_assoc($usersQuery)){ ?>
                                        <option value="<?= $user['id'] ?>" <?= ($d['guardian'] == $user['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['fname'] . ' ' . $user['lname'] . ' (@' . $user['login'] . ')') ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="donkeyInfo" class="form-label fw-bold small">Informacje o Osiołku</label>
                                <textarea class="form-control" id="donkeyInfo" name="donkeyInfo" rows="5" placeholder="Wpisz dodatkowe informacje, cechy charakteru..."><?= htmlspecialchars($d['info']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-5 text-end border-top pt-4">
                            <button class="btn btn-success btn-lg px-5 shadow-sm fw-bold" type="submit" name="update_donkey">
                                Zapisz zmiany
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>

    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
    <script>
        if (typeof TomSelect !== 'undefined') {
            document.addEventListener("DOMContentLoaded", function() {
                new TomSelect("#color", { dropdownParent: "body" });
                new TomSelect("#guardian", { dropdownParent: "body" });
            });
        }
    </script>
</body>
</html>