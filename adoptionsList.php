<?php
require_once 'connect.php';
require_once 'functions.php';

// Zabezpieczenie - tylko pracownicy/admini
if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['userLVL'] == 0){
    header("Location: index.php");
    exit;
}

// -----------------------------------------
// 1. BUDOWANIE WARUNKÓW ZAPYTANIA (FILTRY)
// -----------------------------------------
$whereClauses = ["1=1"];

// Filtr: Status
$status = $_GET['status'] ?? '';
if(in_array($status, ['Oczekujący', 'Zatwierdzony', 'Odrzucony'])) {
    $whereClauses[] = "a.status = '$status'";
}

// Filtr: Osiołek (ID lub Imię wpisane z ręki / podpowiedzi AJAX)
$donkey_id = (int)($_GET['donkey_id'] ?? 0);
$donkey_name = $conn->real_escape_string($_GET['donkey_name'] ?? '');

if($donkey_id > 0) {
    $whereClauses[] = "a.donkey_id = $donkey_id";
} elseif(!empty($donkey_name)) {
    $whereClauses[] = "d.name LIKE '%$donkey_name%'";
}

// Filtr: Data (Od - Do)
$date_from = $conn->real_escape_string($_GET['date_from'] ?? '');
if(!empty($date_from)) {
    $whereClauses[] = "DATE(a.created_at) >= '$date_from'";
}

$date_to = $conn->real_escape_string($_GET['date_to'] ?? '');
if(!empty($date_to)) {
    $whereClauses[] = "DATE(a.created_at) <= '$date_to'";
}

// Filtr: Opiekun (Wyszukiwanie tekstowe)
$guardian_name = $conn->real_escape_string(trim($_GET['guardian_name'] ?? ''));
if(!empty($guardian_name)) {
    $whereClauses[] = "(g.fname LIKE '%$guardian_name%' OR g.lname LIKE '%$guardian_name%')";
}

// Filtr: Tylko moje osiołki
$only_mine = isset($_GET['only_mine']) ? true : false;
if($only_mine && isset($_SESSION['userID'])) {
    $myID = (int)$_SESSION['userID'];
    $whereClauses[] = "d.guardian = $myID";
}

// Sortowanie (Po dacie lub po opiekunie)
$sort_by = $_GET['sort_by'] ?? 'date_desc';
switch($sort_by) {
    case 'date_asc':
        $orderBy = "a.created_at ASC";
        break;
    case 'guardian_asc':
        $orderBy = "g.lname ASC, g.fname ASC, a.created_at DESC";
        break;
    case 'guardian_desc':
        $orderBy = "g.lname DESC, g.fname DESC, a.created_at DESC";
        break;
    case 'date_desc':
    default:
        $orderBy = "a.created_at DESC";
        break;
}

// Łączenie wszystkich warunków
$whereSQL = implode(' AND ', $whereClauses);

// -----------------------------------------
// 2. GŁÓWNE ZAPYTANIE DO BAZY
// -----------------------------------------
// Dołączamy u dla Wnioskodawcy i g dla Opiekuna osiołka
$task = "SELECT a.id, a.status, a.created_at, 
                u.fname AS a_fname, u.lname AS a_lname, 
                d.name AS donkey_name, d.id AS donkey_id,
                g.fname AS g_fname, g.lname AS g_lname
         FROM adoptions a
         JOIN users u ON a.user_id = u.id
         JOIN donkeys d ON a.donkey_id = d.id
         LEFT JOIN users g ON d.guardian = g.id
         WHERE $whereSQL
         ORDER BY $orderBy";
