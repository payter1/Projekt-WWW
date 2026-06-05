<?php
require_once 'connect.php';
if(isset($_POST['login'])){

    $login = htmlspecialchars(trim($_POST['login']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $fname = htmlspecialchars($_POST['fname'], ENT_QUOTES, 'UTF-8');
    $lname = htmlspecialchars($_POST['lname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $tel   = htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8');
    $city  = htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
    $hnum  = htmlspecialchars($_POST['hnum'], ENT_QUOTES, 'UTF-8');
    $anum  = htmlspecialchars($_POST['anum'], ENT_QUOTES, 'UTF-8');
    $zip   = htmlspecialchars($_POST['zip'], ENT_QUOTES, 'UTF-8');

    $isValid = true;
    $result = 0; 
    // WALIDACJA DANYCH
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $login)) {
        $isValid = false;
        $result = 3; // Błąd: Login niepoprawny (złe znaki lub długość inna niż 3-20)
        
    } elseif (strlen($password) < 8) {
        $isValid = false;
        $result = 4; // Błąd: Hasło ma mniej niż 8 znaków
        
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $result = 5; // Błąd: Nieprawidłowy format e-mail
        
    } elseif (strlen($fname) < 2 || strlen($lname) < 2 || strlen($city) < 2 || empty($hnum)) {
        $isValid = false;
        $result = 6; // Błąd: Zbyt krótkie imię, nazwisko, miasto lub brak numeru domu
        
    } elseif (!empty($tel) && !preg_match('/^[0-9]{9,15}$/', $tel)) {
        $isValid = false;
        $result = 7; // Błąd: Telefon podany, ale ma zły format (nie ma 9-15 cyfr)
        
    } elseif (!preg_match('/^[0-9]{2}-[0-9]{3}$/', $zip)) {
        $isValid = false;
        $result = 8; // Błąd: Zły format kodu pocztowego
    }
    // ZAPIS DO BAZY 
    if ($isValid) {
        $task ="INSERT INTO users (`login`, `password`, fname, lname, email, tel, city, hnum, anum, zip)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $conn -> prepare($task);
        $result = 0;
        if($query){
            $query -> bind_param("ssssssssss", 
            $login, $passwordHash, $fname, $lname, $email, $tel, $city, $hnum, $anum, $zip
            );

            if($query -> execute()){
                $result = 1;
            }elseif($query->errno == 1062){
                $result = 2; // 2 - Login lub e-mail jest już zajęty
            } else {
                $result = 0; // 0 - Inny, nieznany błąd
            }
            $query->close();
        }
        header("Location: register.php?result=$result");
        exit;
    }
}
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
        <div class="row g-4 z-2">
            <!-- boczny blok -->
            <aside class="col-lg-3 d-none d-lg-block ">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px; ">
                </div>
            </aside>
            <section class="col-lg-9">
                <!-- Pasek narzędziowy -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm">
                    REJESTRACJA
                </div>
                 <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">
                        <!-- alerty -->
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Błąd</strong> Treść błędu
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <!-- // -->
                        <div class="card-body d-flex flex-column p-3">
                        <!-- POCZĄTEK FORMULARZA REJESTRACJI -->
                        <form class="row g-3 needs-validation" action="register.php" method="POST" novalidate>
                        <div class="col-md-4">
                            <label for="login" class="form-label">Nazwa użytkownika</label>
                            <div class="input-group has-validation">
                                <input type="text" class="form-control" id="login" name="login" aria-describedby="inputGroupPrepend" pattern="^[a-zA-Z0-9_]+$" required>
                                <div class="invalid-feedback">
                                    Wpisz prawidłową nazwę użytkownika(nie może zawierać polskich znaków i spacji)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                        </div>
                        <div class="col-md-5">
                            <label for="fname" class="form-label">Imię</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="col-md-5">
                            <label for="sname" class="form-label">Nazwisko</label>
                            <input type="text" class="form-control" id="sname" name="sname" required>
                        </div>
                        <div class="col-md-5">
                            <label for="email" class="form-label">Adres E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" ^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$ required>
                            <div class="invalid-feedback">
                                Wpisz prawidłowy adres e-mail
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="tel" class="form-label">Numer telefonu</label>
                            <input type="phone" class="form-control" id="tel" name="tel" pattern="[0-9]{9}" maxlength="9">
                            <div class="invalid-feedback">
                                Wprowadź numer telefonu w prawidłowym formacie (XXXXXXXXX)
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label for="city" class="form-label">Miasto</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                            <div class="invalid-feedback">
                                Wprowadź miasto
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="hname" class="form-label">Numer domu</label>
                            <input type="text" class="form-control" id="hname" name="hname" required>
                            <div class="invalid-feedback">
                                Wpisz numer domu
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="anum" class="form-label">Numer mieszkania</label>
                            <input type="text" class="form-control" id="anum" name="anum">
                        </div>
                        <div class="col-md-2">
                            <label for="zip" class="form-label">Kod pocztowy</label>
                            <input type="text" class="form-control" id="zip" name="zip" pattern="[0-9]{2}-[0-9]{3}" maxlength="6" required>
                            <div class="invalid-feedback">
                                Wprowadź prawidłowy kod pocztowy
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Zarejestruj</button>
                        </div>
                        </form>
                        <!-- KONIEC FORMULARZ REJESTRACJI -->
                        </div>
                    </article>
                </div>
            </section>
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