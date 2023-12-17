<?php
require_once("session.php");
require_once("php/user.php");
require_once("php/script.php");
$user = new User();
$id = $_SESSION['user-session'];

$query = $user->Query("SELECT * FROM user WHERE id=:id");
$query->execute(array(":id" => $id));
$row = $query->fetch(PDO::FETCH_ASSOC);

/** On update button click  */
if (isset($_POST['btn-update'])){
    $msg='';
    $oldname = explode('_',$_POST['btn-update'])[0];
    $oldmail = explode('_',$_POST['btn-update'])[1];
    $name = $_POST['txt-username'];
    $email = $_POST['txt-email'];
    $password = $_POST['txt-newpwd'];
    $retpwd = $_POST['txt-retpwd'];
    $ownpwd = strip_tags($_POST['txt-pwd']);
    if(Script::UpdateUser($user, $oldname, $oldmail, $ownpwd, $name, $email, $password, $retpwd, $msg)){
        $user->Redirect('home.php?updated');
    }
    else{
        $user->Redirect('home.php?err');
        $_SESSION['err'] = 'mod_'.$msg;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
    }
}

/** On delete button click  */
if (isset($_POST['btn-delete'])){
    $msg='';
    $name = explode('_',$_POST['btn-delete'])[0];
    $email = explode('_',$_POST['btn-delete'])[1];
    $password = strip_tags($_POST['txt-pwd']);
    if(Script::DeleteUser($user, $name, $email, $password,$msg)){
        if($msg<>'logout') {
            $user->Redirect('home.php?deleted');
        }
    }
    else{
        $user->Redirect('home.php?err');
        $_SESSION['err'] = 'del_'.$msg;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
    }
}

/** On add button click  */
if (isset($_POST['btn-add'])){
    $msg='';
    $name = $_POST['txt-username'];
    $email = $_POST['txt-email'];
    $password = $_POST['txt-newpwd'];
    $retpwd = $_POST['txt-retpwd'];
    $ownpwd = strip_tags($_POST['txt-pwd']);
    if(Script::AddUser($user, $name, $email, $password, $retpwd, $ownpwd, $msg)){
        $user->Redirect('home.php?joined');
    }
    else{
        $user->Redirect('home.php?err');
        $_SESSION['err'] = 'add_'.$msg;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
    }
}

if(isset($_GET['joined'])){
    $success = "User successfully registered";
}
if(isset($_GET['deleted'])){
    $success = "User successfully deleted";
}
if(isset($_GET['updated'])){
    $success = "User successfully updated";
}
if(isset($_GET['err'])){
    $error = $_SESSION['err'];
    $name= $_SESSION['name'];
    $email= $_SESSION['email'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome, <?php print($row['username']); ?>!</title>
    <link rel="icon" href="cont/favicon.png">
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
            integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
            crossorigin="anonymous"></script>
    <script src="js/script.js" type="text/javascript"></script>
</head>

<body>
<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="home-navbar">
    <!--Title-->
    <a class="navbar-brand" id="home-title" href="home.php">GROUP 5</a>
    <!--Collapse buttons-->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <!--Home-->
            <li class="nav-item active">
                <a class="nav-link" href="home.php">Home<span class="sr-only">(current)</span></a>
            </li>
        </ul>
        <!--Right side buttons-->
        <form class="form-inline my-2 my-lg-0">
            <a class="btn btn-info my-2 my-sm-0" href="logout.php?logout=true">Logout</a>
        </form>
    </div>
</nav>
<!--Body-->
<!-- welcome user -->
<div class="col-md-4">
        <div class="card text-left border-info">
            <div class="card-header bg-info text-white">Info</div>
            <div class="card-body">
                <h3 class="card-title">Welcome <?php print($row['username']); ?> 👋🏾</h3>
                <p class="card-text"><?php print($row['email']); ?></p>
            </div>
            <div class="card-footer text-muted">joined on <?php print($row['joining']); ?></div>
        </div>
</div>
<!-- crud on user functionality replace this div <php include 'add_user.php'?> with :) try and see my wonderfull work-->
<div class="list-group" id="div-users-list">
    <?php
    Script::GenerateList($user, $row['username']);
    ?>
</div>

    <?php
    // Assuming $db is your database connection
    $joinType = isset($_GET['joinType']) ? $_GET['joinType'] : '';

    // Call performJoinQuery method from the Script class
    Script::performJoinQuery($user, $joinType); // This will directly output the HTML table for the specified join type
    ?>

<!-- Add buttons for different join queries -->
<div class="col-md-4">
    <h4>Perform Joins:</h4>
    <button onclick="performJoin('left')" class="btn btn-info btn-lg">Left Join</button>
    <button onclick="performJoin('right')" class="btn btn-info btn-lg">Right Join</button>
    <button onclick="performJoin('inner')" class="btn btn-info btn-lg">Inner Join</button>
</div>

<script>
    let joinResultData = ''; // Variable to store join query results

    function performJoin(joinType) {
        // AJAX call to execute the PHP function
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Store the fetched data in joinResultData variable
                joinResultData = this.responseText;

                // Open a new tab and populate it with the fetched join query results
                var newTab = window.open('');
                newTab.document.write('<html><head><meta charset="UTF-8"><title>Welcome, <?php print($row['username']); ?>!</title></head><body>');
                newTab.document.write('<div>' + joinResultData + '</div>');
                newTab.document.write('</body></html>');
                newTab.document.close();
            }
        };
        
        // Send a request to the backend PHP script with the selected join type
        xhttp.open("GET", "join.php?joinType=" + joinType, true);
        xhttp.send();
    }
</script>



<script>
    <?php
    /** On Error */
    if(isset($error)){
        $type = explode('_', $error)[0];
        $err_msg = explode('_', $error)[1];
        if($type == 'mod' || $type =='del'){
    ?>
            ModifyUser(<?php echo '"' . $name . '","' . $email . '","div-modify-user", true'; ?>);
    <?php
        }
        else{
            ?>
            AddUser("div-modify-user", true);
    <?php
        }
    ?>
    ErrorAlert('form-modify-user', '<?php echo $err_msg ?>', 'txt-pwd');
    <?php
    unset($error);
    }
    ?>
</script>
</body>
</html>