$query = $conn->query($task);
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
            
            <!-- Boczny blok - Formularz Filtrów -->
            <aside class="col-lg-3">
                <div class="card p-3 shadow-sm sticky-top bg-primary-subtle" style="top: 90px;">
                    <div class="text-center fw-bold mb-2">FILTRY WNIOSKÓW</div>
                    <hr class="mt-0">
                    
                    <form action="adoptionsList.php" method="GET" id="filterForm">
                        
                        <!-- Tylko moje osiołki -->
                        <div class="form-check form-switch mb-3 bg-white p-2 rounded border shadow-sm">
                            <input class="form-check-input ms-1 me-2" type="checkbox" role="switch" id="only_mine" name="only_mine" value="1" <?= $only_mine ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-primary" for="only_mine">Tylko moje osiołki</label>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Status wniosku</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Wszystkie</option>
                                <option value="Oczekujący" <?= ($status === 'Oczekujący') ? 'selected' : '' ?>>Oczekujące</option>
                                <option value="Zatwierdzony" <?= ($status === 'Zatwierdzony') ? 'selected' : '' ?>>Zatwierdzone</option>
                                <option value="Odrzucony" <?= ($status === 'Odrzucony') ? 'selected' : '' ?>>Odrzucone</option>
                            </select>
                        </div>

                        <!-- Szukaj Osiołka (AJAX Autocomplete) -->
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold small">Osiołek (Imię/ID)</label>
                            <input type="text" class="form-control form-control-sm" id="donkeySearch" name="donkey_name" placeholder="Zacznij pisać..." value="<?= htmlspecialchars($_GET['donkey_name'] ?? '') ?>" autocomplete="off">
                            <input type="hidden" id="donkeyId" name="donkey_id" value="<?= htmlspecialchars($_GET['donkey_id'] ?? '') ?>">
                            <div id="donkeySuggestions" class="list-group position-absolute w-100 shadow-sm z-3" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <!-- Filtr: Opiekun -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Opiekun (Imię/Nazwisko)</label>
                            <input type="text" class="form-control form-control-sm" name="guardian_name" placeholder="Wyszukaj opiekuna..." value="<?= htmlspecialchars($guardian_name) ?>">
                        </div>

                        <!-- Data Od - Do -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Data od</label>
                                <input type="date" class="form-control form-control-sm" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Data do</label>
                                <input type="date" class="form-control form-control-sm" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                            </div>
                        </div>

                        <!-- Sortowanie -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Sortuj według</label>
                            <select name="sort_by" class="form-select form-select-sm">
                                <option value="date_desc" <?= ($sort_by === 'date_desc') ? 'selected' : '' ?>>Daty dodania (Najnowsze)</option>
                                <option value="date_asc" <?= ($sort_by === 'date_asc') ? 'selected' : '' ?>>Daty dodania (Najstarsze)</option>
                                <option value="guardian_asc" <?= ($sort_by === 'guardian_asc') ? 'selected' : '' ?>>Opiekuna (A-Z)</option>
                                <option value="guardian_desc" <?= ($sort_by === 'guardian_desc') ? 'selected' : '' ?>>Opiekuna (Z-A)</option>
                            </select>
                        </div>

                        <!-- Przyciski akcji -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-bold">Zastosuj filtry</button>
                            <!-- Pusty link działa jak reset formularza wysyłanego metodą GET -->
                            <a href="adoptionsList.php" class="btn btn-outline-danger btn-sm">Wyczyść filtry</a>
                        </div>
                    </form>
                </div>
            </aside>
            
            <!-- Główna sekcja z tabelą wniosków -->
            <section class="col-lg-9">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center">
                    <h2 class="h4 mb-0 fw-bold">Złożone Ankiety Przedadopcyjne</h2>
                </div>
                
                <div class="card border-0 shadow-sm p-3">
                    <?php if($query && $query->num_rows > 0){ ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Wnioskodawca</th>
                                    <th>Osiołek</th>
                                    <th>Opiekun</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Akcja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $query->fetch_assoc()){ 
                                    $badgeColor = 'secondary';
                                    if($row['status'] == 'Oczekujący') $badgeColor = 'warning text-dark';
                                    elseif($row['status'] == 'Zatwierdzony') $badgeColor = 'success';
                                    elseif($row['status'] == 'Odrzucony') $badgeColor = 'danger';
                                    
                                    $guardian = (!empty($row['g_fname']) || !empty($row['g_lname'])) ? htmlspecialchars($row['g_fname'] . ' ' . $row['g_lname']) : '<span class="text-muted small">Brak</span>';
                                ?>
                                <tr>
                                    <td>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($row['created_at'])) ?><br><?= date('H:i', strtotime($row['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['a_fname'] . ' ' . $row['a_lname']) ?></strong>
                                    </td>
                                    <td>
                                        <a href="donkProfile.php?id=<?= $row['donkey_id'] ?>" class="text-decoration-none fw-bold">
                                            <?= htmlspecialchars($row['donkey_name']) ?> 
                                        </a><br>
                                        <small class="text-muted">ID: <?= $row['donkey_id'] ?></small>
                                    </td>
                                    <td>
                                        <?= $guardian ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $badgeColor ?> px-3 py-2 rounded-pill shadow-sm">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="adoptionsDetails.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm px-3 shadow-sm text-white fw-bold">
                                            Szczegóły
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                    <div class="text-center my-4 text-muted">
                        <p class="mb-0">Brak wniosków adopcyjnych spełniających podane kryteria.</p>
                    </div>
                    <?php } ?>
                </div>
            </section>
            
        </div>
    </main>

    <!-- STOPKA -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- SKRYPT AJAX DO PODPOWIEDZI OSIOŁKA -->
    <script>
    $(document).ready(function() {
        let searchInput = $('#donkeySearch');
        let hiddenInput = $('#donkeyId');
        let suggestionsBox = $('#donkeySuggestions');

        searchInput.on('keyup', function() {
            let query = $(this).val().trim();
            hiddenInput.val('');

            if (query.length >= 2) { 
                $.ajax({
                    url: 'ajaxDonkeySearch.php',
                    method: 'GET',
                    data: { q: query },
                    dataType: 'json',
                    success: function(data) {
                        suggestionsBox.empty();
                        if (data.length > 0) {
                            $.each(data, function(index, donkey) {
                                suggestionsBox.append(
                                    '<a href="#" class="list-group-item list-group-item-action donkey-item" data-id="'+donkey.id+'" data-name="'+donkey.name+'">' +
                                    '<strong>' + donkey.name + '</strong> <small class="text-muted">(ID: ' + donkey.id + ')</small>' +
                                    '</a>'
                                );
                            });
                            suggestionsBox.show();
                        } else {
                            suggestionsBox.hide();
                        }
                    }
                });
            } else {
                suggestionsBox.hide();
            }
        });

        $(document).on('click', '.donkey-item', function(e) {
            e.preventDefault();
            let selectedName = $(this).data('name');
            let selectedId = $(this).data('id');
            
            searchInput.val(selectedName); 
            hiddenInput.val(selectedId);   
            suggestionsBox.hide();         
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#donkeySearch, #donkeySuggestions').length) {
                suggestionsBox.hide();
            }
        });
    });
    </script>
</body>
</html>