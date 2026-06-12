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
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png');">
    <!-- Panel nawigacyjny -->
    <?php require_once("partials/nav.php")?>
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-light bg-opacity-75" >
        <div class="row">
            <section class="col-lg-12">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center">
                    <strong>Profil</strong>
                </div>
                <div class="row">
                    <div class="col">
                        <article class="card h-100 shadow-sm border-0 position-relative">
                            <div class="card-body d-flex flex-column p-3">
                                <div class="row">
                                    <div class="col-4">
                                        <div id="carouselExample" class="carousel slide">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="<?=$f['src']?>" alt="Zdjęcie Profilowe - <?=$f['name']?>" class=" w-100" >
                                            </div>
                                            <?php 
                                            $taskExtraImg = "SELECT * FROM img WHERE `profile` IS NULL AND id_donk =".$_GET['id'];
                                            $queryExtraImg = mysqli_query($conn, $taskExtraImg);
                                            while($feImg = mysqli_fetch_assoc($queryExtraImg)){
                                            ?>
                                            <div class="carousel-item">
                                                <img src="<?=$feImg['src']?>" alt="Zdjęcie - <?=$f['name']?>" class=" w-100">
                                            </div>
                                            <?php } ?>
                                            
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>   
                                </div>
                                    <div class="col-7">
                                        <dl class="row">
                                            <dt class="col-sm-3">Imię</dt>
                                            <dd class="col-sm-9">
                                                <?= $f['name']?>
                                            </dd>
                                        </dl>
                                        <dl class="row">
                                            <dt class="col-sm-3">Płeć</dt>
                                            <dd class="col-sm-9">
                                                <?=($f['sex'] == "F") ? "Samica" : "Samiec"?>
                                            </dd>
                                        </dl>
                                        <dl class="row">
                                            <dt class="col-sm-3">Opiekun</dt>
                                            <dd class="col-sm-9">
                                                <p><?= $f['fname'].' '.$f['lname'] ?></p>
                                                <p>E-mail: <?= $f['email']?></p>
                                                <p>Tel.: <?= $f['tel']?></p>
                                            </dd>

                                            <dt class="col-sm-3">Wiek</dt>
                                            <dd class="col-sm-9">
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
                                            </dd>

                                            <dt class="col-sm-3">Kolor</dt>
                                            <dd class="col-sm-9">
                                                <?= $f['color'] ?>
                                            </dd>

                                        </dl>
                                    </div>
                                    <div class="col-1">
                                        <?php 
                                        if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] == 0){
                                        ?>
                                        <a href="editProfile.php?<?=$_GET['id']?>" class="btn btn-primary">Edytuj</a><?php } ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <hr>
                                    <p><strong>Informacje</strong></p>
                                    
                                    <p><?= $f['dInfo'] ?></p>
                                </div>

                            </div>
                        </article>
                    </div>                    
                </div>
            </section>
        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>