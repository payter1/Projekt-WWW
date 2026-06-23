<?php
require_once 'connect.php';
require_once 'functions.php';

// Zabezpieczenie - tylko personelu (userLVL 1 lub 2) może tu wejść
if(!isset($_SESSION['logged']) || $_SESSION['logged'] != true || $_SESSION['userLVL'] != 2){
    header("Location: index.php");
    exit;
}

$idEdytowanego = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jaZalogowanyLvl = (int)$_SESSION['userLVL'];

// Jeśli nie podano ID, wróć do listy
if($idEdytowanego === 0) {
    header("Location: admusers.php");
    exit;
}

$resultMsg = '';
$success = false;

// ---------------------------------------------------------
// 1. POBRANIE AKTUALNYCH DANYCH EDYTOWANEGO UŻYTKOWNIKA
// ---------------------------------------------------------
$taskSql = "SELECT * FROM users WHERE id = ?";
$stmtParams = [$idEdytowanego];
$queryData = selectPrepared($taskSql, $stmtParams); // Zakładam, że selectPrepared jest w functions.php i używa prepared statements

if(!$queryData || count($queryData) === 0) {
    // Nie znaleziono takiego użytkownika
    header("Location: admusers.php?msg=err_not_found");
    exit;
}

$u = $queryData[0]; // Dane użytkownika

// Logika uprawnień: Tylko Admin (lvl 2) może edytować innego personelu
if($u['lvl'] != 0 && $jaZalogowanyLvl != 2) {
    header("Location: admusers.php?msg=err_no_perm");
    exit;
}


