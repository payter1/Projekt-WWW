<?php
require_once 'connect.php';
if($_SESSION['userLVL'] != 2){
    header("Location: index.php");
}

$task = 'SELECT * FROM users WHERE id='.$_GET['id'];
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
                                    <div class="col-4"><img src="<?=$f['pimg']?>" class="rounded float-start" width="400px" alt="Zdjęcie profilowe"></div>
                                    <div class="col-7">
                                        <dl class="row">
                                            <dt class="col-sm-3">Nazwa użytkownika</dt>
                                            <dd class="col-sm-9">
                                                <?= $f['login']?>
                                            </dd>
                                        </dl>
                                        <dl class="row">
                                            <dt class="col-sm-3">Imię i nazwisko</dt>
                                            <dd class="col-sm-9">
                                                <?= $f['fname'].' '.$f['lname'] ?>
                                            </dd>

                                            <dt class="col-sm-3">Adres e-mail</dt>
                                            <dd class="col-sm-9">
                                                <?= $f['email'] ?>
                                            </dd>

                                            <dt class="col-sm-3">Numer telefonu</dt>
                                            <dd class="col-sm-9">
                                                <?= $f['tel'] ?>
                                            </dd>

                                            <dt class="col-sm-3 text-truncate">Adres</dt>
                                            <dd class="col-sm-9">
                                                <p><?= $f['city'].' '.$f['zip']; ?></p>
                                                <p><?= ' ul. '.$f['street'].' '.$f['hnum'] ?><?= (!empty($f['anum'])) ? '/'.$f['anum'] : ''; ?></p>
                                            </dd>

                                        </dl>
                                    </div>
                                    <div class="col-1">
                                        <a href="editUser.php?id=<?=$_GET['id']?>" class="btn btn-primary">Edytuj</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <hr>
                                    <p><strong>Informacje</strong></p>
                                    
                                    <p><?= $f['info'] ?></p>
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