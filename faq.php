<?php
require_once 'connect.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <!-- Panel nawigacyjny -->
    <?php require_once("partials/nav.php")?>
    
    <!-- Główna przestrzeń -->
    <main class="container my-5 flex-grow-1 bg-white rounded shadow p-4 p-md-5">
        
        <div class="row mb-5 text-center">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-primary fw-bold">Często Zadawane Pytania (FAQ)</h1>
                <p class="text-muted">Znajdź odpowiedzi na najważniejsze pytania dotyczące adopcji, opieki i wymagań dla przyszłych domów naszych osiołków.</p>
                <hr class="w-25 mx-auto mb-0">
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <div class="accordion shadow-sm" id="accordionFAQ">
                    
                    <!-- Pytanie 1 -->
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
                                1. Czy osiołek może mieszkać sam?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted lh-lg">
                                <strong>Zdecydowanie nie.</strong> Osły są zwierzętami wybitnie stadnymi i niezwykle silnie przywiązują się do swoich towarzyszy. Samotny osiołek bardzo szybko wpada w depresję, staje się osowiały i może przestać jeść. Najlepszym towarzyszem dla osła jest drugi osioł. Często adoptowane są w parach. Kucyk lub koń również mogą być dobrymi kompanami, choć ich potrzeby dietetyczne nieco się różnią.
                            </div>
                        </div>
                    </div>

                    <!-- Pytanie 2 -->
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                2. Czym powinnam/powinienem karmić osiołka?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted lh-lg">
                                Dieta osła drastycznie różni się od diety konia. Ich układ pokarmowy jest przystosowany do trawienia bardzo ubogiej, włóknistej roślinności. Podstawą diety osła powinna być <strong>słoma jęczmienna lub owsiana</strong> (nawet 75% dziennego zapotrzebowania) z dodatkiem ubogiego siana. Należy bezwzględnie unikać zielonej, bogatej trawy, pasz treściwych, zbóż i pieczywa, ponieważ prowadzą one do otyłości i groźnego dla życia ochwatu. Smakołykami mogą być marchewka lub gałązki drzew owocowych.
                            </div>
                        </div>
                    </div>

                    <!-- Pytanie 3 -->
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                3. Jakiej opieki weterynaryjnej i kowalskiej wymaga osioł?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted lh-lg">
                                Podobnie jak konie, osły wymagają regularnego odrobaczania (najlepiej poprzedzonego badaniem kału) oraz corocznych szczepień (m.in. tężec, grypa). Niezwykle istotna jest <strong>opieka kowala</strong>. Kopyta osła rosną inaczej niż końskie – są bardziej pionowe i wymagają profesjonalnej korekcji co około 8-10 tygodni.
                            </div>
                        </div>
                    </div>

                    <!-- Pytanie 4 -->
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                4. Jak powinno wyglądać schronienie (wiata/stajnia)?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted lh-lg">
                                Sierść osłów, w przeciwieństwie do sierści końskiej, nie jest wodoodporna. Dlatego osły muszą mieć stały i swobodny dostęp do zadaszonego, suchego schronienia (np. solidnej wiaty trzystronnej lub stajni otwartej z dostępem do wybiegu), w którym będą mogły ukryć się przed deszczem, wiatrem oraz palącym słońcem. Podłoże wewnątrz musi być utwardzone i suche.
                            </div>
                        </div>
                    </div>

                    <!-- Pytanie 5 -->
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                                5. Jak przebiega proces adopcji?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted lh-lg">
                                Pierwszym krokiem jest wypełnienie ankiety przedadopcyjnej, którą znajdziesz na profilu każdego osiołka na naszej stronie. Po analizie formularza skontaktujemy się z Tobą, by omówić szczegóły. Następnie organizowana jest wizyta przedadopcyjna u Ciebie, podczas której sprawdzamy warunki (ogrodzenie, stajnia) i chętnie doradzamy. Ostatnim etapem jest podpisanie umowy i organizacja transportu zwierzaka.
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>