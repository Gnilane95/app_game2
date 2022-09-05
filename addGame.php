<!-- header -->
<?php
// start session
session_start();
$title = "Add Game"; //title for current page
include("models/database.php");

$error = [];
$errorMessage = "<span class=text-red-500>*Ce champs est obligatoire</span>";


// 1-je verifie si le formulaire est soumis
if (!empty($_POST["submited"]) && isset($_FILES["url_img"]) && $_FILES["url_img"]["error"] == 0) {
    create($error);
}
require("view/addPage.php");
?>
