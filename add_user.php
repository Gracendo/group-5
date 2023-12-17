<div class="row" id="home-body">
    <!--Users list-->
    <div class="col-md-5">
        <?php
        if(isset($success)){
            ?>
            <!--Success alert-->
            <div class="alert alert-success alert-dismissible" id="alert-success">
                <a href="home.php" class="close" data-dismiss="alert" aria-label="close" id="alert-success-close">&times;</a>
                <strong>Success!</strong> <?php echo $success ?>
            </div>
            <script>
                $('#alert-success').hide().show('slide', {direction: 'up'}, 200);
                $('#div-modify-user').html("");
            </script>
            <?php
            unset($success);
        }
        ?>
        <div class="list-group" id="div-users-list">
            <?php
            Script::GenerateList($user, $row['username']);
            ?>
            <button type="button" class="btn btn-info btn-lg" id="btn-adduser" onclick="AddUser('div-modify-user', false)">+</button>
        </div>
    </div>
    <!--Modify user form-->
    <div class="col-md-7" id="div-modify-user"></div>
</div>