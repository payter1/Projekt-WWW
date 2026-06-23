<nav class="navbar navbar-expand-lg bg-body sticky-top shadow-sm border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">PrzygarnijOsiołka.pl</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Przełącz nawigację">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark" aria-current="page" href="index.php">Nasze Osiołki</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" aria-current="page" href="news.php">Aktualności</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" aria-current="page" href="faq.php">FAQ</a>
                </li>
                
                <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] != 0){ ?>
                <li class="nav-item dropdown ms-lg-3">
                    <a class="nav-link dropdown-toggle text-danger fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill"></i> Panel Obsługi
                    </a>
                    <ul class="dropdown-menu shadow-sm">
                        <li><a class="dropdown-item" href="adoptionsList.php">Lista wniosków adopcyjnych</a></li>
                        
                        <?php if($_SESSION['userLVL'] == 2){ ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="register.php">Dodaj użytkownika</a></li>
                        <li><a class="dropdown-item" href="admusers.php">Zarządzaj użytkownikami</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
            </ul>

            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <?php if(isset($_SESSION['logged']) && $_SESSION['logged'] === true){ ?>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Witaj, <?=$_SESSION['userFName'].' '.$_SESSION['userLName']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <?php if($_SESSION['userLVL'] == 0){ ?>
                            <li><a class="dropdown-item fw-bold text-success" href="myAdoptions.php">Moje wnioski</a></li>
                            <li><hr class="dropdown-divider"></li>
                        <?php } ?>
                        <li><a class="dropdown-item text-danger" href="logout.php">Wyloguj się</a></li>
                    </ul>
                </li>
                
                <?php } else { ?>

                <li class="nav-item me-2 mb-2 mb-lg-0">
                    <a class="btn btn-outline-success w-100" href="login.php">Zaloguj</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary w-100 shadow-sm" href="register.php">Zarejestruj</a>
                </li>
                
                <?php } ?>
            </ul>
                
        </div>
    </div>
</nav>