<?php 
// on connecte notre bdd avec le fichier db.php
require 'db.php';
// vérifie que les données existe bien plus déclaration
if (isset($_POST['action'])){
    $sql = "SELECT * FROM vehicule,energie WHERE vehicule.typeEnergie = energie.codeEnergie AND marque !=''";

    if(isset($_POST['marque'])){
        $marque = implode("','", $_POST['marque']);
        $sql .="AND marque IN('".$marque."')";
    }
 
    if(isset($_POST['codeEnergie'])){
        $codeEnergie = implode("','", $_POST['codeEnergie']);
        $sql .="AND codeEnergie IN('".$codeEnergie."')";
    }
  
    $result = $conn->query($sql);
    $output='';
    //Si une donnée est trouvé on affiche ça
    if($result->num_rows>0){
        while($row=$result->FETCH_ASSOC()){
            $output .='    <div class="col-md-3 mb-2">
            <div class="card-deck">
                <div class="card border-secondary">
                    <img src="'.$row['photo'].'"class="card-img-top">
                    <div class="card-img-overlay">
                    </div>
                    <h6 " class="text-light bg-info text-center rounded p-1">'.$row['description'].'<h6>
                    
                    <div class="card-body">
                        <h4 class="card-title text-danger"> Prix: '.number_format($row['prixvente']).'/-</h4>
                        <p>
                            modele : '.$row['modele'].'<br>
                            Annee : '.$row['annee'].'<br>
                            Kilométrage : '.$row['kilometrage'].'<br>
                            libelleEnergie : ' .$row['libelleEnergie'].'<br>
                            marque : ' .$row['marque'].'<br>
                        
                        </p>
                    </div>
                </div>
            </div>
        </div>
        ';
        }
    }
    // si rien est trouvé on affiche ça 
    else{
        $output = "<h3> PAS DE VEHICULE TROUVé";
    }
    echo $output;
}

?>