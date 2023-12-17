# GROUP 5
Database implementation project

## Prerequisites 

* MySQL
* PHP 
* MySQL PDO extension 

## To manage the database
You can find the database dump in **cont/facebook.sql**  <br />
Edit **database.php** before starting the server. 

*Example:*

```php
private $host = "localhost";
private $database = "db_name"; 
private $username = "mysql_user";
private $password = "mysql_password";
```
## unlock hidden functionalities
- add this line in line 141 in home.php to implement crud on user <?php include 'add_user.php';?> 
- replace it instead, only if you want to unlock them :)

## ajust the joins depending on the teacher demande
- you can do so just by changing performJoinQuery() function in php/script.php look at the switch case and enjoy, just change the query and the platform would automatically ajust it self...
## Technologies
* [PHP 7.2.3](https://secure.php.net)
* [Bootstrap 4.0.0](https://getbootstrap.com) 
* [jQuery](https://jquery.com)
* [jQuery UI](https://jqueryui.com)
* [Popper](https://popper.js.org)

### credit
for more intresting challenges took at `https://github.com/menoc61`
- you can also get a copy by folking or cloneing the project