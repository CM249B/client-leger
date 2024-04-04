<?php

session_start();

if(!isset($_SESSION['ffff'])){
    header("Location:../login.php");
}
if(empty($_SESSION['ffff'])){
    header("Location:../login.php");
}



    require("../config/commandes.php");

    $Produits=afficher()

?>





<!DOCTYPE html>
<html>
<head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


    <title> Tous les produits</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">monoshop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <a class="nav-link" aria-current="page" href="../admin/">Nouveau</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="supprimer.php">Suppression</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="afficher.php" style="font-weight:bold">Produits</a>
                </li>
            </ul>
            <div style="diplay: flex; justify-content: flex-end;">
                <a href="deconnexion.php" class="btn btn-danger">Se deconnecter</a>
            </div>
            </div>
        </div>
    </nav>


    <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">



            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Image</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Prix</th>
                        <th scope="col">Description</th>
                        <th scope="col">Editer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($Produits as $Produit): ?>

                        <tr>
                        <th scope="row"><?= $Produit->id ?></th>
                        <td>
                            <img src="<?= $Produit ->image ?>" style="width: 18%">
                        </td>
                        <td><?= $Produit->nom ?></td>
                        <td  style="font-weight: bold; color: green;"><?= $Produit->prix ?>€</td>
                        <td><?= substr($Produit-> description, 0, 100); ?>...</td>
                        <td>
                            <a href="editer.php?pdt=<?= $Produit->id ?>"><i class="fa fa-pencil" style="font-size: 150%"></i></a>
                        </td>
                        </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

