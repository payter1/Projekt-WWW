<?php
require_once 'connect.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <!-- NAV -->
    <?php require_once("partials/nav.php")?>
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-light bg-opacity-75" >
        <div class="row g-4 z-2">
            
            <!-- boczny blok -->
            <aside class="col-lg-3 d-none d-lg-block ">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px; ">
                    <center><strong>FILTRY</strong></center>
                    <hr>
                    <form action="index.php?filters" method="POST">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="F" id="fem" name="fem" checked>
                    <label class="form-check-label" for="fem">
                        Samica
                    </label>
                    </div>
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="M" id="male" name="male" checked>
                    <label class="form-check-label" for="male">
                        Samiec
                    </label>
                    </div> <br>
                    
                    <label for="minAge" class="form-label">Minimalny wiek</label>
                    <div class="d-flex gap-2 align-items-center mb-3">
                        <input type="range"  class="form-range" min="0" max="50" value="<?=(isset($_POST['minAgeV']) ? $_POST['minAgeV'] : 0)?>" name="minAge" id="minAge">
                         <input type="number" style="width: 70px;" class="form-control form-control-sm" min="0" max="50" value="<?=(isset($_POST['minAgeV']) ? $_POST['minAge'] : 0)?>" for="minAge" id="minAgeV" name="minAgeV" aria-hidden="true">
                    </div>

                    <label for="maxAge" class="form-label">Maksymalny wiek</label>
                    <div class="d-flex gap-2 align-items-center mb-3">
                        <input type="range" class="form-range" min="0" max="50" value="<?=(isset($_POST['maxAgeV']) ? $_POST['maxAgeV'] : 50)?>" name="maxAge" id="maxAge">
                        <input type="number" style="width: 70px;" class="form-control form-control-sm" min="0" max="50" value="<?=(isset($_POST['maxAgeV']) ? $_POST['maxAgeV'] : 50)?>" for="maxAge" id="maxAgeV" name="maxAgeV" aria-hidden="true">
                    </div>
                    <div class="row g-3 align-items-center">
                        <input type="submit" class="btn btn-primary">
                        <a href="index.php" class="btn btn-danger">RESET</a>
                    </div>
                    

                    </form>
                    
                </div>
            </aside>
            <section class="col-lg-9">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    <div class="row justify-content-end">
                        <div class="col-1"></div>
                        <div class="col"><h2>Stajnia</h2></div>
                        <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] != 0){ ?>
                        <div class="col-2"><a href="addDonk.php" class="btn btn-success btn-sm">Dodaj Osła</a></div>
                        <?php } ?>
                    </div>
                    
                    
                    
                    <hr>
                    <form action="index.php" method="GET" class="row g-3">
                    <div class="col-auto">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Wyszukaj po imieniu">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-3">Szukaj</button>
                    </div>
                    </form>
                </div>
                <!-- Siatka dla osłów -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php 
                $filters = '';
                if(isset($_GET['search'])){
                    $phrase = $conn->real_escape_string($_GET['search']);
                    $filters = "AND d.name LIKE '%$phrase%'";
                }elseif(isset($_GET['filters'])){
                    // echo (int)($_POST['minAgeV'] ?? 0);
                    // echo (int)($_POST['maxAgeV'] ?? 50);
                    $minAge = (int)($_POST['minAgeV'] ?? 0);
                    $maxAge = (int)($_POST['maxAgeV'] ?? 50);
                    $minBday = GetBdayRange($minAge)['max'];
                    $maxBday = GetBdayRange($maxAge)['min'];
                    $fChbx = isset($_POST['fem']);
                    $mChbx = isset($_POST['male']);
                    if ($fChbx === $mChbx) {
                        $sex = 2;
                    }elseif ($fChbx) {
                        $sex = $_POST['fem'];
                    } else {
                        $sex = $_POST['male'];
                    }
                    if($sex != 2){
                        $filters = "AND d.sex = '$sex'";
                    }
                    $filters .= " AND bday BETWEEN '$maxBday' AND '$minBday'";
                    
                }
                $task = "SELECT d.*, i.src, i.profile, c.name AS color FROM donkeys d
                LEFT JOIN colors c ON d.color = c.id
                LEFT JOIN img i ON i.id_donk = d.id
                WHERE d.status = 1 AND i.profile = 1 $filters";
                $query = $conn -> query($task);
                while($f = mysqli_fetch_assoc($query)){
                ?>
                <!-- Pojedyncza karta osła (w pętli) -->
                <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">
                        <!-- Górna belka / zdjęcie osła -->
                        <div class="ratio ratio-4x3 bg-secondary-subtle">
                            <img class="w-100 h-100 object-fit-cover" style="" src="<?=$f['src']?>" alt="Zdjęcie Profilowe - <?=$f['name']?>">
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <header class="mb-2">
                                <strong><?=$f['name']?></strong>
                            </header>
                            <div class="mb-3">
                                <?=($f['sex'] == "F") ? "Samica" : "Samiec"?>
                            </div>
                            <div class="mb-3">
                                Wiek: 
                                <?php 
                                $age = GetAge($f['bday']);
                                if($age < 1){
                                    echo "Poniżej roku";
                                }else echo $age;

                                if($age == 1){
                                    echo ' rok';
                                }elseif($age > 1 && $age <=4){
                                    echo ' lata';
                                }elseif($age >= 5){
                                    echo ' lat';
                                }
                                ?>
                            </div>
                            <footer class="mt-auto pt-2 position-relative" style="z-index: 2;">
                                <a href="donkProfile.php?id=<?=$f['id']?>" class="btn btn-info">Zobacz profil</a>
                            </footer>
                        </div>
                    </article>
                </div>
                <!-- Koniec sekcji pojedynczego osła -->
                <?php }?>
                </div>
            </section>

        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
  <script>
const rangeInputMinAge = document.getElementById('minAge');
const rangeOutputMinAge = document.getElementById('minAgeV');
const rangeInputMaxAge = document.getElementById('maxAge');
const rangeOutputMaxAge = document.getElementById('maxAgeV');

// Set initial value
rangeOutputMinAge.value = rangeInputMinAge.value;
rangeOutputMaxAge.value = rangeInputMaxAge.value;

rangeInputMinAge.addEventListener('input', function() {
    // Blokada: Jeśli min wykroczy poza max, ustaw min równe max
    if (parseInt(this.value) > parseInt(rangeInputMaxAge.value)) {
        this.value = rangeInputMaxAge.value;
    }
    rangeOutputMinAge.value = this.value;
});

rangeInputMaxAge.addEventListener('input', function() {
    // Blokada: Jeśli max spadnie poniżej min, ustaw max równe min
    if (parseInt(this.value) < parseInt(rangeInputMinAge.value)) {
        this.value = rangeInputMinAge.value;
    }
    rangeOutputMaxAge.value = this.value;
});
</script>
</body>
</html>