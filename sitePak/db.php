<?php
//connexion bdd avec méthode msqli

$conn = new mysqli("127.0.0.1", "sandro", "root", "GARAGE");
if($conn->connect_error){
    die("Connexion échouée ! ".$conn->connect_error);
}

  ?>