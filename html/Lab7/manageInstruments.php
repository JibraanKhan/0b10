<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?php 
    $dbse = "instrument_rentals";
    $config = parse_ini_file('/home/jibraan/my_sql.ini');
    $conn = new mysqli(
        $config['mysqli.default_host'],
        $config['mysqli.default_user'],
        $config['mysqli.default_pw'],
        $dbse
    );
    if ($conn->connect_errno){
        echo "Error: Failed to make a MySQL connection, here is why: ". "<br/>";
        echo "Errno: " . $conn->connect_errno . "\n";
        echo "Error: " . $conn->connect_error . "\n";
        exit; //Quitting the PHP script if connection failed.
    }

    if (isset($_POST['delete_all'])){
        $contents = file_get_contents('delete_all_instruments.sql');

        if (!$conn->query($contents)){
            echo $conn->error;
            echo "Failed to delete all records.";
        }else{
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
            exit();
        }
    }
    
    if (array_key_exists('add_records', $_POST)){
        $contents = file_get_contents('add_instruments.sql');

        if (!$conn->query($contents)){
            echo $conn->error;
            echo "Failed to insert records!\n";
        }else{
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
            exit();
        }
    }
    $del_stmt = $conn->prepare("DELETE FROM instruments WHERE instrument_id = ?;");
    $del_stmt->bind_param('i', $id);

    $query = "SELECT * FROM instruments;";
    $result = $conn->query($query);
    if (!$result){
        echo "Query failed.";
    }
    $nrows = $result->num_rows;
    $ncols = $result->field_count;
    $resar = $result->fetch_all();
    $rec_added = false;
    for($i=0; $i<$nrows; $i++){
        $id = $resar[$i][0];
        if (isset($_POST["checkbox$id"])){
            $del_stmt->execute();
        }
    }

    for($i=0; $i<$nrows; $i++){
        $id = $resar[$i][0];
        if (isset($_POST["checkbox$id"])){
            $rec_added = true;
        }
    }
    if ($rec_added){
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
        exit();
    }
    ?>
    <h1>Showing All Routes in the Database</h1>
    <h3>By converting a MySQLi result object to an HTML table.</h3>
    <?php
    $result = $conn->query($query);   
    result_to_deletable_table($result);
    $conn->close();
?>
</body>
</html>
<?php

function result_to_deletable_table($res) {
        $resar = $res->fetch_all();
        $nrows = $res->num_rows;
        $ncols = $res->field_count;
    ?> 
    <p>
    <?php echo $ncols; ?> columns, <?php echo $nrows; ?> rows.
    </p>
        <form action="manageInstruments.php" method="POST">
        <table>
        <thead>
        <tr>
        <th>Delete?</th>
    <?php
    while ($fld = $res->fetch_field()) {
    ?>
        <th><?php echo $fld->name; ?></th>
    <?php
    }
    ?>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0;$i<$nrows; $i++) {
        $id = $resar[$i][0];

    ?>
        <tr>
            <td>
                <input type="checkbox"
                name="checkbox<?php echo $id; ?>"
                value=<?php echo $id ?>
                />
            </td>
        <?php
            for ( $j = 0; $j < $ncols; $j++ ) {
        ?>
                <td><?php echo $resar[$i][$j]; 
                ?></td>
        <?php
            }
        ?>        
            </tr>
    <?php
    }
?>
    </tbody>
    </table>
    <input type="submit" value="Delete Selected Records" method="POST"/>
    <br/>
    <input type="submit" name="delete_all" value="Delete All Records" method="POST"/>
    </form>
    <form action="manageInstruments.php" method="POST">
        <input type="submit" name="add_records" value="Add Records" method="POST"/>
    </form>
<?php
}
?>