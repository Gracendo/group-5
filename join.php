<?php 
require_once("php/database.php");
require_once("php/script.php");

$database = new Database();
$user = $database->Connect();
// Check if the connection is successful before proceeding
if ($user) {
    if (isset($_GET['joinType'])) {
        $joinType = $_GET['joinType'];
        Script::performJoinQuery($user, $joinType);
    } else {
        echo "Invalid request on joinType";
    }
} else {
    echo "Failed to establish a database connection.";
}

?>