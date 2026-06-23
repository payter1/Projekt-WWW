<?php
require_once 'connect.php';
require_once 'functions.php';

// Zabezpieczenie - tylko pracownicy/admini
if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['userLVL'] == 0){
    header("Location: index.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id === 0) {
    header("Location: adoptionsList.php");
    exit;
}

// Obsługa zmiany statusu wniosku
if(isset($_POST['action'])) {
    $newStatus = '';
    if($_POST['action'] === 'approve') $newStatus = 'Zatwierdzony';
    elseif($_POST['action'] === 'reject') $newStatus = 'Odrzucony';
    elseif($_POST['action'] === 'pending') $newStatus = 'Oczekujący';

    if($newStatus !== '') {
        $updateTask = "UPDATE adoptions SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateTask);
        if($stmt) {
            $stmt->bind_param("si", $newStatus, $id);
            $stmt->execute();
            $stmt->close();
            // Przekierowanie, by zapobiec ponownemu wysłaniu formularza po odświeżeniu
            header("Location: adoptionsDetails.php?id=$id&msg=success");
            exit;
        }
    }
}

// Pobranie pełnych danych wniosku, użytkownika i osiołka
$task = "SELECT a.*, 
                u.fname, u.lname, u.email, u.tel, u.city, u.street, u.hnum, u.anum, u.zip,
                d.name AS donkey_name
         FROM adoptions a
         JOIN users u ON a.user_id = u.id
         JOIN donkeys d ON a.donkey_id = d.id
         WHERE a.id = $id";
$query = $conn->query($task);

if($query->num_rows === 0) {
    header("Location: adoptionsList.php");
    exit;
}

$f = $query->fetch_assoc();

// Przygotowanie koloru badge dla statusu
$badgeColor = 'secondary';
if($f['status'] == 'Oczekujący') $badgeColor = 'warning text-dark';
elseif($f['status'] == 'Zatwierdzony') $badgeColor = 'success';
elseif($f['status'] == 'Odrzucony') $badgeColor = 'danger';
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <a href="adoptionsList.php" class="btn btn-outline-secondary btn-sm mb-2">&laquo; Wróć do listy</a>
                <h2 class="h3 fw-bold mb-0 text-primary">Szczegóły wniosku #<?= $f['id'] ?></h2>
                <small class="text-muted">Złożono: <?= date('d.m.Y H:i', strtotime($f['created_at'])) ?></small>
            </div>
            <div class="text-end">
                <span class="d-block mb-1 small fw-bold text-muted">Obecny status:</span>
                <span class="badge bg-<?= $badgeColor ?> fs-5 px-4 py-2 rounded-pill shadow-sm">
                    <?= $f['status'] ?>
                </span>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'success'){ ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                Status wniosku został pomyślnie zaktualizowany.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm bg-light mb-4 rounded-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">Dane Osiołka</h5>
                        <p class="mb-1"><strong>Imię:</strong> <a href="donkProfile.php?id=<?= $f['donkey_id'] ?>" class="text-decoration-none fw-bold"><?= htmlspecialchars($f['donkey_name']) ?></a></p>
                        <p class="mb-0"><strong>ID w bazie:</strong> <?= $f['donkey_id'] ?></p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-light mb-4 rounded-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">Dane Wnioskodawcy</h5>
                        <p class="mb-1"><strong>Imię i nazwisko:</strong> <?= htmlspecialchars($f['fname'] . ' ' . $f['lname']) ?></p>
                        <p class="mb-1"><strong>E-mail:</strong> <a href="mailto:<?= htmlspecialchars($f['email']) ?>"><?= htmlspecialchars($f['email']) ?></a></p>
                        <p class="mb-3"><strong>Telefon:</strong> <a href="tel:<?= htmlspecialchars($f['tel']) ?>"><?= htmlspecialchars($f['tel']) ?></a></p>
                        
                        <h6 class="fw-bold small text-muted mb-1">Adres:</h6>
                        <p class="mb-0 small">
                            <?= htmlspecialchars($f['street']) ?> <?= htmlspecialchars($f['hnum']) ?><?= !empty($f['anum']) ? '/'.htmlspecialchars($f['anum']) : '' ?><br>
                            <?= htmlspecialchars($f['zip']) ?> <?= htmlspecialchars($f['city']) ?>
                        </p>
                    </div>
                </div>

                <div class="card border border-primary shadow-sm rounded-3 sticky-top" style="top: 90px;">
                    <div class="card-body text-center">
                        <h5 class="fw-bold text-primary mb-3">Zarządzaj Wnioskiem</h5>
                        <form action="adoptionsDetails.php?id=<?= $id ?>" method="POST" class="d-grid gap-2">
                            <?php if($f['status'] !== 'Zatwierdzony'){ ?>
                                <button type="submit" name="action" value="approve" class="btn btn-success fw-bold shadow-sm">Zatwierdź do adopcji</button>
                            <?php } ?>
                            
                            <?php if($f['status'] !== 'Odrzucony'){ ?>
                                <button type="submit" name="action" value="reject" class="btn btn-danger fw-bold shadow-sm">Odrzuć wniosek</button>
                            <?php } ?>

                            <?php if($f['status'] !== 'Oczekujący'){ ?>
                                <hr class="my-1">
                                <button type="submit" name="action" value="pending" class="btn btn-outline-warning btn-sm fw-bold">Cofnij do "Oczekujący"</button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-primary mb-4">Odpowiedzi na Ankietę Przedadopcyjną</h4>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">1. Cel adopcji osiołka:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['purpose'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">2. Doświadczenie ze zwierzętami gospodarskimi/osłami:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['experience'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">3. Warunki stacjonowania (stajnia, wiata):</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['housing'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">4. Wybieg i ogrodzenie:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['fencing'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">5. Planowana dieta osiołka:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['diet_knowledge'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">6. Inne posiadane zwierzęta:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['other_animals'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">7. Główny opiekun i czas dziennie:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['caretaker_time'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">8. Opieka w przypadku wyjazdu/choroby:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['vacation_care'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">9. Świadomość kosztów (budżet miesięczny):</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['finances'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">10. Organizacja transportu:</h6>
                            <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                                <?= nl2br(htmlspecialchars($f['transport'])) ?>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-danger mb-3">Zgody prawne i regulaminowe</h5>
                        
                        <ul class="list-group list-group-flush shadow-sm rounded">
                            <li class="list-group-item d-flex align-items-center bg-light">
                                <?php if($f['vet_agreement'] == 1) { ?>
                                    <i class="text-success fs-4 me-3 bi bi-check-circle-fill">✓</i>
                                <?php } else { ?>
                                    <i class="text-danger fs-4 me-3 bi bi-x-circle-fill">✗</i>
                                <?php } ?>
                                <span>Wnioskodawca zobowiązuje się do zapewnienia regularnej opieki weterynaryjnej (odrobaczanie, szczepienia) oraz kowalskiej.</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center bg-light">
                                <?php if($f['inspection_agreement'] == 1) { ?>
                                    <i class="text-success fs-4 me-3 bi bi-check-circle-fill">✓</i>
                                <?php } else { ?>
                                    <i class="text-danger fs-4 me-3 bi bi-x-circle-fill">✗</i>
                                <?php } ?>
                                <span>Wnioskodawca wyraża zgodę na wizyty przedadopcyjne i poadopcyjne.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>