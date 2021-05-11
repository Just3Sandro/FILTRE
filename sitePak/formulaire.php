<!DOCTYPE html>
<html lang="en">

<head>
    <link href="css/formulaire-css.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire</title>
</head>

<body>
    <main id="container">
        <header>
            <h1 id="title">Le Questionnaire Du Concessionaire</h1>
            <p id="description">
                Merci de prendre de votre temps pour notre platforme
            </p>
        </header>
        <form action="" method="POST">
            <div class="form-group">
                <label  for="nom">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" placeholder="Entrer votre nom" required />
            </div>
            <div class="form-group">
                <label for="mail">Email</label>
                <input type="email" name="mail" id="email" class="form-control" placeholder="Entrer votre Email"
                    required />
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" name="ville" id="ville" class="form-control" placeholder="Entrer votre ville" required />
            </div>
            <div class="form-group">
                <label  for="cp">Code Postal</label>
                <input type="number" name="cp" id="cp" class="form-control" placeholder="Code Postal" required/>
            </div>
            <div class="form-group">
                <label  for="adresse">Adresse</label>
                <input type="text" name="adresse" id="adresse" class="form-control" placeholder="Entrer votre adresse" />
            </div>
            <div class="form-group">
                <label for="telephone">Numéro de Téléphone</label>
                <input type="number" name="telephone" id="telephone" class="form-control" placeholder="Téléphone" required/>
            </div>

            <div class="form-group">
                <p>Recommandrais-tu notre site à un amis</p>
                <label>
                    <input name="avis" value="oui" type="radio" class="input-radio" checked />Oui
                    </label>
                    <label><input name="avis" value="non" type="radio" class="input-radio" />Non</label>
            </div>

       

            <div class="form-group">
                <p>Un commentaire ?</p>
                <textarea id="comments" class="input-textarea" name="message"
                    placeholder="Ecrit ton commentaire ici..."></textarea>
            </div>

            <div class="form-group">
                <button type="submit" id="submit" class="submit-button">
                    Envoyer
                </button>
            </div>
        </form>
    </main>

    <?php


try{
    $db = new PDO('mysql:host=127.0.0.1;dbname=GARAGE;charset=utf8mb4', 'sandro', 'root');
  }
  catch(PDOException $e){
    print "HOLALALA L'Erreurrr :" . $e->getMessage() . "<br/>";
    die;
  }

$statement = $db->prepare("INSERT INTO messages(nom, adresse, cp, ville, mail, telephone, message, avis) VALUES (:nom, :adresse, :cp, :ville, :mail, :telephone, :message, :avis)");

$statement->execute([
  "nom" => $_POST['nom'],
  "adresse" => $_POST['adresse'],
  "cp" => $_POST['cp'],
  "ville" => $_POST['ville'],
  "mail" => $_POST['mail'],
  "telephone" => $_POST['telephone'],
  "message" => $_POST['message'],
  "avis" => $_POST['avis'],

]);

echo " L'article a bien été ajouté !"




?>
</body>

</html>
