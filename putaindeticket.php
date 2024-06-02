<?php
include("db_conn/db_conn.php");

    // Récupérer les noms des entreprises et leurs identifiants
    $query = "SELECT ID_Entreprise, Nom_Entreprise FROM SLY_Entreprises";
    $statement = $db->query($query);
    $entreprises = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier si le formulaire a été soumis
    if (!isset($_POST['submit'])) {
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Créer Ticket</title>
    </head>
    <body>
        <h1>Création de Ticket: ajouter sujet</h1>

        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">

            <div class="wrap-in">
                    <label for="searchInput">Filtre à troupeau</label>
                    <input type="text" id="searchInput" class="beau_gros_champ">

                    <label for="entreprise" class="margin15">Troupeau :</label>
                    <select class="combo-form" name="entreprise" id="entreprise" required onchange="updateEmployees()">
                        <?php foreach ($entreprises as $entreprise): ?>
                            <option value="<?php echo $entreprise['ID_Entreprise']; ?>"><?php echo $entreprise['Nom_Entreprise']; ?></option>
                        <?php endforeach; ?>
                    </select><br><br><br>
            </div>

            <div class="wrap-in">
                    <label for="employe">Mouton :</label>
                    <select name="employe" id="employe" required onchange="updatePhoneNumber(); updateMail();"class="combo-form">
                        <!-- options ajoutées dynamiquement via JavaScript -->
                    </select><br><br>

                    <label for="telephone">Numéro de téléphone :</label>
                    <input type="tel" id="telephone" name="telephone" readonly class="beau_gros_champ"><br><br>

                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" readonly class="beau_gros_champ" size="30px"><br><br>
                </div>

            <label for="demande">Sujet :</label>
            <textarea id="demande" name="demande" rows="8" cols="70" required></textarea>
            <br><br>

            <input type="submit" name="submit" value="Créér">
        </form>
        <script src="scripts/FORM_MAJ_Employe.js"></script>
    </body>
</html>

<?php
    } else {
        // Traitement du formulaire soumis
        try {

            // Requête d'insertion dans la base de données
            $sql = "INSERT INTO SLY_Ticket ( Sujet_Ticket,
                                             ID_Entreprise
                                            ) 
                                    VALUES ( :demande, 
                                            :ID_Entreprise
                                            )
                    ";
            $stmt = $db->prepare($sql); 

        // Liaison des paramètres
        $Sujet_Ticket = filter_input(INPUT_POST, 'demande', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $stmt->bindValue(':demande', $Sujet_Ticket, PDO::PARAM_STR);

        $ID_Entreprise = filter_input(INPUT_POST, 'entreprise', FILTER_SANITIZE_NUMBER_INT);
            $stmt->bindValue(':ID_Entreprise', $ID_Entreprise, PDO::PARAM_INT);

        $db = null;

        $success = $stmt->execute();
        if ($success) {
            echo "Le ticket a bien été créé";
        } else {
            echo "Une erreur s'est produite lors de la création du ticket.";
        }

        } catch (PDOException $e) {
            // En cas d'erreur, affichez un message d'erreur.
            echo "Erreur lors de la création du ticket : " . $e->getMessage();
        } finally {
            // Fermeture de la connexion à la base de données
            $db = null;
        }
    }
?>