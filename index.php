<?php
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szablon E-commerce z Dark Mode</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png');">

    <!-- HEADER: Pasek informacyjny / Topbar -->
    <!-- <header class="bg-dark text-white py-2 small"> -->
        <!-- <div class="container d-flex justify-content-between align-items-center">? -->
            <!-- <div> -->
                <!-- Miejsce na komunikaty marketingowe (np. darmowa dostawa) -->
            <!-- </div> -->
            <!-- <div class="d-flex gap-3"> -->
                <!-- Miejsce na linki pomocnicze (np. logowanie, pomoc) -->
            <!-- </div> -->
        <!-- </div> -->
    <!-- </header> -->

    <!-- NAV: Główna nawigacja, wyszukiwarka, koszyk + PRZEŁĄCZNIK TRYBU -->
    <nav class="navbar navbar-expand-lg bg-body sticky-top shadow-sm border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">PrzygarnijOsiołka.pl</a>
            
            <div class="d-flex order-lg-last gap-2 align-items-center">
                <!-- Przełącznik Dark Mode -->
                <button class="btn btn-link text-secondary me-2 p-2" id="themeToggler" type="button" aria-label="Zmień motyw">
                    <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                </button>

                <!-- Ikona koszyka z licznikiem -->
                <!-- <a href="#" class="btn btn-outline-primary position-relative" aria-label="Koszyk">
                    <i class="bi bi-cart3"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button> -->
            </div>

            <div class="collapse navbar-collapse" id="mainMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                      <a class="nav-link active text-primary" aria-current="page" href="#">Nasze Osiołki</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link text-primary" aria-current="page" href="#">Zarządzaj</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link text-primary" aria-current="page" href="admconf.php">Panel Administratora</a>
                    </li>
                </ul>
                <form class="d-flex col-12 col-lg-4">
                    <li class="nav-item">
                      <button class="btn btn-outline-success" type="submit">Zaloguj</button>
                    </li>
                </form>
            </div>
        </div>
    </nav>

    <!-- MAIN: Główna przestrzeń sklepu -->
    <main class="container my-5 flex-grow-1 bg-light bg-opacity-75" >
        <div class="row g-4 z-2">
            
            <!-- ASIDE: Filtry boczne i nawigacja kategorii -->
            <aside class="col-lg-3 d-none d-lg-block ">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px; ">
                    Typ Osiołka <br>
                    - Rasy??

                </div>
            </aside>

            <!-- SEKCJA: Lista produktów -->
            <section class="col-lg-9">
                
                <!-- Pasek narzędziowy (sortowanie / widok) -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm">
                    Dom Osiołka
                </div>

                <!-- Siatka produktów -->
                <div class="row row-cols-1 row-cols-md-3 g-4">

                <?php 
                $task = "SELECT * FROM donkeys where `status` = 1";
                $query = $conn -> query($task);
                while($f = mysqli_fetch_assoc($query)){
                    $idDonk = $f['id'];
                    // Pobieranie zdjęcia profilowego Osła (PImg - Profile Image)
                    $taskPImg = "SELECT src, `profile` FROM img WHERE id_donk = $idDonk AND `profile`=1";
                    $queryPImg = $conn -> query($taskImg);
                    $fPImg =  mysqli_fetch_assoc($queryPImg);
                
                ?>
                <!-- ARTICLE: Pojedyncza karta osła (w pętli) -->
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

    <!-- FOOTER: Stopka witryny -->
    <footer class="bg-dark text-white-50 pt-5 pb-3 mt-auto border-top border-secondary">
        <div class="container">
            <!-- <div class="row g-4 mb-4"> -->
                <!-- Kolumny stopki: O nas / Pomoc / Płatności / Kontakt -->
            <!-- </div> -->
            
            <!-- <hr class="border-secondary"> -->
            
            <!-- <div class="row align-items-center small"> -->
                <!-- <div class="col-md-6 text-center text-md-start mb-2 mb-md-0"> -->
                    <!-- Prawa autorskie -->
                <!-- </div> -->
                <!-- <div class="col-md-6 text-center text-md-end"> -->
                    <!-- Linki prawne / regulaminy -->
                <!-- </div> -->
            <!-- </div> -->
        </div>
    </footer>

    <!-- Bootstrap 5.3 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- SKRYPT OBSŁUGI DARK MODE (Bootstrap 5.3) -->
    <script>
        (() => {
            'use strict'

            const htmlElement = document.documentElement;
            const themeToggler = document.getElementById('themeToggler');
            const themeIcon = document.getElementById('themeIcon');

            // Funkcja pobierająca aktualny motyw (zapisany lub systemowy)
            const getStoredTheme = () => localStorage.getItem('theme');
            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme();
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            // Funkcja ustawiająca motyw i zmieniająca ikonę
            const setTheme = theme => {
                htmlElement.setAttribute('data-bs-theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-moon-stars-fill text-warning';
                } else {
                    themeIcon.className = 'bi bi-sun-fill text-secondary';
                }
            }

            // Inicjalizacja motywu przy starcie strony
            setTheme(getPreferredTheme());

            // Obsługa kliknięcia w przycisk
            themeToggler.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                localStorage.setItem('theme', newTheme);
                setTheme(newTheme);
            });
        })()
    </script>
</body>
</html>