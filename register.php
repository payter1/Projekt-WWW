<?php
require_once 'connect.php';

// Blokada wejścia dla zalogowanych (chyba że to admin - poziom 2)
if(isset($_SESSION['logged']) && $_SESSION['logged'] === true && (!isset($_SESSION['userLVL']) || $_SESSION['userLVL'] != 2)){
    header("Location: index.php");
    exit;
}

if(isset($_POST['login'])){

    $login = htmlspecialchars(trim($_POST['login']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $passwordR = $_POST['passwordR'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $fname   = htmlspecialchars($_POST['fname'], ENT_QUOTES, 'UTF-8');
    $lname   = htmlspecialchars($_POST['lname'], ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $tel     = htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8');
    $city    = htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
    $street  = htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8');
    $hnum    = htmlspecialchars($_POST['hnum'], ENT_QUOTES, 'UTF-8');
    $anum    = htmlspecialchars($_POST['anum'], ENT_QUOTES, 'UTF-8');
    $zip     = htmlspecialchars($_POST['zip'], ENT_QUOTES, 'UTF-8');
    
    if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] == 2){
        $lvl = $_POST['lvl'];
    } else {
        $lvl = NULL;
    }

    $isValid = true;
    $result = 0; 
    
    // WALIDACJA DANYCH
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $login)) {
        $isValid = false;
        $result = "Login niepoprawny (złe znaki lub długość inna niż 3-20)";
        
    } elseif (strlen($password) < 3) {
        $isValid = false;
        $result = "Hasło ma mniej niż 3 znaków";
        
    } elseif ($password !== $passwordR) {
        $isValid = false;
        $result = "Podane hasła nie są identyczne";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $result = "Nieprawidłowy format e-mail";
        
    } elseif (strlen($fname) < 2 || strlen($lname) < 2 || strlen($city) < 2 || empty($hnum)) {
        $isValid = false;
        $result = "Zbyt krótkie imię, nazwisko, miasto lub brak numeru domu";
        
    } elseif (!empty($tel) && !preg_match('/^[0-9]{9,15}$/', $tel)) {
        $isValid = false;
        $result = "Telefon podany, ale ma zły format (nie ma 9-15 cyfr)";
        
    } elseif (!preg_match('/^[0-9]{2}-[0-9]{3}$/', $zip)) {
        $isValid = false;
        $result = "Zły format kodu pocztowego";
    }
    
    // ZAPIS DO BAZY 
    if ($isValid) {
        $task ="INSERT INTO users (`login`, `password`, fname, lname, email, tel, city, street, hnum, anum, zip, lvl)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $query = $conn->prepare($task);
        $result = 0;
        if($query){
            $query->bind_param("sssssssssssi", 
                $login, $passwordHash, $fname, $lname, $email, $tel, $city, $street, $hnum, $anum, $zip, $lvl
            );

            if($query->execute()){
                $result = 1;
            } elseif($query->errno == 1062){
                $result = "Login lub e-mail jest już zajęty";
            } else {
                $result = "Inny, nieznany błąd";
            }
            $query->close();
            header("Location: login.php?result=$result");
            exit;
        }
    } else {
        header("Location: register.php?result=$result");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4">
        <div class="row g-4 z-2">
            
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px;">
                    <h5 class="text-center fw-bold">Dołącz do nas!</h5>
                    <hr>
                    <p class="small text-muted mb-3">Rejestracja w serwisie Przygarnij Osiołka pozwoli Ci na składanie wniosków adopcyjnych oraz zarządzanie swoimi danymi.</p>
                    <ul class="small text-muted ps-3">
                        <li>Szybka adopcja</li>
                        <li>Historia zgłoszeń</li>
                        <li>Wsparcie fundacji</li>
                    </ul>
                </div>
            </aside>
            
            <section class="col-lg-9">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    REJESTRACJA UŻYTKOWNIKA
                </div>
                
                <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">

                        <?php if(isset($_GET['result']) && $_GET['result'] != 1){?>
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <strong>Błąd: </strong> <?=$_GET['result']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php }?>
                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="mb-4 text-center">Wprowadź swoje dane</h5>
                            <hr class="mb-4">

                            <form class="row g-3 needs-validation" action="register.php" method="POST" novalidate>
                                
                                <div class="col-md-4">
                                    <label for="login" class="form-label fw-bold small">Nazwa użytkownika</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="login" name="login" aria-describedby="inputGroupPrepend" pattern="^[a-zA-Z0-9_]+$" required>
                                        <div class="invalid-feedback">
                                            Brak spacji i polskich znaków.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-8 d-flex align-items-end">
                                    <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] == 2){ ?>
                                        <div class="btn-group w-100 shadow-sm" role="group" aria-label="Poziom uprawnień">
                                            <input type="radio" class="btn-check" name="lvl" id="staff" value="1" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="staff">Obsługa</label>

                                            <input type="radio" class="btn-check" name="lvl" id="adm" value="2" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="adm">Administrator</label>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="col-md-6 mt-4">
                                    <label for="password" class="form-label fw-bold small">Hasło</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="col-md-6 mt-4">
                                    <label for="passwordR" class="form-label fw-bold small">Powtórz hasło</label>
                                    <input type="password" class="form-control" id="passwordR" name="passwordR" required>
                                    <div class="invalid-feedback">
                                        Hasła nie są identyczne!
                                    </div>
                                </div>

                                <div class="col-12 mt-4"><hr></div>

                                <div class="col-md-6">
                                    <label for="fname" class="form-label fw-bold small">Imię</label>
                                    <input type="text" class="form-control" id="fname" name="fname" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="lname" class="form-label fw-bold small">Nazwisko</label>
                                    <input type="text" class="form-control" id="lname" name="lname" required>
                                </div>
                                
                                <div class="col-md-7">
                                    <label for="email" class="form-label fw-bold small">Adres E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                                    <div class="invalid-feedback">
                                        Wpisz prawidłowy adres e-mail.
                                    </div>
                                </div>
                                
                                <div class="col-md-5">
                                    <label for="tel" class="form-label fw-bold small">Numer telefonu</label>
                                    <input type="tel" class="form-control" id="tel" name="tel" pattern="[0-9]{9}" maxlength="9">
                                    <div class="invalid-feedback">
                                        Wprowadź 9 cyfr (bez spacji).
                                    </div>
                                </div>

                                <div class="col-12 mt-4"><hr></div>

                                <div class="col-md-9">
                                    <label for="city" class="form-label fw-bold small">Miasto</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                    <div class="invalid-feedback">
                                        Wprowadź miasto.
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="zip" class="form-label fw-bold small">Kod pocztowy</label>
                                    <input type="text" class="form-control" id="zip" name="zip" pattern="[0-9]{2}-[0-9]{3}" maxlength="6" required placeholder="00-000">
                                    <div class="invalid-feedback">
                                        Wymagany format: XX-XXX.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="street" class="form-label fw-bold small">Ulica</label>
                                    <input type="text" class="form-control" id="street" name="street">
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="hnum" class="form-label fw-bold small">Numer domu</label>
                                    <input type="text" class="form-control" id="hnum" name="hnum" required>
                                    <div class="invalid-feedback">
                                        Wpisz numer domu.
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="anum" class="form-label fw-bold small">Nr mieszkania</label>
                                    <input type="text" class="form-control" id="anum" name="anum" placeholder="Opcjonalnie">
                                </div>
                                
                                <div class="col-12 mt-5 text-end">
                                    <button class="btn btn-success btn-lg px-5 shadow-sm" type="submit">Zarejestruj się</button>
                                </div>
                            </form>
                            </div>
                    </article>
                </div>
            </section>
        </div>
    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
    </body>
</html>