// ---------------------------------------------------------
// 2. OBSŁUGA WYSŁANIA FORMULARZA (AKTUALIZACJA)
// ---------------------------------------------------------
if(isset($_POST['update_user'])){
    
    // Logika zależy od tego, czy edytujemy pracownika czy zwykłego usera
    if($u['lvl'] != 0) {
        // --- Pełna edycja personelu ---
        $fname = htmlspecialchars(trim($_POST['fname']), ENT_QUOTES, 'UTF-8');
        $lname = htmlspecialchars(trim($_POST['lname']), ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
        $tel = htmlspecialchars(trim($_POST['tel']), ENT_QUOTES, 'UTF-8');
        $lvl = (int)$_POST['lvl'];
        $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8'); // Używam 'status', nie 'lvl' do blokady

        // Podstawowa walidacja dla pracownika
        if(empty($fname) || empty($lname) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $resultMsg = "Wypełnij poprawnie Imię, Nazwisko i adres E-mail.";
        } else {
            // Prepared statement do aktualizacji personelu
            $updateTask = "UPDATE users SET fname = ?, lname = ?, email = ?, tel = ?, lvl = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($updateTask);
            $stmt->bind_param("ssssisi", $fname, $lname, $email, $tel, $lvl, $status, $idEdytowanego);
            
            if($stmt->execute()){
                $success = true;
                $resultMsg = "Dane personelu zostały pomyślnie zaktualizowane.";
                // Odśwież dane do wyświetlenia w formularzu
                $u['fname'] = $fname; $u['lname'] = $lname; $u['email'] = $email; $u['tel'] = $tel; $u['lvl'] = $lvl; $u['status'] = $status;
            } else {
                $resultMsg = "Błąd zapisu do bazy: " . $stmt->error;
            }
            $stmt->close();
        }

    } else {
        // --- Tylko blokada dla zwykłego użytkownika ---
        $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

        // Prepared statement do aktualizacji tylko statusu
        $updateTask = "UPDATE users SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateTask);
        $stmt->bind_param("si", $status, $idEdytowanego);

        if($stmt->execute()){
            $success = true;
            $resultMsg = "Status konta użytkownika został zaktualizowany.";
            // Odśwież dane
            $u['status'] = $status;
        } else {
            $resultMsg = "Błąd zapisu do bazy: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <a href="admusers.php" class="btn btn-outline-secondary btn-sm mb-2">&laquo; Wróć do listy</a>
                <h2 class="h3 fw-bold mb-0 text-primary">Edycja Użytkownika: @<?= htmlspecialchars($u['login']) ?></h2>
                <small class="text-muted">ID: <?= $u['id'] ?> | Zarejestrowano: <?= date('d.m.Y', strtotime($u['created_at'])) ?></small>
            </div>
            <div class="text-end">
                <span class="d-block mb-1 small fw-bold text-muted">Status konta:</span>
                <?php $bColor = ($u['status'] == 'Aktywny') ? 'success' : 'danger'; ?>
                <span class="badge bg-<?= $bColor ?> fs-5 px-4 py-2 rounded-pill shadow-sm">
                    <?= $u['status'] ?>
                </span>
            </div>
        </div>

        <?php if($resultMsg !== ''){ ?>
            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                <?= $resultMsg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form class="row g-3 needs-validation" action="editUser.php?id=<?= $idEdytowanego ?>" method="POST" novalidate>
            <div class="col-lg-10 mx-auto">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        
                        <?php if($u['lvl'] != 0){ ?>
                            <h4 class="fw-bold text-primary mb-4 border-bottom pb-2">Pełna edycja danych personelu</h4>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fname" class="form-label fw-bold small">Imię</label>
                                    <input type="text" class="form-control" id="fname" name="fname" value="<?= htmlspecialchars($u['fname']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lname" class="form-label fw-bold small">Nazwisko</label>
                                    <input type="text" class="form-control" id="lname" name="lname" value="<?= htmlspecialchars($u['lname']) ?>" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="email" class="form-label fw-bold small">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="tel" class="form-label fw-bold small">Telefon</label>
                                    <input type="tel" class="form-control" id="tel" name="tel" value="<?= htmlspecialchars($u['tel']) ?>">
                                </div>
                                
                                <div class="col-12 mt-4"><hr></div>
                                
                                <div class="col-md-6">
                                    <label for="lvl" class="form-label fw-bold small">Poziom uprawnień</label>
                                    <select name="lvl" id="lvl" class="form-select" <?= ($jaZalogowanyLvl != 2) ? 'disabled' : '' ?>>
                                        <option value="1" <?= ($u['lvl'] == 1) ? 'selected' : '' ?>>Obsługa (Lvl 1)</option>
                                        <option value="2" <?= ($u['lvl'] == 2) ? 'selected' : '' ?>>Administrator (Lvl 2)</option>
                                    </select>
                                    <?php if($jaZalogowanyLvl != 2){ ?>
                                        <small class="text-muted">Tylko główny administrator może zmieniać poziom uprawnień.</small>
                                        <input type="hidden" name="lvl" value="<?= $u['lvl'] ?>">
                                    <?php } ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="statusPersonel" class="form-label fw-bold small">Blokada konta personelu</label>
                                    <select name="status" id="statusPersonel" class="form-select">
                                        <option value="Aktywny" <?= ($u['status'] == 'Aktywny') ? 'selected' : '' ?>>Konto aktywne</option>
                                        <option value="Zablokowany" <?= ($u['status'] == 'Zablokowany') ? 'selected' : '' ?>>Zablokuj konto personelu</option>
                                    </select>
                                </div>
                            </div>

                        <?php } else { ?>
                            <h4 class="fw-bold text-danger mb-4 border-bottom pb-2">Zarządzanie dostępem</h4>
                            <p class="text-muted small mb-4">Dla zwykłych użytkowników dostępna jest wyłącznie opcja zablokowania lub odblokowania konta. Dane osobowe nie podlegają edycji z poziomu tego panelu.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-8 mx-auto">
                                    <label for="statusUser" class="form-label fw-bold small">Status konta</label>
                                    <select name="status" id="statusUser" class="form-select select-lg">
                                        <option value="Aktywny" <?= ($u['status'] == 'Aktywny') ? 'selected' : '' ?>>Użytkownik Aktywny</option>
                                        <option value="Zablokowany" <?= ($u['status'] == 'Zablokowany') ? 'selected' : '' ?>>ZABLOKUJ Konto</option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        
                        <div class="col-12 mt-5 text-end border-top pt-4">
                            <button class="btn btn-success btn-lg px-5 shadow-sm fw-bold" type="submit" name="update_user">
                                <i class="bi bi-save"></i> Zapisz zmiany
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>

    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
</body>
</html>