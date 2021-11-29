<?php
    session_start();
    //Start our session or find a session that was already launched previously.
    $reload = False; //Will be used to reload the page when necessary/when post request gets detected.
    
    //Relevant functions
    function bind($statement, $values, $types){
        return $statement->bind_param($types, ...$values);
    }

    function get_sql_script_str($specific_table, $functionality_str){
        return "../../SQLScripts/".$specific_table."/".$functionality_str.$specific_table.".sql";
    }

    function replace_specific_occurence($str, $occurence, $looking_for, $replacement){ //replace the string you're looking for with replacement at when it occurs $occurence times.
        $curr_occurence = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++){
            $char =  substr($str, $i, 1);
            if ($char == $looking_for){
                $curr_occurence++;
            }
            
            if ($curr_occurence >= $occurence){
                return substr_replace($str, $replacement, $i, strlen($looking_for));
            }
        }

        echo "string: $str <br/>";
        return $str;
    }

    function make_relevant_table($result, $ncols, $nrows, $primary_fields){
        if ($nrows == 0){
            $_SESSION['Error'] = 'No records exist.';
            $_SESSION['PreviousTable'] = $_SESSION['tableName'];
            return;
        }
        ?>
        <table>
            <?php
                $resar = $result->fetch_all();
                $i = 0;
                $primary_field_indices = array();
                ?>
                <thead>
                    <?php
                    while ($fld = $result->fetch_field()){
                        $fld_name = $fld->name;
                        ?>
                        <th>
                            <?php 
                                echo $fld_name;
                            ?>
                        </th>
                        <?php
                        $i++;
                    }
                    ?>
                </thead>

                <tbody>
                    <?php
                    for ($i = 0; $i < $nrows; $i++){
                        ?>
                        <tr>
                            <?php
                            for ($j = 0; $j < $ncols; $j++){
                                ?>

                                <td>
                                    <?php
                                        if ($resar[$i][$j] != ''){
                                            echo $resar[$i][$j];
                                        }else{
                                            echo "NULL";
                                        }
                                    ?>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <?php
            ?>
        </table>
        <?php
    }

    //Relevant variables

    $flds = array(
        'Costume_Types' => array('CostumeType'),
        'Costumes_Inventory' => array('Costume_Type', 'Costume_Size'),
        'Costumes_Rented' => array('Staff_ID', 'Costume_ID', 'Rental_CheckoutDate', 'Rental_DueDate'),
        'Customers' => array('Cust_LastName', 'Cust_FirstName', 'Cust_Address', 'Cust_Phone'),
        'Orders' => array('Pokemon_Name', 'Cust_ID', 'Inventory_ID'),
        'Pokemon' => array('Pokemon_Name', 'Pokemon_Type'),
        'Pokemon_Inventory' => array('Pokemon_Name', 'Pokemon_Price'),
        'Sightings' => array('Pokemon_Name', 'Sighting_Location', 'Sighting_Time', 'Sighting_NumPokemon'),
        'Staff' => array('Staff_LastName', 'Staff_FirstName'),
    );

    $primary_flds = array( //All of the primary fields for each table.
        'Costume_Types' => array('Costume_Type'),
        'Costumes_Inventory' => array('Costume_ID'),
        'Costumes_Rented' => array('Staff_ID', 'Costume_ID'),
        'Customers' => array('Cust_ID'),
        'Orders' => array('Order_ID'),
        'Pokemon' => array('Pokemon_Name'),
        'Pokemon_Inventory' => array('Inventory_ID'),
        'Sightings' => array('Pokemon_Name', 'Sighting_Location', 'Sighting_Time'),
        'Staff' => array('Staff_ID'),
    );

    $not_required_fields = array( //All the optional fields
        'Costumes_Rented' => array('Rental_CheckoutDate', 'Rental_DueDate'),
        'Customers' => array('Cust_Phone'),
        'Orders' => array('Inventory_ID', 'Order_SoldFor')
    );
?>

<html>
    <head>
    <link rel="stylesheet" href="read.css">
    <link rel="stylesheet" href="../table.css">
    <link rel="stylesheet" href="../different.css">
    <link rel="stylesheet" href="../all.css">
    <?php
        
    ?>
    <title>Read Records</title>
    </head>
    <body>
    <div class="navbar">
            <a href="/team/html/TermProject/PHPScripts/readRecordsFiles/readRecords.php" class="readRecords">Read Records</a>
            <a href="/team/html/TermProject/PHPScripts/addRecordsFiles/addRecords.php" class="addRecords">Add Records</a>
            <a href="/team/html/TermProject/PHPScripts/deleteRecordsFiles/deleteRecords.php" class="deleteRecords">Delete Records</a>
            <a href="/team/html/TermProject/PHPScripts/updateRecordsFiles/updateRecords.php" class="updateRecords">Update Records</a>
        </div>
        <h1>Read Records</h1>
        <?php 

        $dbse = "PPC";
        $config = parse_ini_file('/home/mysql.ini');
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

        $query_str = "USE PPC;";
        $conn->query($query_str);


        if (isset($_POST['tablesSelector'])){
            $_SESSION['tableName'] = $_POST['tablesSelector'];
            $reload = True;
        }

        
        ?>
        <form action="readRecords.php" method="POST"> <!-- Choose table-->
            <label for="tablesSelector">Table:</label>  
            <!--
                Need a label for the selector becuase it helps to 
                later find the selected table
            -->

            <select name="tablesSelector" id="tableSelector">
                <option value="unselected" selected></option>
                <?php
                    
                $query = "SHOW TABLES;";
                $result = $conn->query($query);
                if (!$result){
                    echo "Failed to show tables";
                }else{
                    while ($table_name = $result->fetch_array()){
                        if ($_SESSION['tableName'] == $table_name[0]){
                        ?>
                        <option selected>
                            <?php echo $table_name[0]; ?>
                        </option>
                        <?php
                        }else{
                        ?>
                        <option>
                            <?php echo $table_name[0]; ?>
                        </option>
                        <?php
                        }
                        
                        
                        }
                    }
                ?>
                </select>
                <button type="submit">Select</button>            
            </form>

            <?php
                $table_name = $_SESSION['tableName'];
                if ($table_name){
                    //If the table has been selected, show all records
                    if (!isset($_POST['Edited'])){
                        $dirc = get_sql_script_str($table_name, 'readRecords');
                        //echo $dirc."<br/>";
                        $query_str = file_get_contents($dirc);
                        //echo $query_str."<br/>";
                        $result = $conn->query($query_str);
                        $ncols = $result->field_count;
                        $nrows = $result->num_rows;
                        make_relevant_table($result, $ncols, $nrows, $primary_flds[$table_name]);
                    }
                }
            ?>
    </body>
</html>

<?php

    if ($_SESSION['Error'] && ($_SESSION['PreviousTable'] == $table_name)){
        $_SESSION['PreviousTable'] = False;
        ?>
        <h4 class="Error">
            <?php
                echo $_SESSION['Error'];
            ?>
        </h4>
        <?php
    }

    if ($reload){
        //Ok, reload the page and redirect to a get request so that post don't repeat
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
        exit();
    }
    $conn->close();
?>