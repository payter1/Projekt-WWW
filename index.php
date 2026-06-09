<?php
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png');">
    <!-- NAV -->
    <?php require_once("partials/nav.php")?>
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-light bg-opacity-75" >
        <div class="row g-4 z-2">
            
            <!-- boczny blok -->
            <aside class="col-lg-3 d-none d-lg-block ">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px; ">
                    Typ Osiołka <br>
                    - Rasy??
                </div>
            </aside>
            <section class="col-lg-9">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    Dom Osiołka
                </div>
                <!-- Siatka dla osłów -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php 
                $task = "SELECT * FROM donkeys where `status` = 1";
                $query = $conn -> query($task);
                while($f = mysqli_fetch_assoc($query)){
                    $idDonk = $f['id'];
                    // Pobieranie zdjęcia profilowego Osła (PImg - Profile Image)
                    $taskPImg = "SELECT src, `profile` FROM img WHERE id_donk = $idDonk AND `profile`=1";
                    $queryPImg = $conn -> query($taskPImg);
                    $fPImg =  mysqli_fetch_assoc($queryPImg);
                ?>
                <!-- Pojedyncza karta osła (w pętli) -->
                <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">
                        <!-- Górna belka / zdjęcie osła -->
                        <div class="ratio ratio-4x3 bg-secondary-subtle">
                            <img src="<?=$fPImg['src']?>" alt="Zdjęcie Profilowe - <?=$f['name']?>">
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <header class="mb-2">
                                <?=$f['name']?>
                            </header>
                            <div class="mb-3">
                                <?=$f['bday']?>
                            </div>
                            <footer class="mt-auto pt-2 position-relative" style="z-index: 2;">
                                <div class="btn btn-info">Przygarnij osołka</div>
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
</body>
</html>