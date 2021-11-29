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
                $primary_field_indices = array();
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

                        if (in_array($fld_name, $primary_fields)){ //To help construct id, we need to know the indices
                            array_push($primary_field_indices, $i);
                        }
                        $i++;
                        }
                    ?>
                    <th>Edit?</th>
                </thead>

                <tbody>
                    <?php
                    for ($i = 0; $i < $nrows; $i++){
                        $id = "";

                        foreach ($primary_field_indices as $prim_fld_index){
                            $id = $id.$resar[$i][$prim_fld_index];
                        }
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
                            <td>
                                <input type="submit"
                                name="<?php echo $id; ?>"
                                value="Edit"
                                />
                            </td>
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

    function make_relevant_table_for_special_fields($result, $ncols, $nrows, $tablename, $special_flds, $accepted_special_flds, $primary_flds, $value){
        ?>
        <table>
            <thead>
                <tr>
                    <?php
                    $resar = $result->fetch_all();
                    $disgusting_fields = array();
                    $i = 0;
                    $rel_primary_flds_indices = array();
                    $rel_primary_flds = $primary_flds;
                        while ($fld = $result->fetch_field()){
                            $fld_name = $fld->name;
                            if ($rel_primary_flds[$i] && ($rel_primary_flds[$i] == $fld_name)){ //If the relevant primary field exists for the index and if it is equal to the field name.
                                array_push($rel_primary_flds_indices, $i);
                            }

                            if ($accepted_special_flds[$fld_name] || (!$special_flds[$fld_name])){
                            ?>
                                <th>
                                    <?php
                                            echo $fld_name;
                                    ?>
                                </th>
                            <?php
                            }else{
                                $disgusting_fields[$i] = True;
                            }
                            $i++;
                        }
                        
                    ?>
                    <th>Select</th>
                </tr>
            </thead>
            
            <tbody>
                
                <?php
                
                    for ($i = 0; $i < $nrows; $i++){
                        ?>
                        <tr>
                            
                        <?php
                        $id = "";
                        for ($fld_index_rel = 0; $fld_index_rel < count($rel_primary_flds_indices); $fld_index_rel++){
                            $id = $id.$resar[$i][$rel_primary_flds_indices[$fld_index_rel]];
                            //echo $resar[$i][$rel_primary_flds_indices[$fld_index_rel]];
                        }
                        for ($j = 0; $j < $ncols; $j++){
                            if (!$disgusting_fields[$j]){
                                echo "<td>".$resar[$i][$j]."</td>";
                            }
                        }
                        ?>
                        <td>
                            <?php 
                            if ($id == $value){
                            ?>
                                <input type="checkbox"
                                name="checkbox<?php echo $tablename.str_replace(' ', '_', $id); ?>"
                                value="<?php echo $id; ?>"
                                id="<?php echo "checkbox$tablename";?>"
                                checked
                                />
                                <?php
                            }else{
                                ?>
                                <input type="checkbox"
                                name="checkbox<?php echo $tablename.str_replace(' ', '_', $id); ?>"
                                value="<?php echo $id; ?>"
                                id="<?php echo "checkbox$tablename";?>"
                                />
                                <?php
                            }
                            ?>
                        </td>
                        </tr>
                        <?php
                        
                    }
                ?>
                
            </tbody>
        </table>
        <script>
            
            document.querySelectorAll("#checkbox" + "<?php echo $tablename?>").forEach((checkbox) => {
                
                checkbox.addEventListener('click', (event) => {
                    if (checkbox.checked){ //If the checkbox has just been checked to be true.
                        document.querySelectorAll("#checkbox" + "<?php echo $tablename?>").forEach((checkboxother) => {
                            if (checkboxother.value != checkbox.value){ //if it's not the same checkbox
                                checkboxother.checked = false; //make the other checkboxes be unchecked
                            }
                        })
                    }
                    
                })
                
            })
            
        </script>
        <?php
    }
    
    //Relevant variables

    $flds = array(
        'Costume_Types' => array('Costume_Type'),
        'Costumes_Inventory' => array('Costume_Type', 'Costume_Size'),
        'Costumes_Rented' => array('Costume_Type', 'Costume_Size', 'Staff_FirstName', 'Staff_LastName', 'Rental_CheckoutDate', 'Rental_DueDate', 'Rental_ReturnedDate'),
        'Customers' => array('Cust_FirstName', 'Cust_LastName', 'Cust_Address', 'Cust_Phone'), 
        'Orders' => array('Pokemon_Name', 'Cust_FirstName', 'Cust_LastName', 'Inventory_Pokemon_Name', 'Pokemon_Price', 'Order_SoldFor'),
        'Pokemon' => array('Pokemon_Name', 'Pokemon_Type'),
        'Pokemon_Inventory' => array('Pokemon_Name', 'Pokemon_Price'),
        'Sightings' => array('Pokemon_Name', 'Sighting_Location', 'Sighting_Time', 'Sighting_NumPokemon'),
        'Staff' => array('Staff_FirstName', 'Staff_LastName'),
    );

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
?>

<html>
    <head>
    <link rel="stylesheet" href="update.css">
    <link rel="stylesheet" href="../table.css">
    <link rel="stylesheet" href="../different.css">
    <link rel="stylesheet" href="../all.css">
    <?php
        
    ?>
    <title>Update Records</title>
    </head>
    <body>
        
        <div class="navbar">
            <a href="/team/html/TermProject/PHPScripts/readRecordsFiles/readRecords.php" class="readRecords">Read Records</a>
            <a href="/team/html/TermProject/PHPScripts/addRecordsFiles/addRecords.php" class="addRecords">Add Records</a>
            <a href="/team/html/TermProject/PHPScripts/deleteRecordsFiles/deleteRecords.php" class="deleteRecords">Delete Records</a>
            <a href="/team/html/TermProject/PHPScripts/updateRecordsFiles/updateRecords.php" class="updateRecords">Update Records</a>
        </div>
        <h1>Update Records</h1>
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
        <form action="updateRecords.php" method="POST"> <!-- Choose table-->
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

                    $result = $conn->query($query_str);
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
                        ?>
                        
                        <form action="updateRecords.php" method="POST">
                        <?php
                        make_relevant_table($result, $ncols, $nrows, $flds[$table_name], $primary_flds[$table_name]);
                        ?>
                        </form>
                        <?php
                    }else{
                        $rel_flds = $flds2[$table_name]; //Get the array of all the relevant fields.
                        $record = $resar[$i];
                        $_SESSION['id_of_edited_record'] = $prim_keys_for_specific_record;
                        ?>
                        <form action="updateRecords.php" method="POST">
                            <div class="Form">
                                <?php
                                for ($i = 0; $i < count($rel_flds); $i++){
                                    $rel_fld = $rel_flds[$i];
                                    $val = $prim_keys_for_specific_record[$rel_fld];
                                    ?>
                                    <div class="field_name">
                                        <?php
                                        echo $rel_fld.":";
                                        ?>
                                    </div>
                                    <?php
                                    if ($special_flds[$rel_fld]){
                                        $rel_table = $special_flds[$rel_fld];
                                        $query_str = "SELECT * FROM $rel_table;";
                                        $result = $conn->query($query_str);
                                        $ncols = $result->field_count;
                                        $nrows = $result->num_rows;
                                        make_relevant_table_for_special_fields($result, $ncols, $nrows, $rel_table, $special_flds, $accepted_special_flds, $primary_flds[$rel_table], $val);
                                    }else{
                                        ?>
                                        <input type="text" name="<?php echo $id; ?>" placeholder="<?= $mask; ?>" class="textbox_input" value="<?php echo $val; ?>"/>
                                        <?php
                                    }
                                }
                                    ?>
                            </div>
                            <input type="submit" name="Edited" value="Publish Edits"/>
                        </form>
                        <?php
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