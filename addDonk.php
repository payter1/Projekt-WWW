<?php
require_once 'connect.php';
require_once 'functions.php';
if(isset($_SESSION['logged']) && $_SESSION['logged'] === true && $_SESSION['userLVL'] != 2){
    header("Location: index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $isValid = true;
    $result = 0; 

    $_SESSION['donkeyName'] = $_POST['donkeyName'];
    $_SESSION['colorId'] = $_POST['colorId'];
    $_SESSION['guardianId'] = $_POST['guardianId'];
    $_SESSION['donkeyInfo'] = $_POST['donkeyInfo'];
    $_SESSION['bDay'] = $_POST['bDay'];
    
    // WALIDACJA DANYCH
    if (empty($_POST['donkeyName'])) {
        $isValid = false;
        $result = "Imię jest wymagane";
        
    } elseif (!isset($_FILES['profilePic']) || $_FILES['profilePic']['error'] !== UPLOAD_ERR_OK) {
        $isValid = false;
        $result = "Zdjęcie profilowe jest wymagane";

    } elseif (empty($_POST['bDay'])) {
        $isValid = false;
        $result = "Data urodzenia jest wymagana";
    }

    // ZAPIS DO BAZY 
    if ($isValid) {
        $name = htmlspecialchars(trim($_POST['donkeyName']), ENT_QUOTES, 'UTF-8');
        $colorId = !empty($_POST['colorId']) ? (int)$_POST['colorId'] : null;
        $guardianId = !empty($_POST['guardianId']) ? (int)$_POST['guardianId'] : null;
        $info = !empty(trim($_POST['donkeyInfo'])) ? trim($_POST['donkeyInfo']) : null;
        $bday = $_POST['bDay'];
        $status = isset($_POST['isActive']) ? 1 : 0;
        $sex = !empty($_POST['sex']) ? $_POST['sex'] : null;
        // START TRANSAKCJI
        mysqli_begin_transaction($conn);

        try {
            // 1. DODAWANIE DO TABELI DONKEYS
            $task = "INSERT INTO donkeys (`name`, `bday`, `sex`, `color`, `guardian`, `info`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $query = $conn->prepare($task);
            
            if ($query) {
                $query->bind_param("sssiisi", $name, $bday, $sex, $colorId, $guardianId, $info, $status);

                if (!$query->execute()) {
                    throw new Exception("Wystąpił błąd podczas dodawania osła do bazy.");
                }
                
                $idDonk = $conn->insert_id;
                $query->close();
            } else {
                throw new Exception("Błąd przygotowania zapytania SQL.");
            }
            
            // 2. OBSŁUGA ZDJĘĆ
            $imgToTask = '';
            
            // Zdjęcie profilowe
            $profilePic = saveFile('profilePic', 'png, jpg, jpeg, webp, gif', 10, "img/donkeys/$idDonk/prof");
            if ($profilePic[0] == 1) {
                // profile = 1 dla zdjęcia profilowego
                $imgToTask .= "('$profilePic[1]', $idDonk, 1),";
            } else {
                // throw new Exception("Nie udało się zapisać zdjęcia profilowego na serwerze.");
                throw new Exception("Nie udało się zapisać zdjęcia profilowego na serwerze: " . $profilePic[1]);
            }
            
            // Zdjęcia dodatkowe
            if (isset($_FILES['extraPics']) && $_FILES['extraPics']['error'][0] === UPLOAD_ERR_OK) {
                $extraPics = saveFile('extraPics', 'png, jpg, jpeg, webp, gif', 10, "img/donkeys/$idDonk");
                if ($extraPics[0] == 1) {
                    $extraPicsHrefs = explode(';', $extraPics[1]);
                    foreach ($extraPicsHrefs as $picHref) {
                        if (!empty($picHref)) {
                            // profile = 0 dla zdjęć dodatkowych
                            $imgToTask .= "('$picHref', $idDonk, NULL),";
                        }
                    }
                }
            }
            
            // Wykonanie zapytania zbiorczego dla zdjęć
            $imgToTask = rtrim($imgToTask, ',');
            
            if (strlen($imgToTask) > 0) {
                $taskImg = "INSERT INTO img (`src`, `id_donk`, `profile`) VALUES $imgToTask";
                if (!mysqli_query($conn, $taskImg)) {
                    throw new Exception("Błąd podczas przypisywania zdjęć w bazie danych.");
                }
            }
        
            mysqli_commit($conn);
            $result = 1; // 1 = sukces
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $result = $e->getMessage();
        }
    }
    header("Location: addDonk.php?result=" . urlencode($result));
    exit;
} 
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<!-- <head>  -->
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <!-- Panel nawigacyjny -->
    <?php require_once("partials/nav.php")?>
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-light bg-opacity-75" >
        <div class="row g-4 z-2">
            <section class="col">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    DODAWANIE OSŁA 
                </div>
                 <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">

                        <!-- alerty -->
                        <?php if(isset($_GET['result']) && $_GET['result'] != 1){?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Błąd: </strong> <?=$_GET['result']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php }?>
                        <?php if(isset($_GET['result']) && $_GET['result'] == 1){?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Dodano osiołka!</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php }?>
                        <!-- // -->

                        <div class="card-body d-flex flex-column p-3">
                        <!-- POCZĄTEK FORMULARZA DODAWANIA OSŁA -->
                        <form class="row g-3 needs-validation" action="addDonk.php" method="POST" enctype="multipart/form-data" novalidate>
    
                            <div class="col-md-6">
                                <label for="donkeyName" class="form-label">Imię Osła</label>
                                <input type="text" class="form-control" id="donkeyName" value="<?php if(isset($_SESSION['name'])){ echo $_SESSION['name']; unset($_SESSION['name']);} ?>" name="donkeyName" required>
                                <div class="invalid-feedback">
                                    Wprowadź imię osła
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="bDay" class="form-label">Data urodzenia</label>
                                <input type="date" class="form-control" id="bDay" name="bDay" max="<?= date('Y-m-d'); ?>" value="<?php if(isset($_SESSION['bDay'])){ echo $_SESSION['bDay']; unset($_SESSION['bDay']);} ?>" required>
                                <div class="invalid-feedback">
                                    Wprowadź prawidłową datę urodzenia (nie późniejszą niż dzisiaj)
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div>
                                    <label class="form-label d-block mb-2">Płeć</label>
                                    <input type="radio" class="btn-check" name="sex" id="fem" value="F" autocomplete="off">
                                    <label class="btn btn-outline-danger" for="fem">Samica</label>

                                    <input type="radio" class="btn-check" name="sex" id="male" value="M" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="male">Samiec</label>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="isActive" value="1" checked>
                                    <label class="form-check-label" for="isActive">
                                        Aktywny
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="profilePic" class="form-label">Zdjęcie profilowe</label>
                                <input type="file" class="form-control" id="profilePic" name="profilePic" accept="image/*" required>
                                <div class="invalid-feedback">
                                    Wybierz zdjęcie profilowe
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="extraPics" class="form-label">Zdjęcia dodatkowe</label>
                                <input type="file" class="form-control" id="extraPics" name="extraPics[]" accept="image/*" multiple>
                                <div class="form-text">Możesz wybrać kilka zdjęć naraz (przytrzymaj Ctrl / Cmd).</div>
                            </div>

                            <div class="col-md-6">
                                <label for="color" class="form-label">Kolor</label>
                                <select class="form-select" id="color" name="colorId">
                                    <option selected disabled value="">Wybierz kolor...</option>
                                    <?php 
                                    // Przykładowa pętla zasilająca z bazy - zmienna $colors to wynik zapytania (np. PDO/MySQLi)
                                    // SELECT id, name FROM colors
                                    $taskColors = "SELECT * FROM colors";
                                    $colors = mysqli_query($conn, $taskColors);
                                    if (!empty($colors)) {
                                        foreach ($colors as $color) {
                                            echo '<option value="' . htmlspecialchars($color['id']) . '">' . htmlspecialchars($color['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Wybierz kolor z listy
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="guardian" class="form-label">Opiekun</label>
                                <select class="form-select" id="guardian" name="guardianId">
                                    <option selected disabled value="">Przypisz opiekuna...</option>
                                    <?php 
                                    $taskUsers = "SELECT * FROM users WHERE active = 1 AND lvl IN (1,2)";
                                    $users = mysqli_query($conn, $taskUsers);
                                    if (!empty($users)) {
                                        foreach ($users as $user) {
                                            echo '<option value="' . htmlspecialchars($user['id']) . '">' . htmlspecialchars($user['fname']) . ' '.$user['lname'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Przypisz opiekuna z listy
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="donkeyInfo" class="form-label">Informacje o Osiołku</label>
                                <textarea class="form-control" id="donkeyInfo" name="donkeyInfo" rows="4" placeholder="Wpisz dodatkowe informacje, cechy charakteru, przebyte choroby, czy uwagi dotyczące osiołka...">
                                    <?php if(isset($_SESSION['donkeyInfo'])){ echo $_SESSION['donkeyInfo']; unset($_SESSION['donkeyInfo']);} ?>
                                </textarea>
                                <div class="invalid-feedback">
                                    Wprowadź informacje o osiołku.
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button class="btn btn-primary" type="submit">Dodaj Osła</button>
                            </div>
                        </form>
                        <!-- KONIEC FORMULARZ REJESTRACJI -->
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#color", {
            dropdownParent: "body", 
        });
    });
    </script>
</body>
</html>