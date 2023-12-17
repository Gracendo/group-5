<?php

/**
 * Class Script
 * Manage generic php scripts
 */
class Script
{
    /**PERFORM JOIN
     *Perform a join query and return the result as an associative array
     * @param $db = active session
     * @param $joinType = join Type
     */
    public static function performJoinQuery($db, $joinType)
    {
        $JT=$joinType;
        switch ($joinType) {
            case 'left':
                $joinQuery = 'SELECT * FROM user LEFT JOIN post ON user.id = post.id';
                break;
            case 'right':
                $joinQuery = 'SELECT * FROM user RIGHT JOIN post ON user.id = post.id';
                break;
            case 'inner':
                $joinQuery = 'SELECT * FROM user INNER JOIN post ON user.id = post.id';
                break;
            default:
                echo "Invalid join type";
                return;
        }
        
        $state = $db->query($joinQuery);
        $state->execute();
        $result = $state->fetchAll(PDO::FETCH_ASSOC);
    
        if ($result) {
            echo '<h2>Join Query Result </h2>';
            echo 'The join Query is '.$joinQuery;
            echo '<table border="1">';
            echo '<tr>';
            foreach (array_keys($result[0]) as $header) {
                echo '<th>' . $header . '</th>';
            }
            echo '</tr>';
            foreach ($result as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>' . $value . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'No results found.';
        }
    }
    
    /**GenerateList
     *
     * Display a list of registered users
     * @param $db = active session
     * @param $name = name of the session user
     */
    public static function GenerateList($db, $name)
    {
        $users = $db->Query('SELECT username, email, joining FROM user ORDER BY joining DESC');
        $users->execute();
        foreach ($users as $r) {
            if ($r['username'] == $name) {
                $list_name = '<h5 class="mb-1"><strong>' . $r['username'] . '</strong></h5>';
                $ind = '<span class="badge badge-info badge-pill" id="badge-' . $name . '">You</span>';
            } else {
                $list_name = '<h5 class="mb-1">' . $r['username'] . '</h5>';
                $ind = '';
            }
            print('<a href="#" id="item-' . $r['username'] . '" class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="d-flex w-100 justify-content-between">' .
                $list_name . '
                                <small>joined on:' . $r['joining'] . '</small>
                            </div>
                            <p class="mb-1">' . $r['email'] . '</p> ' . $ind . '
                        </div>

                    </div>
                </a>');
        }
    }

    /**RedirectToLogin
     *
     * Redirect the user to the Login page
     * @param $session = active session
     */
    public static function RedirectToLogin($session)
    {
        if (!$session->LoggedIn()) {
            $session->Redirect('index.php');
        }
    }

    /**RedirectToHome
     *
     * Redirect the user to the Home page
     * @param $session = active session
     */
    public static function RedirectToHome($session)
    {
        if ($session->LoggedIn()) {
            $session->Redirect('home.php');
        }
    }

    /**Login
     *
     * Check if login is possible and execute it
     * @param $session = active session
     * @param $name = name of the user
     * @param $email = email of the user
     * @param $password = password of the session user
     * @param $msg = output message (success|failure)
     * @return bool     = true if successful
     */
    public static function LoginUser($session, $name, $email, $password, &$msg)
    {
        if ($session->Login($name, $email, $password)) {
            $session->Redirect('home.php');
            return true;
        } else {
            $msg = "Invalid credentials";
        }
        return false;
    }

    /**RegisterUser
     *
     * Check if user registration is possible and execute it
     * @param $session = active session
     * @param $name = name of the user
     * @param $email = email of the user
     * @param $password = password of user
     * @param $msg = output message (success|failure)
     * @return bool     = true if successful
     */
    public static function RegisterUser($session, $name, $email, $password, $retpwd, &$msg)
    {
        if (Script::CheckCredentials($session, $name, $email, $password, $retpwd, $msg)) {
            if ($session->Register($name, $email, $password)) {
                $session->redirect('register.php?joined');
                return true;
            }
        }
        return false;
    }

    /**UpdateUser
     *
     * Check if update is possible and execute it
     * @param $session = active session
     * @param $oldname = old name of the user
     * @param $oldmail = old mail of the user
     * @param $ownpwd = password of session user
     * @param $name = new name of the user
     * @param $email = new email of the user
     * @param $password = new password of the user
     * @param $retpwd = retyped password
     * @param $msg = output message (success|failure)
     * @return bool     = true if successful
     */
    public static function UpdateUser($session, $oldname, $oldmail, $ownpwd, $name, $email, $password, $retpwd, &$msg)
    {
        if (Script::CheckCredentials($session, $name, $email, $password, $retpwd, $msg) || $msg = 'Enter a password!') {
            if ($session->Update($oldname, $oldmail, $ownpwd, $name, $email, $password)) {
                $msg = "User successfully updated";
                return true;
            } else {
                $msg = "Incorrect password!";
            }
        }
        return false;
    }

    /**DeleteUser
     *
     * Check if unregistration is possible and execute it
     * @param $session = active session
     * @param $name = name of the user
     * @param $email = email of the user
     * @param $password = password of the session user
     * @param $msg = output message (success|failure)
     * @return bool     = true if successful
     */
    public static function DeleteUser($session, $name, $email, $password, &$msg)
    {
        try {
            $query = $session->Query("SELECT username, email FROM user WHERE id=:id");
            $query->execute(array(':id' => $_SESSION['user-session']));
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $logout = false;
            if ($name == $row['username']) {
                $logout = true;
            }

            if ($session->Unregister($name, $email, $password)) {
                $msg = "User successfully deleted";
                if ($logout) {
                    $msg = "logout";
                    $session->Redirect('logout.php?logout=true');
                }
                return true;
            } else {
                $msg = "Incorrect password!";
            }
        } catch (PDOException $e) {
            $msg = $e->getMessage();
        }
        return false;
    }

    /**AddUser
     *
     * Check if external user registration is possible and execute it
     * @param $session = active session
     * @param $name = name of the user
     * @param $email = email of the user
     * @param $password = password of user
     * @param $retpwd = retyped password of user
     * @param $ownpwd = password of the session user
     * @param $msg
     * @return bool
     */
    public static function AddUser($session, $name, $email, $password, $retpwd, $ownpwd, &$msg)
    {
        if (Script::CheckCredentials($session, $name, $email, $password, $retpwd, $msg)) {
            if ($session->Add($ownpwd, $name, $email, $password)) {
                $msg = "User successfully added";
                return true;
            } else {
                $msg = "Password incorrect!";
            }
        }
        return false;
    }

    /**CheckCredentials
     *
     * Check user credentials
     * @param $session = active session
     * @param $name = name of the user
     * @param $email = email of the user
     * @param $password = password of user
     * @param $retpwd = retyped password of user
     * @param $msg = output message (success|failure)
     * @return bool     = true if successful
     */
    private static function CheckCredentials($session, $name, $email, $password, $retpwd, &$msg)
    {
        if ($name == "") {
            $msg = "Enter a username!";
        } else if ($email == "") {
            $msg = "Enter an email address!";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Enter a valid email address!';
        } else if ($password == "") {
            $msg = "Enter a password!";
        } else if (strlen($password) < 6) {
            $msg = "Password must be at least 6 characters!";
        } else {
            try {
                $query = $session->Query("SELECT username, email FROM user WHERE username=:name OR email=:email");
                $query->execute(array(':name' => $name, ':email' => $email));
                $row = $query->fetch(PDO::FETCH_ASSOC);

                if ($row['username'] == $name) {
                    $msg = "Username already taken :(";
                } else if ($row['email'] == $email) {
                    $msg = "Email already taken!";
                } else if ($password <> $retpwd) {
                    $msg = "Passwords not coincident!";
                } else {
                    return true;
                }
            } catch (PDOException $e) {
                $msg = $e->getMessage();
            }
        }
        return false;
    }
}