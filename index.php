<?php
header ("Cache-Control: no-cache, must-revalidate");
header ("pragma: no-cache");

try{
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO('mysql:host=localhost:3306;dbname=montres&prestiges;charset=utf8', 'root', '');
    $bdd->query("SET NAMES utf8");

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        switch($_GET['data']){            
            case 'parGenre':
                if (isset($_GET['genre_id'])) {
                    $req = $bdd->prepare("SELECT id,nom,description,date_ajout,quantite,prix,genre_id,type_id,matiere_id,couleur_id,mouvement_id,marque_id,seuilAlerte FROM montres WHERE genre_id = :genre_id");
                    $req->bindParam(':genre_id', $_GET['genre_id'], PDO::PARAM_INT);
                    $req->execute();
                    $result = $req->fetchAll();
                    echo json_encode($result);
                } else {
                    // Si genre_id n'est pas fourni, exécuter la requête avec toutes les montres
                    $req = $bdd->prepare("SELECT id,nom,description,date_ajout,quantite,prix,genre_id,type_id,matiere_id,couleur_id,mouvement_id,marque_id,seuilAlerte FROM montres");
                    $req->execute();
                    $result = $req->fetchAll();
                    echo json_encode($result);
                }
                break;
            
            case 'updateSeuil':
                if (isset($_GET['id']) && isset($_GET['seuilAlerte'])) {
                    $req = $bdd->prepare("UPDATE montres SET seuilAlerte = :seuilAlerte WHERE id = :id");
                    $req->bindParam(':seuilAlerte', $_GET['seuilAlerte'], PDO::PARAM_INT);
                    $req->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
                    $req->execute();
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('success' => false));
                }
            break;
            
            case 'getReaprovisionnement':
                $req = $bdd->prepare("SELECT id,nom,description,date_ajout,quantite,prix,genre_id,type_id,matiere_id,couleur_id,mouvement_id,marque_id,seuilAlerte FROM montres WHERE quantite <= seuilAlerte");
                $req->execute();
                $result = $req->fetchAll();
                echo json_encode($result);
            break;

            case 'infosCommandes':
                if (isset($_GET['id'])) {
                    $req = $bdd->prepare("SELECT id_commande, id_status, libelle, date_status FROM status_passer JOIN status ON status_passer.id_status = status.id WHERE id_commande = :id");
                    $req->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
                    $req->execute();
                    $result = $req->fetchAll();
                    echo json_encode($result);
                } else {
                    throw new Exception('id manquant');
                }
            break;
        }
    }

}catch(Exception $e){
    die('Erreur : '.$e->getMessage());
}

?>