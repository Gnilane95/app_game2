<?php
require("utils/helpers/functions.php");

/**
 * Get connexion with database
 * 
 * @return PDO 
 */
function getPDO(): PDO
{
    $serveur = "localhost";
    $dbname = "app_game";
    $login = "root";
    $password = "";
    
    try {
        $pdo = new PDO("mysql:host=$serveur;dbname=$dbname", $login, $password, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            // pour ne pas avoir de doublons
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // pour afficher les erreurs
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
        ));
        // affiche message ok connexion
        // echo "connexion établie";
        return $pdo ;
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
}

/**
 * this function return all games in array
 * 
 * @return array 
 */
function getAllGames(): array
{
    $pdo = getPDO() ;
    $sql = "SELECT * FROM jeux ORDER BY name";
    $query = $pdo->prepare($sql);
    $query->execute();
    $games = $query->fetchAll();

    return $games ;
}

/**
 * This function return current id of item
 * @return int
 */
function getID(): int
{
    if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
        // 2- je nettoie mon id contre xss
        $id = clear_xss($_GET['id']);
    } else {
        $_SESSION["error"] = "URL invalide";
        header("location: index.php");
    }
    return $id;
}

/**
 * This function return a single game
 * @return array 
 */
function getGame(): array
{
    $pdo = getPDO() ;
    $id = getID();
    // 3- requette (query in english) vers BDD
    $sql = "SELECT * FROM jeux WHERE id=:id";
    // 4- préparation de la requette
    $query = $pdo->prepare($sql);
    // 5- securiser la requette contre injection sql
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    // 6- executer la requette vers la BDD
    $query->execute();
    // 7- on stock tout ds une variable
    $game = $query->fetch();
    // debug_array($game);
    // $game = [];

    if (!$game) {
        $_SESSION["error"] = "Ce jeu n'est pas disponible.";
        header("location: index.php");
    }
    return $game ;
}

/**
 * This function delete an item
 * @return void 
 */
function delete(): void
{
    $pdo = getPDO() ;
    $id = getID();
    $sql = "DELETE FROM jeux WHERE id=?";
    $query = $pdo->prepare($sql);
    $query->execute([$id]);
    //Redirect
    $_SESSION["success"] = "Le jeu es bien supprimer.";
    header("location:index.php");
}

/**
 * This function create an item
 * @return void 
 */
function create($error): void
{
    $pdo = getPDO() ;
    require_once("utils/secure-form/include.php");
    if (count($error) == 0) {
    $sql = "INSERT INTO jeux(name, price, genre, note, plateforms, description, PEGI, created_at, url_img) VALUES(:name, :price, :genre, :note, :plateforms, :description, :PEGI, NOW(), :url_img)";
    $query = $pdo->prepare($sql);
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':price', $price, PDO::PARAM_STMT);
    $query->bindValue(':note', $note, PDO::PARAM_STMT);
    $query->bindValue(':description', $description, PDO::PARAM_STR);
    $query->bindValue(':genre', implode("|", $genre_clear), PDO::PARAM_STR);
    $query->bindValue(':plateforms', implode("|", $plateforms_clear), PDO::PARAM_STR);
    $query->bindValue(':PEGI', $PEGI, PDO::PARAM_STR);
    $query->bindValue(':url_img', $url_img, PDO::PARAM_STR);
    $query->execute();

    // Redirect
    $_SESSION["success"] = "Le jeux a bien été ajouté";
    header("Location: index.php");
    die;
    }
}