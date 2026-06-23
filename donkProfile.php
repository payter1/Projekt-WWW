<?php
require_once 'connect.php';
require_once 'functions.php';

$task = "SELECT d.*, d.info AS dInfo, i.src, i.profile, c.name AS color, u.id AS userID, u.fname, u.lname, u.email, u.tel FROM donkeys d
                LEFT JOIN colors c ON d.color = c.id
                LEFT JOIN img i ON i.id_donk = d.id
                LEFT JOIN users u ON d.guardian = u.id
                WHERE d.id=".$_GET['id'].' AND i.profile = 1';
$query = mysqli_query($conn, $task);
$f = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<!-- <head>  -->
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <!-- Panel nawigacyjny -->
    <?php require_once("partials/nav.php")?>
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4" >
        <div class="row mb-4">
            <div class="col text-center">
                <h2 class="text-primary fw-bold">Poznaj mnie!</h2>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner ratio ratio-4x3 bg-secondary">
                            <div class="carousel-item active">
                                <img src="<?=$f['src']?>" alt="Zdjęcie Profilowe - <?=$f['name']?>" class="w-100 h-100 object-fit-cover">
                            </div>
                            <?php 
                            $taskExtraImg = "SELECT * FROM img WHERE `profile` IS NULL AND id_donk =".(int)$_GET['id'];
                            $queryExtraImg = mysqli_query($conn, $taskExtraImg);
                            while($feImg = mysqli_fetch_assoc($queryExtraImg)){
                            ?>
                            <div class="carousel-item">
                                <img src="<?=$feImg['src']?>" alt="Zdjęcie - <?=$f['name']?>" class="w-100 h-100 object-fit-cover">
                            </div>
                            <?php } ?>
                        </div>
                        
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Poprzednie</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Następne</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-7 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="display-5 fw-bold mb-0"><?= $f['name']?></h1>
                    
                    <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] != 0){ ?>
                        <a href="editDonk.php?id=<?=$_GET['id']?>" class="btn btn-outline-secondary btn-sm">Edytuj profil</a>
                    <?php } ?>
                </div>

                <div class="mb-4">
                    <span class="badge text-bg-<?php echo ($f['sex'] == 'F') ? 'danger' : 'primary'; ?> fs-6 me-2 px-3 py-2">
                        <?=($f['sex'] == "F") ? "Samica" : "Samiec"?>
                    </span>
                    <span class="badge text-bg-warning fs-6 me-2 px-3 py-2">
                        Wiek: 
                        <?php 
                        $age = GetAge($f['bday']);
                        if($age < 1) echo "Poniżej roku";
                        else echo $age;
                        if($age == 1) echo ' rok';
                        elseif($age > 1 && $age <=4) echo ' lata';
                        elseif($age >= 5) echo ' lat';
                        ?>
                    </span>
                    <span class="badge text-bg-light border fs-6 px-3 py-2">
                        Maść: <?= $f['color'] ?>
                    </span>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold border-bottom pb-2">O mnie</h5>
                    <p class="text-body-secondary lh-lg" style="text-align: justify;">
                        <?= nl2br($f['dInfo']) ?>
                    </p>
                </div>

                <div class="card bg-primary-subtle border-0 mb-auto rounded-3">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-primary fw-bold">Aktualny dom tymczasowy / Opiekun</h6>
                        <p class="mb-1"><strong><?= $f['fname'].' '.$f['lname'] ?></strong></p>
                        <p class="mb-1 small">E-mail: <a href="mailto:<?= $f['email']?>"><?= $f['email']?></a></p>
                        <p class="mb-0 small">Tel.: <a href="tel:<?= $f['tel']?>"><?= $f['tel']?></a></p>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="adopt.php?id=<?=$_GET['id']?>" class="btn btn-success btn-lg shadow">
                        Chcę adoptować!
                    </a>
                </div>
            </div>
        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>