
<nav class="navbar navbar-expand-lg bg-body sticky-top shadow-sm border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">PrzygarnijOsiołka.pl</a>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-primary" aria-current="page" href="index.php">Nasze Osiołki</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary" aria-current="page" href="news.php">Aktualności</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary" aria-current="page" href="about-us.php">O nas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary" aria-current="page" href="contact.php">Kontakt</a>
                </li>
                <?php if(isset($_SESSION['userLVL']) && $_SESSION['userLVL'] == 2){ ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Zarządzaj
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="register.php">Dodaj użytkownika</a></li>
                        <li><a class="dropdown-item" href="admusers.php">Zarządzaj użytkownikami</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                <?php } ?>
            </ul>
            <?php if(isset($_SESSION['logged']) && $_SESSION['logged'] === true){ ?>
            <span class="navbar-text me-2">Witaj <strong><?=$_SESSION['userFName'].' '.$_SESSION['userLName']; ?></strong></span>
            <a class="btn btn-outline-danger me-2" href="logout.php">Wyloguj</a>
            <?php }else{ ?>
            <a class="btn btn-outline-success me-2" href="login.php">Zaloguj</a>
            <a class="btn btn-outline-primary me-2" href="register.php">Zrejestruj</a>
            <?php } ?>
                
        </div>
    </div>
</nav>
