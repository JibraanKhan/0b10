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

    function make_relevant_table($result, $ncols, $nrows, $fields, $primary_fields){
        if ($nrows == 0){
            $_SESSION['Error'] = 'No records exist.';
            $_SESSION['PreviousTable'] = $_SESSION['tableName'];
            return;
        }
        ?>
        <table>
            <?php
                $resar = $result->fetch_all();
                $field_indices = array();
                $i = 0;
                ?>
                <thead>
                    <?php
                    while ($fld = $result->fetch_field()){
                        $fld_name = $fld->name;
                        //print_r($fields);
                        //echo $fld_name;
                        if (in_array($fld_name, $fields)){
                            array_push($field_indices, $i)
                            ?>
                            
                            <th>
                                <?php 
                                    echo $fld_name;
                                ?>
                            </th>
                        <?php
                        }
                        
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
                                if (in_array($j, $field_indices)){
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

    

    $flds2 = array(
        'Costume_Types' => array('Costume_Type'),
        'Costumes_Inventory' => array('Costume_Type', 'Costume_Size'),
        'Costumes_Rented' => array('Staff_ID', 'Costume_ID', 'Rental_CheckoutDate', 'Rental_DueDate'),
        'Customers' => array('Cust_LastName', 'Cust_FirstName', 'Cust_Address', 'Cust_Phone'),
        'Orders' => array('Pokemon_Name', 'Cust_ID', 'Inventory_ID', 'Order_SoldFor'),
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

    $special_flds = array( //All the fields that need a table representation to show records to choose
        'Cust_ID' => 'Customers',
        'Inventory_ID' => 'Pokemon_Inventory',
        'Staff_ID' => 'Staff',
        'Costume_ID' => 'Costumes_Inventory',
        'Pokemon_Name' => 'Pokemon',
        'Costume_Type' => 'Costume_Types'
    );

    $accepted_special_flds = array( //All fields that I want them to be able to change.
        'Pokemon_Name' => 'Pokemon',
        'Costume_Type' => 'Costume_Types'
    );

    $masks = array( //All of the place holders that show them what to enter into the text box.
        'CostumeType' => 'Meowth Costume, Office Jenny Costume',
        'Costume_Size' => 'S, M, L, XL',
        'Rental_CheckoutDate' => 'YYYY-MM-DD hh:mm:ss',
        'Rental_DueDate' => 'YYYY-MM-DD hh:mm:ss',
        'Cust_LastName' => 'Smith',
        'Cust_FirstName' => 'John',
        'Cust_Address'=> 'House #30, Veridian Forest',
        'Cust_Phone' => '(502)982-5012',
        'Pokemon_Name'=> 'Charmander, Pikachu',
        'Pokemon_Type' => 'Fire, Water, Grass',
        'Pokemon_Price' => '5.50, 6.25, 6',
        'Sighting_Location' => 'Cerulean City',
        'Sighting_Time' => 'YYYY-MM-DD hh:mm:ss',
        'Sighting_NumPokemon' => '5, 2',
        'Staff_LastName' => 'Smith',
        'Staff_FirstName' => 'John',
        'Order_SoldFor' => '5.50, 6.25, 6',
    );

    $not_required_fields = array( //All the optional fields
        'Costumes_Rented' => array('Rental_CheckoutDate', 'Rental_DueDate'),
        'Customers' => array('Cust_Phone'),
        'Orders' => array('Inventory_ID', 'Order_SoldFor')
    );

    $flds = array(
        'Costume_Types' => array('Costume'),
        'Costumes_Inventory' => array('Costume', 'Size'),
        'Costumes_Rented' => array('Costume', 'Size', 'Staff_FirstName', 'Staff_LastName', 'CheckoutDate', 'DueDate', 'ReturnedDate'),
        'Customers' => array('FirstName', 'LastName', 'Address', 'Phone'), 
        'Orders' => array('Ordered_Pokemon', 'Customers_FirstName', 'Customers_LastName', 'Inventory_Pokemon', 'Price', 'SoldFor'),
        'Pokemon' => array('Pokemon_Species', 'Type'),
        'Pokemon_Inventory' => array('Pokemon_Species', 'Price'),
        'Sightings' => array('Pokemon_Species', 'Location_Sighted', 'Time_Sighted', 'Number_Of_Pokemon_Sighted'),
        'Staff' => array('FirstName', 'LastName'),
    );

    $query_strs = array(
        'Costume_Types' => 'SELECT Costume_Type as Costume FROM Costume_Types;',
        'Costumes_Inventory' => 'SELECT Costume_Type as Costume, Costume_Size as Size FROM Costumes_Inventory;',
        'Costumes_Rented' => 'SELECT Rental_CheckoutDate as CheckoutDate, Rental_DueDate as DueDate, Rental_ReturnedDate as ReturnedDate, Staff_FirstName, Staff_LastName, Costume_Type as Costume, Costume_Size as Size FROM Costumes_Rented LEFT JOIN Staff USING (Staff_ID) LEFT JOIN Costumes_Inventory USING (Costume_ID);',
        'Customers' => 'SELECT Cust_FirstName as FirstName, Cust_LastName as LastName, Cust_Address as Address, Cust_Phone as Phone FROM Customers;',
        'Orders' => 'SELECT Orders.Pokemon_Name as Ordered_Pokemon, Order_SoldFor as SoldFor, Cust_FirstName as Customers_FirstName, Cust_LastName as Customers_LastName, Pokemon_Inventory.Pokemon_Name as Inventory_Pokemon, Pokemon_Price as Price FROM Orders LEFT JOIN Customers USING (Cust_ID) LEFT JOIN Pokemon_Inventory USING (Inventory_ID);',
        'Pokemon' => 'SELECT Pokemon_Name as Pokemon_Species, Pokemon_Type as Type FROM Pokemon;',
        'Pokemon_Inventory' => 'SELECT Pokemon_Name as Pokemon_Species, Pokemon_Price as Price FROM Pokemon_Inventory;',
        'Sightings' => 'SELECT Pokemon_Name as Pokemon_Species, Sightings_Location as Location_Sighted, Sightings_Time as Time_Sighted, Sightings_NumPokemon as Number_Of_Pokemon_Sighted FROM Sightings;',
        'Staff' => 'SELECT Staff_FirstName as FirstName, Staff_LastName as LastName FROM Staff;'
    );
?>

<html>
    <head>
    <link rel="stylesheet" href="update.css">
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

        if (isset($_POST['hi'])){
            echo "<h1>Hi!</h1>";
        }
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
                    $dirc = get_sql_script_str($table_name, 'readRecords');
                    $query_str = file_get_contents($dirc);
                    $result = $conn->query($query_str);
                    $ncols = $result->field_count;
                    $nrows = $result->num_rows;
                    $resar = $result->fetch_all();
                    $edit = False;
                    $primary_field_indices = array();
                    $i = 0;
                    $prim_keys_for_specific_record = array(); //The dictionary of primary key field and value that is currently being edited
                    $flds_by_indices = array();
                    while ($fld = $result->fetch_field()){
                        $fld_name = $fld->name;
                        if (in_array($fld_name, $primary_flds[$table_name])){ //To help construct id, we need to know the indices
                            array_push($flds_by_indices, $fld_name);
                            array_push($primary_field_indices, $i);
                        }
                        $i++;
                    }
                    
                    for ($i = 0; $i < $nrows; $i++){
                        $id = "";
                        foreach ($primary_field_indices as $prim_fld_index){
                            $id = $id.$resar[$i][$prim_fld_index];
                            $prim_keys_for_specific_record[$flds_by_indices[$prim_fld_index]] = $resar[$i][$prim_fld_index];
                        }
                        if (isset($_POST[$id])){
                            $edit = True;
                            break;
                        }
                    }

                    $result = $conn->query($query_strs[$table_name]);
                    if (!$edit || $_POST['Edited']){ //If they are not trying to edit a record or if a file just got edited.
                        //echo "<br/>Table Name: ".$table_name."<br/>";
                        
                        // Check if there were any edited fields from the edit
                        if ($_POST['Edited']){
                            // A record was just edited, apply all changes.
                            //Must first locate the record that was edited
                            $record;
                            $ids_dict = $_SESSION['id_of_edited_record'];
                            $read_query_str = "SELECT * FROM $table_name;";
                            $result_find_record = $conn->query($read_query_str);
                            $resar = $result_find_record->fetch_all();
                            $primary_fld_indices = array();
                            $i = 0;
                            while ($fld = $result_find_record->fetch_field()){
                                $fld_name = $fld->name;
                                if ($ids_dict[$fld_name]){
                                    array_push($primary_fld_indices, $i);
                                }
                                $i++;
                            }
                            print_r($primary_fld_indices);
                            //print_r($ids_dict[$fld_name]);
                        }                        
                        make_relevant_table($result, $ncols, $nrows, $flds[$table_name], $primary_flds[$table_name]);
                        
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