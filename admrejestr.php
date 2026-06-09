<?php
require_once 'connect.php';
if($_SESSION['userLVL'] =! 2){
    header("Location: index.php");
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
                    AKTUALNOŚCI
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                </div>
            </section>
        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>