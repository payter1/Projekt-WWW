<?php
require_once 'connect.php';

if(isset($_SESSION['logged']) && $_SESSION['logged'] === true){
    header("Location: index.php");
    exit;
}

if(isset($_POST['login'])){

    $login = htmlspecialchars(trim($_POST['login']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    
    $task = "SELECT * FROM users WHERE `login` = ?";
    $query = $conn -> prepare($task);
    if($query){
        $query -> bind_param("s", $login);
        $query -> execute();
        $result = $query -> get_result();
        if($result -> num_rows === 1){
            $user = $result -> fetch_assoc();
            if(password_verify($password, $user['password'])){
                $_SESSION['logged'] = true;
                $_SESSION['userID'] = $user['id'];
                $_SESSION['userLogin'] = $user['login'];
                $_SESSION['userFName'] = $user['fname'];
                $_SESSION['userLName'] = $user['lname'];
                $_SESSION['userLVL'] = $user['lvl'];
                $dane = $user['login'].'-'.$user['fname'].'-'.$user['lname'].'-'.$user['lvl'];
                $query -> close();
                header("Location: index.php?logged");
            }
        }
    }
    $query->close();
    header("Location: login.php?result=Nieprawidłowa nazwa użytkownika lub hasło");
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
    <main class="container my-5 flex-grow-1" >
        <div class="row g-4 z-2">
            <!-- boczny blok -->
            <div class="col-3"></div>
            <section class="col-lg-6">
                 <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">

                        <!-- alerty -->
                        <?php if(isset($_GET['result']) && $_GET['result'] != 1){?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Błąd: </strong> <?=$_GET['result']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php }?>
                        <!-- // -->

                        <div class="card-body d-flex flex-column p-3 text-center">
                        <!-- POCZĄTEK FORMULARZA Logowania -->
                        <form class="row g-3" action="login.php" method="POST">
                            <img class="mb-4" src="img/ikona.png" alt="">
                            <h1 class="h3 mb-3 fw-normal">Zaloguj się</h1>

                            <div class="form-floating">
                                <input type="text" class="form-control" id="login" name="login" placeholder="Login">
                                <label for="login">Nazwa Użytkownika</label>
                            </div>
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control" id="password" placeholder="Hasło">
                                <label for="password">Hasło</label>
                            </div>

                            <button class="w-100 btn btn-lg btn-primary" type="submit">Zaloguj</button>
                        </form>
                        <!-- KONIEC FORMULARZ Logowania -->
                        </div>
                    </article>
                </div>
            </section>
            <div class="col-3"></div>
        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
    <!-- Bootstrap 5.3 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    'use strict'

    // Pobierz wszystkie formularze, do których chcemy zastosować niestandardowe style walidacji Bootstrap
    const forms = document.querySelectorAll('.needs-validation')

    // Przejdź przez nie i zapobiegaj wysyłaniu, jeśli są błędy
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            // Dodanie klasy 'was-validated' uruchamia wizualne style Bootstrapa (na czerwono/zielono)
            form.classList.add('was-validated')
        }, false)
    })
})
    </script>
</body>
</html>