<?php
require_once 'connect.php';
if($_SESSION['userLVL'] != 2){
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
        <div class="row">
            <section class="col-lg-12">
                <!-- Pasek narzędziowy -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-primary-subtle border p-3 rounded-3 shadow-sm">
                    Dodaj użytkownika
                </div>
                <div class="row">
                    <div class="col">
                        <article class="card h-100 shadow-sm border-0 position-relative">
                            <div class="card-body d-flex flex-column p-3">
                                <table class="table text-center">
                                    <thead>
                                        <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Login</th>
                                        <th scope="col">Imię</th>
                                        <th scope="col">Nazwisko</th>
                                        <th scope="col">Typ</th>
                                        <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                            $task = " SELECT id, `login`, fname, lname, lvl, `active` FROM users WHERE lvl IS NOT NULL"; 
                                            $query = mysqli_query($conn, $task);
                                            while($f = mysqli_fetch_assoc($query)){
                                        ?>
                                        <tr>
                                        <th scope="row"><?=$f['id']?></th>
                                        <td><a href="userProfile.php?id=<?=$f['id']?>"><?=$f['login']?></a></td>
                                        <td><?=$f['fname']?></td>
                                        <td><?=$f['lname']?></td>
                                        <td><?=($f['lvl'] == 1) ? "Obsługa" : "Administrator"?></td>
                                        <td><?=($f['active'] == 1) ? '<span class="badge text-bg-success">&nbsp;</span>' : '<span class="badge text-bg-danger">&nbsp;</span>' ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        </article>
                    </div>                    
                </div>
            </section>
        </div>
    </main>
    <!-- FOOTER -->
    <?php require_once("partials/footer.php")?>
    <script src="scripts/scripts.js"></script>
</body>
</html>