<?php
require_once 'connect.php';
require_once 'functions.php';

// Zabezpieczenie - strona dostępna tylko dla zalogowanych użytkowników
if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true){
    header("Location: login.php?result=Musisz się zalogować, aby zobaczyć swoje wnioski.");
    exit;
}

// Zakładam, że ID zalogowanego użytkownika przechowujesz w $_SESSION['userID']
// Jeśli używasz innej nazwy (np. 'id'), zmień to poniżej:
$userID = (int)$_SESSION['userID']; 

// Pobranie wniosków zalogowanego użytkownika wraz ze zdjęciem osiołka
$task = "SELECT a.id, a.status, a.created_at, 
                d.name AS donkey_name, d.id AS donkey_id, 
                i.src 
         FROM adoptions a
         JOIN donkeys d ON a.donkey_id = d.id
         LEFT JOIN img i ON i.id_donk = d.id AND i.profile = 1
         WHERE a.user_id = $userID
         ORDER BY a.created_at DESC";

$query = $conn->query($task);
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        
        <div class="row mb-4 border-bottom pb-3">
            <div class="col">
                <h2 class="text-primary fw-bold mb-0">Moje wnioski adopcyjne</h2>
                <p class="text-muted small mt-1 mb-0">Tutaj możesz śledzić status swoich zgłoszeń.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                
                <?php if($query && $query->num_rows > 0){ ?>
                    <div class="list-group shadow-sm">
                        <?php while($row = $query->fetch_assoc()){ 
                            
                            // Dobieranie koloru i ikony w zależności od statusu
                            $badgeColor = 'secondary';
                            $icon = 'bi-clock-history';
                            $statusText = 'W trakcie weryfikacji';
                            
                            if($row['status'] == 'Oczekujący') {
                                $badgeColor = 'warning text-dark';
                                $icon = 'bi-hourglass-split';
                                $statusText = 'Oczekujący na rozpatrzenie';
                            } elseif($row['status'] == 'Zatwierdzony') {
                                $badgeColor = 'success';
                                $icon = 'bi-check-circle-fill';
                                $statusText = 'Gratulacje! Wniosek zatwierdzony.';
                            } elseif($row['status'] == 'Odrzucony') {
                                $badgeColor = 'danger';
                                $icon = 'bi-x-circle-fill';
                                $statusText = 'Niestety, wniosek został odrzucony.';
                            }
                            
                            // Zabezpieczenie braku zdjęcia
                            $imgSrc = !empty($row['src']) ? $row['src'] : 'img/default_donkey.png'; 
                        ?>
                        
                        <div class="list-group-item list-group-item-action p-4">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="ratio ratio-1x1 bg-secondary rounded-circle overflow-hidden shadow-sm" style="width: 80px; height: 80px;">
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Zdjęcie osiołka" class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>
                                
                                <div class="col">
                                    <h5 class="mb-1 fw-bold">
                                        <a href="donkProfile.php?id=<?= $row['donkey_id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($row['donkey_name']) ?>
                                        </a>
                                    </h5>
                                    <p class="mb-0 text-muted small">
                                        Złożono: <?= date('d.m.Y', strtotime($row['created_at'])) ?> r. o godz. <?= date('H:i', strtotime($row['created_at'])) ?>
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        ID Wniosku: #<?= $row['id'] ?>
                                    </p>
                                </div>
                                
                                <div class="col-md-auto mt-3 mt-md-0 text-md-end">
                                    <span class="d-block mb-1 small text-muted">Status:</span>
                                    <span class="badge bg-<?= $badgeColor ?> fs-6 px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="bi <?= $icon ?>"></i> <?= $row['status'] ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if($row['status'] == 'Zatwierdzony'){ ?>
                            <div class="alert alert-success mt-3 mb-0 py-2 small" role="alert">
                                <strong>Skontaktujemy się z Tobą!</strong> Przygotuj się na wizytę przedadopcyjną. Nasz wolontariusz zadzwoni w ciągu kilku dni.
                            </div>
                            <?php } elseif($row['status'] == 'Oczekujący'){ ?>
                            <div class="alert alert-warning mt-3 mb-0 py-2 small" role="alert">
                                Twój wniosek trafił do naszej bazy. Ze względu na dużą liczbę zgłoszeń, czas oczekiwania na odpowiedź może potrwać do 14 dni.
                            </div>
                            <?php } ?>

                        </div>
                        
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    
                    <div class="text-center py-5 bg-light rounded-3 border border-dashed">
                        <h4 class="text-muted mb-3">Nie złożyłeś jeszcze żadnego wniosku.</h4>
                        <p class="text-muted mb-4">Przejdź do naszej stajni, wybierz osiołka, który skradł Twoje serce i wypełnij ankietę adopcyjną!</p>
                        <a href="index.php" class="btn btn-primary btn-lg shadow-sm">Zobacz osiołki do adopcji</a>
                    </div>
                    
                <?php } ?>

            </div>
        </div>
    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>