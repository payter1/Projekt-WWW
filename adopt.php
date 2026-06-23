<?php
require_once 'connect.php';
require_once 'functions.php';

// Zabezpieczenie - tylko zalogowani mogą adoptować
if(!isset($_SESSION['logged']) || $_SESSION['logged'] !== true){
    header("Location: login.php?result=Aby adoptować osiołka, musisz się zalogować.");
    exit;
}

$userID = $_SESSION['userID']; 
$donkeyID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Pobieranie informacji o osiołku do formularza
$donkeyName = "Nieznany osiołek";
if($donkeyID > 0) {
    $taskDonkey = "SELECT name FROM donkeys WHERE id = $donkeyID";
    $queryDonkey = mysqli_query($conn, $taskDonkey);
    if($fDonk = mysqli_fetch_assoc($queryDonkey)){
        $donkeyName = $fDonk['name'];
    } else {
        $donkeyID = 0; 
    }
}

// Obsługa wysłania formularza
if(isset($_POST['adopt_submit'])){
    $purpose = htmlspecialchars(trim($_POST['purpose']), ENT_QUOTES, 'UTF-8');
    $experience = htmlspecialchars(trim($_POST['experience']), ENT_QUOTES, 'UTF-8');
    $housing = htmlspecialchars(trim($_POST['housing']), ENT_QUOTES, 'UTF-8');
    $fencing = htmlspecialchars(trim($_POST['fencing']), ENT_QUOTES, 'UTF-8');
    $diet_knowledge = htmlspecialchars(trim($_POST['diet_knowledge']), ENT_QUOTES, 'UTF-8');
    $other_animals = htmlspecialchars(trim($_POST['other_animals']), ENT_QUOTES, 'UTF-8');
    $caretaker_time = htmlspecialchars(trim($_POST['caretaker_time']), ENT_QUOTES, 'UTF-8');
    $vacation_care = htmlspecialchars(trim($_POST['vacation_care']), ENT_QUOTES, 'UTF-8');
    $finances = htmlspecialchars(trim($_POST['finances']), ENT_QUOTES, 'UTF-8');
    $transport = htmlspecialchars(trim($_POST['transport']), ENT_QUOTES, 'UTF-8');
    
    $vet_agreement = isset($_POST['vet_agreement']) ? 1 : 0;
    $inspection_agreement = isset($_POST['inspection_agreement']) ? 1 : 0;
    $d_id = (int)$_POST['donkey_id'];

    $isValid = true;
    $resultMsg = "";

    // Walidacja - sprawdzamy czy żadne pole nie jest puste
    if(empty($purpose) || empty($experience) || empty($housing) || empty($fencing) || empty($diet_knowledge) || 
       empty($other_animals) || empty($caretaker_time) || empty($vacation_care) || empty($finances) || empty($transport)) {
        $isValid = false;
        $resultMsg = "Prosimy o wypełnienie wszystkich pól w ankiecie.";
    } elseif(!$vet_agreement || !$inspection_agreement) {
        $isValid = false;
        $resultMsg = "Musisz wyrazić zgodę na warunki adopcji (wizyty i opieka weterynaryjna/kowalska).";
    } elseif($d_id == 0) {
        $isValid = false;
        $resultMsg = "Błąd identyfikacji osiołka.";
    }

    if($isValid){
        $task = "INSERT INTO adoptions (user_id, donkey_id, purpose, experience, housing, fencing, diet_knowledge, other_animals, caretaker_time, vacation_care, finances, transport, vet_agreement, inspection_agreement) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($task);
        if($stmt){
            // 2 inty, 10 stringów, 2 inty
            $stmt->bind_param("iissssssssssii", $userID, $d_id, $purpose, $experience, $housing, $fencing, $diet_knowledge, $other_animals, $caretaker_time, $vacation_care, $finances, $transport, $vet_agreement, $inspection_agreement);
            
            if($stmt->execute()){
                header("Location: donkProfile.php?id=$d_id&msg=Wniosek adopcyjny został złożony!");
                exit;
            } else {
                $resultMsg = "Wystąpił błąd podczas zapisu do bazy: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="auto">
<?php require_once("partials/html-head.php")?>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary" style="background-image: url('img/bg.png'); background-attachment: fixed; background-size: cover; background-position: center;">
    <?php require_once("partials/nav.php")?>
    
    <main class="container my-5 flex-grow-1 bg-light bg-opacity-75">
        <div class="row g-4 z-2">
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="card p-4 shadow-sm sticky-top bg-primary-subtle" style="top: 90px;">
                    <h5 class="text-center">Proces Adopcji</h5>
                    <hr>
                    <p class="small text-muted">Wypełnienie ankiety to pierwszy i najważniejszy krok. Pamiętaj, że osły to zwierzęta wymagające specyficznej diety i stałego towarzystwa. Po analizie formularza skontaktujemy się z Tobą telefonicznie.</p>
                </div>
            </aside>
            
            <section class="col-lg-9">
                <div class="mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm text-center fw-bold">
                    ANKIETA PRZEDADOPCYJNA
                </div>
                
                <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative">
                        
                        <?php if(isset($resultMsg) && $resultMsg != ""){?>
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <strong>Błąd: </strong> <?=$resultMsg?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php }?>

                        <?php if($donkeyID == 0){ ?>
                        <div class="alert alert-warning m-3" role="alert">
                            Nie wybrano osiołka do adopcji. Wróć do <a href="index.php" class="alert-link">stajni</a> i wybierz podopiecznego.
                        </div>
                        <?php } else { ?>

                        <div class="card-body d-flex flex-column p-3">
                            <h5 class="mb-4 text-center">Wnioskujesz o adopcję osiołka o imieniu: <span class="text-primary"><?=$donkeyName?></span></h5>
                            <hr>

                            <form class="row g-3 needs-validation" action="adopt.php?id=<?=$donkeyID?>" method="POST" novalidate>
                                <input type="hidden" name="donkey_id" value="<?=$donkeyID?>">

                                <div class="col-12">
                                    <label for="purpose" class="form-label fw-bold">1. Jaki jest główny cel adopcji osiołka?</label>
                                    <div class="form-text mt-0 mb-2">Np. towarzystwo dla konia/innego osła, pupil domowy, agroturystyka, kosiarka do trawy.</div>
                                    <input type="text" class="form-control" id="purpose" name="purpose" value="<?=htmlspecialchars($_POST['purpose'] ?? '')?>" required>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="experience" class="form-label fw-bold">2. Jakie masz doświadczenie w opiece nad zwierzętami gospodarskimi, a w szczególności osłami/końmi?</label>
                                    <textarea class="form-control" id="experience" name="experience" rows="2" required><?=htmlspecialchars($_POST['experience'] ?? '')?></textarea>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="housing" class="form-label fw-bold">3. Opisz dokładnie warunki, w jakich będzie stacjonował osiołek.</label>
                                    <div class="form-text mt-0 mb-2">Wymiary stajni/wiaty, dostęp do światła, rodzaj podłoża i ściółki.</div>
                                    <textarea class="form-control" id="housing" name="housing" rows="2" required><?=htmlspecialchars($_POST['housing'] ?? '')?></textarea>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="fencing" class="form-label fw-bold">4. Jakiej wielkości jest wybieg/pastwisko i z jakiego materiału wykonane jest ogrodzenie?</label>
                                    <div class="form-text mt-0 mb-2">Podaj również orientacyjną wysokość ogrodzenia.</div>
                                    <textarea class="form-control" id="fencing" name="fencing" rows="2" required><?=htmlspecialchars($_POST['fencing'] ?? '')?></textarea>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="diet_knowledge" class="form-label fw-bold">5. Czym zamierzasz żywić osiołka?</label>
                                    <div class="form-text mt-0 mb-2">Podaj podstawę diety oraz ewentualne dodatki/smakołyki. Pamiętaj, że osły mają inne potrzeby żywieniowe niż konie.</div>
                                    <textarea class="form-control" id="diet_knowledge" name="diet_knowledge" rows="2" required><?=htmlspecialchars($_POST['diet_knowledge'] ?? '')?></textarea>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="other_animals" class="form-label fw-bold">6. Jakie inne zwierzęta posiadasz?</label>
                                    <div class="form-text mt-0 mb-2">Osły to zwierzęta wybitnie stadne i źle znoszą samotność. Z kim będzie przebywał adoptowany osioł?</div>
                                    <textarea class="form-control" id="other_animals" name="other_animals" rows="2" required><?=htmlspecialchars($_POST['other_animals'] ?? '')?></textarea>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="caretaker_time" class="form-label fw-bold">7. Kto będzie głównym opiekunem zwierzęcia i ile czasu dziennie może mu poświęcić?</label>
                                    <input type="text" class="form-control" id="caretaker_time" name="caretaker_time" value="<?=htmlspecialchars($_POST['caretaker_time'] ?? '')?>" required>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="vacation_care" class="form-label fw-bold">8. Kto zajmie się osiołkiem w przypadku Twojego dłuższego wyjazdu lub choroby?</label>
                                    <input type="text" class="form-control" id="vacation_care" name="vacation_care" value="<?=htmlspecialchars($_POST['vacation_care'] ?? '')?>" required>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="finances" class="form-label fw-bold">9. Czy jesteś świadomy(a) kosztów utrzymania osła?</label>
                                    <div class="form-text mt-0 mb-2">Koszty obejmują m.in. regularne wizyty kowala, szczepienia, odrobaczanie, paszę. Jaki budżet miesięczny na to zakładasz?</div>
                                    <textarea class="form-control" id="finances" name="finances" rows="2" required><?=htmlspecialchars($_POST['finances'] ?? '')?></textarea>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12">
                                    <label for="transport" class="form-label fw-bold">10. W jaki sposób zamierzasz przetransportować osiołka do nowego domu?</label>
                                    <div class="form-text mt-0 mb-2">Czy posiadasz własny transport (przyczepę do przewozu koni), czy zamierzasz go wynająć?</div>
                                    <input type="text" class="form-control" id="transport" name="transport" value="<?=htmlspecialchars($_POST['transport'] ?? '')?>" required>
                                    <div class="invalid-feedback">To pole jest wymagane.</div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-check bg-light p-3 border rounded">
                                        <input class="form-check-input ms-1" type="checkbox" id="vet_agreement" name="vet_agreement" required>
                                        <label class="form-check-label ms-2 fw-bold text-danger" for="vet_agreement">
                                            Zobowiązuję się do zapewnienia osiołkowi regularnej opieki weterynaryjnej (odrobaczanie, szczepienia) oraz profesjonalnej opieki kowalskiej (korekcja kopyt min. co 8-10 tygodni).
                                        </label>
                                        <div class="invalid-feedback">Zgoda na opiekę jest wymagana.</div>
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <div class="form-check bg-light p-3 border rounded">
                                        <input class="form-check-input ms-1" type="checkbox" id="inspection_agreement" name="inspection_agreement" required>
                                        <label class="form-check-label ms-2 fw-bold text-danger" for="inspection_agreement">
                                            Wyrażam zgodę na wizytę przedadopcyjną oraz ewentualne niezapowiedziane wizyty poadopcyjne ze strony przedstawicieli serwisu.
                                        </label>
                                        <div class="invalid-feedback">Zgoda na wizyty jest wymagana.</div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-4 text-end">
                                    <button class="btn btn-success btn-lg px-5 shadow" type="submit" name="adopt_submit">Wyślij wniosek adopcyjny</button>
                                </div>
                            </form>
                            </div>
                        <?php } ?>
                    </article>
                </div>
            </section>
        </div>
    </main>

    <?php require_once("partials/footer.php")?>
    <script src="scripts/formValidator.js"></script>
</body>
</html>