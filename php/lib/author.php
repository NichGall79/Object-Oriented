<?php
//require_once("/etc/apache2/capstone-mysql/Secrets.php");
//$secrets =  new Secrets("/etc/apache2/capstone-mysql/cohort28/NichGall79");
//$pdo = $secrets->getPdoObject();
//require_once("/etc/apache2/capstone-mysql/Secrets.php");

require_once (dirname(__DIR__,1)."/Classes/Author.php");
//use Author;
$password = "$\skull_skunk_%year";
$authorHash = password_hash($password, PASSWORD_ARGON2I, ["time_cost" => 45]);
//$authorHash = "hash1235676ghg91gothamhash";

$authorId = "3134e90a-e3a5-4df2-abff-7cc7d8324530";

$authorActivationToken = 'o9AbabiSlayerjkGE9xo9ZFoTGE9x750';

$authorAvatarUrl = "https://avars.discourse.org/v4/letter/m/a8b319/squad4.png";

$authorUsername = "Andre3000";

$authorEmail = "Aundre@cnm.edu";

$author = new NichGall79\ObjectOriented\Author($authorId, $authorActivationToken, $authorAvatarUrl, $authorEmail, $authorHash, $authorUsername);
var_dump($author);
//$author->insert($pdo);
//$authors = Author::getAllAuthor($pdo);
//$authors =  $author->getAllAuthor($pdo);
//var_dump($authors);
//$author->delete($pdo);
//Author::getAllAuthor($pdo);
//$author->getAuthor $authorId);
//$authors->getAllAuthor($pdo);
//var_dump($author->getAuthor($pdo, $authorId));