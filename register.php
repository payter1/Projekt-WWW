<?php
require_once 'connect.php';
if(isset($_SESSION['logged']) && $_SESSION['logged'] === true && $_SESSION['userLVL'] != 2){
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
    if($_SESSION['userLVL'] == 2){
        $lvl   = $_POST['lvl'];
    }else $lvl = NULL;
    


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
        echo "2=$result\n";
        $query = $conn -> prepare($task);
        $result = 0;
        if($query){
            $query -> bind_param("sssssssssssi", 
            $login, $passwordHash, $fname, $lname, $email, $tel, $city, $street, $hnum, $anum, $zip, $lvl
            );

            if($query -> execute()){
                $result = 1;
            }elseif($query->errno == 1062){
                $result = "Login lub e-mail jest już zajęty";
            } else {
                $result = "Inny, nieznany błąd";
            }
            $query->close();
            header("Location: login.php?result=$result");
            exit;echo "5\n";
        }
    }else header("Location: register.php?result=$result");
    exit;
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
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    REJESTRACJA
                </div>
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
                            <?php if($_SESSION['userLVL'] == 2){ ?>
                                <input type="radio" class="btn-check" name="lvl" id="staff" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="staff">Obsługa</label>

                                <input type="radio" class="btn-check" name="lvl" id="adm" autocomplete="off">
                                <label class="btn btn-outline-primary" for="adm">Administrator</label>
                            <?php } ?>
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">Hasło</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-4">
                            <label for="passwordR" class="form-label">Powtórz hasło</label>
                            <input type="password" class="form-control" id="passwordR" name="passwordR" required>
                            <div class="invalid-feedback">
                                Hasła nie są identyczne!
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label for="fname" class="form-label">Imię</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="col-md-5">
                            <label for="lname" class="form-label">Nazwisko</label>
                            <input type="text" class="form-control" id="lname" name="lname" required>
                        </div>
                        <div class="col-md-5">
                            <label for="email" class="form-label">Adres E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                            <div class="invalid-feedback">
                                Wpisz prawidłowy adres e-mail
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="tel" class="form-label">Numer telefonu</label>
                            <input type="tel" class="form-control" id="tel" name="tel" pattern="[0-9]{9}" maxlength="9">
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
                            <label for="zip" class="form-label">Kod pocztowy</label>
                            <input type="text" class="form-control" id="zip" name="zip" pattern="[0-9]{2}-[0-9]{3}" maxlength="6" required>
                            <div class="invalid-feedback">
                                Wprowadź prawidłowy kod pocztowy
                            </div>
                        </div>
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-5">
                            <label for="street" class="form-label">Ulica</label>
                            <input type="text" class="form-control" id="street" name="street" >
                        </div>
                        <div class="col-md-2">
                            <label for="hnum" class="form-label">Numer domu</label>
                            <input type="text" class="form-control" id="hnum" name="hnum" required>
                            <div class="invalid-feedback">
                                Wpisz numer domu
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="anum" class="form-label">Numer mieszkania</label>
                            <input type="text" class="form-control" id="anum" name="anum">
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
    <script src="scripts/formValidator.js"></script>
    <!-- Bootstrap 5.3 JavaScript Bundle -->
</body>
</html>