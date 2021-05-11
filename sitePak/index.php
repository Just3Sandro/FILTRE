<?php  require 'db.php'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L'autoroutier</title>

    <!-- on déclare du BOOTSTRAP et du javascript -->
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Déclare du css -->
<link rel="stylesheet" href="css/style.css" >

</head>
<body>
    <div class="text-center text-light bg-info p-2">
        <h3 >L'autoroutier</H3>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"> 
            <h5>Produit Filtrer</h5>
            <hr>
            <!-- Début du formulaire-->
            <form action="" method="POST">
            <!-- Zone de prix avec un prix de base -->
            <div class="col-md-4">
                <label for="">Prix Mini</label>
                <input type="text" name="prix_mini" value="<?php if(isset($_POST['prix_mini'])){echo $_POST['prix_mini']; }else{echo"300";} ?>" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="">Prix Max</label>
                <input type="text" name="prix_max" value="<?php if(isset($_POST['prix_max'])){echo $_POST['prix_max']; }else{echo"71000";} ?>" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="">Click</label>
                <button type="submit" class="btn btn-primary px-4">Filtre</button>
            </div>
            </form>
            

            <h6 class="text-info">Selection du Vehicule</h6>
            <ul class="list-group">
                <!-- séléction du vehiclue-->
                <?php 
                    $sql="SELECT DISTINCT marque FROM vehicule";
                    $result=$conn->query($sql);
                    // ouveture de la boucle while pour afficher toutes les voitures de la bdd
                    while($row=$result->FETCH_ASSOC()){
                ?>
                <li class="list-group-item">
                    <div class="from-check">
                        <label class="form-check-label">
                            <!-- echo du resultat ($row), on lui mes la valeur 'marque', une class et son nom -->
                            <input type="checkbox" class="form-check-input product_check" value="<?= $row['marque']; ?>" class="marque"><?= $row['marque']; ?> 
                        </label>
                    </div>
                </li>
                <!--fermeture boucle while -->
                <?php } ?>
            </ul>

            <h6 class="text-info">Selection de energie</h6>
            <ul class="list-group">
            <!-- même proceder que au-dessus-->
                <?php 
                    $sql="SELECT DISTINCT codeEnergie, libelleEnergie FROM vehicule,energie WHERE vehicule.typeEnergie = energie.codeEnergie";
                    $result=$conn->query($sql);
                    while($row=$result->FETCH_ASSOC()){
                ?>
                <li class="list-group-item">
                    <div class="from-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input product_check" name="energie" value="<?= $row['codeEnergie']; ?>" id="codeEnergie"><?= $row['libelleEnergie']; ?> 
                        </label>
                    </div>
                </li>
                <?php } ?>
            </ul>
            <!-- bouton pour acceder au formulaire-->
            <a class="btn btn-primary" href="formulaire.php" role="button">Suggestion ?</a>

            </div>

            <div class="col-lg-9">
                <h5 class="text-center" id="textChange"> TOUS LES PRODUITS</h5>
                <hr>
                <div class="row" id="result">
                <!-- vérifie que les prix soit bien et on les déclare dans une variable -->
                <?php   
                    if(isset($_POST['prix_mini'])&& isset($_POST['prix_max']))
                    {
                        $petitprix=$_POST['prix_mini'];
                        $grandprix=$_POST['prix_max'];
                    }
                    //On selectionne ce que l'on veut afficher
                    $sql="SELECT * FROM vehicule,energie WHERE vehicule.typeEnergie = energie.codeEnergie AND prixvente BETWEEN $petitprix AND $grandprix";
                    $result=$conn->query($sql);
                    while($row=$result->FETCH_ASSOC()){
                ?> 
                <div class="col-md-3 mb-2">
                    <div class="card-deck">
                        <div class="card border-secondary">
                        <!-- Ajout des images-->
                            <img src="<?= $row['photo']; ?>"class="card-img-top">
                            <div class="card-img-overlay">
                            </div>
                            <!-- Ajout nom de la voiture -->
                            <h6  class="text-light bg-info text-center rounded p-1"><?= $row['description']; ?><h6>

                            <!-- On ajoute des information sur les voitures-->
                            <div class="card-body">
                                <h4 class="card-title text-danger"> Prix: <?= number_format($row['prixvente']); ?>/- </h4>
                                <p>
                                    modele : <?= $row['modele']; ?><br>
                                    Annee : <?= $row['annee']; ?><br>
                                    Kilométrage : <?= $row['kilometrage']; ?><br>
                                    libelleEnergie : <?= $row['libelleEnergie']; ?><br>
                                    marque : <?= $row['marque']; ?><br>
                                    
                                
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- javascript !!-->
    <script type="text/javascript">
    $(document).ready(function(){
        //Si le produit est check
        $('.product_check').click(function(){
           var action = 'data';
           var marque = get_filter_text('marque'); 
           var codeEnergie = get_filter_text('codeEnergie');
                    
           $.ajax({
               url:'action.php',
               method:'POST',
               data:{action:action,marque:marque,codeEnergie:codeEnergie},
               success:function(response){
                   $("#result").html(response);
                   $("#textChange").text("Filtered Products");
               }
           })
        });
        
        function get_filter_text(text_id){
            var filterData = [];
            $('#'+text_id+':checked').each(function(){
                filterData.push($(this).val());
        });
        return filterData;
        }

    });
    </script>

</body>
</html>