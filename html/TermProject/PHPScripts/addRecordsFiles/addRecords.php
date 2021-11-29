<html>
    <head>
        <title>
            <?php
            session_start();
            $reload = False;
            ?>
            Add Records
        </title>
        <link rel="stylesheet" href="add.css">
        <link rel="stylesheet" href="../table.css">
        <link rel="stylesheet" href="../different.css">
        <link rel="stylesheet" href="../all.css">
    </head>
    <body>
    <div class="navbar">
            <a href="/team/html/TermProject/PHPScripts/readRecordsFiles/readRecords.php" class="readRecords">Read Records</a>
            <a href="/team/html/TermProject/PHPScripts/addRecordsFiles/addRecords.php" class="addRecords">Add Records</a>
            <a href="/team/html/TermProject/PHPScripts/deleteRecordsFiles/deleteRecords.php" class="deleteRecords">Delete Records</a>
            <a href="/team/html/TermProject/PHPScripts/updateRecordsFiles/updateRecords.php" class="updateRecords">Update Records</a>
        </div>
        <h1 class="Add Records">Add Records</h1>
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
        
        //print_r($_POST);
        ?>
        
        <?php
                    $query = "USE PPC;";
                    $conn->query($query);
        ?>

        <!-- Using the database at this point
    
            Now we just need to make the drop down list for them so they
            can selected a table to add the records to.
    
        -->
        
        <?php

        if ($_POST['tablesSelector']){
            $_SESSION['tableName'] = $_POST['tablesSelector'];
            $reload = True;
        }

        ?>

        <form action="addRecords.php" method="POST"> <!-- Choose table-->
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
        
        

        $flds = array(
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

        $table_types = array(
            'Costume_Types' => array('s'),
            'Costumes_Inventory' => array('s', 's'),
            'Costumes_Rented' => array('i', 'i', 's', 's'),
            'Customers' => array('s', 's', 's', 's'),
            'Orders' => array('s', 'i', 'i', 'd'),
            'Pokemon' => array('s', 's'),
            'Pokemon_Inventory' => array('s', 'd'),
            'Sightings' => array('s', 's', 's', 'i'),
            'Staff' => array('s', 's'),
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
        
        if ($_SESSION['added']){
            ?>
            <div class="Confirmation">
                <p>
                    Successfully added records!
                </p>
            </div>
            <?php
            $_SESSION['added'] = False;
        }

        
        if ($_SESSION['tableName']){
            $selectedTable = $_SESSION['tableName'];
            echo "<h1>$selectedTable</h1>";
            $rel_arr = $flds[$selectedTable]; //Get the array of all the relevant fields.
            if (isset($_POST['AddRecords'])){
                // If records need to be added...
                //echo "Records are being added...<br/>";
                $fld_value_pairs = array();
                $vals_only = array(); //for binding later
                $field_rqs_failed = False;
                $not_required_field_replacement_arr = array(); //Keeps track of which fields need to be set to DEFAULT in the query
                for ($i = 0; $i < count($rel_arr); $i++){

                    $id = $rel_arr[$i];
                    echo $id;
                    $rel_tbl = $special_flds[$id];
                    //echo $id."<br/>";
                    if ((!$rel_tbl || ($rel_tbl == $selectedTable)) && isset($_POST[$id])){
                        //echo "Not special field...<br/>";
                        echo "<br/>";
                        $value = htmlspecialchars(str_replace(' ', '_', $_POST[$id]));
                        $fld = $id;
                        
                        if ($value == ''){
                            if ($not_required_fields[$selectedTable]){
                                //If we found that the table has optional entry fields
                                $not_required_field_arr = $not_required_fields[$selectedTable];
                                if (in_array($fld, $not_required_field_arr)){
                                    echo "$fld isn't required";
                                    $position_of_default_insertion = $i + 1;
                                    //echo $position_of_default_insertion;
                                    array_push($not_required_field_replacement_arr, $position_of_default_insertion);
                                    //print_r($not_required_field_replacement_arr);
                                }else{
                                    //field is required and no value was found for it.
                                    ?>
                                    <div class="Error">
                                        <p>
                                            Please choose a value for all required fields!
                                        </p>
                                    </div>
                                    <?php
                                    $field_rqs_failed = True;
                                    echo "Requirements failed:".$field_rqs_failed;
                                    break;
                                }
                            }else{
                                //No optional entry fields
                                ?>
                                    <div class="Error">
                                        <p>
                                            Please choose a value for all required fields!
                                        </p>
                                    </div>
                                <?php
                                $field_rqs_failed = True;
                                break;
                            }
                        }else{
                            $fld_value_pairs[$fld] = $value;
                            $vals_only[$i] = $value;
                            echo $value.$i.$vals_only[$i];
                        }
                    }elseif ($rel_tbl){
                        //The field is a foreign key
                        $fld = $id;
                        $query_str = "SELECT * FROM $rel_tbl;";
                        //echo "query:".$query_str;
                        $result = $conn->query($query_str);
                        $resar = $result->fetch_all();
                        $nrows = $result->num_rows;
                        if ($result){
                            $fld_index = 0;
                            
                            while ($fld_rel = $result->fetch_field()){
                                if ($id == $fld_rel->name){
                                    break;
                                }
                                $fld_index++;
                            }
                            for ($j = 0; $j < $nrows; $j++){
                                $rel_val = $resar[$j][$fld_index]; // The specific record and the specific value
                                $rel_val = str_replace(' ', '_', $rel_val);
                                
                                
                                if (isset($_POST["checkbox".$rel_tbl.$rel_val])){
                                    //echo "found checked box";
                                    $value = str_replace(' ', '_', $_POST["checkbox".$rel_tbl.$rel_val]);
                                    //echo "<br/>checkbox real value:".$value."<br/>";
                                    $fld_value_pairs[$fld] = $value;
                                    $vals_only[$i] = $value;
                                }
                            }
                            if (!$fld_value_pairs[$fld]){
                                echo "<br/>Value doesn't exist for $fld<br/>";
                                //Now check if the value needs to be specified, if so,
                                //tell the user they need to specify the value for the field.
                                //Otherwise, set the value to be default.
                                if ($not_required_fields[$selectedTable]){
                                    //If the table has an optional field
                                    $not_required_field_arr = $not_required_fields[$selectedTable];
                                    if (in_array($fld, $not_required_field_arr)){
                                        //The field isn't required
                                        echo $fld." isn't required.";
                                        $position_of_default_insertion = $i + 1;
                                        array_push($not_required_field_replacement_arr, $position_of_default_insertion);
                                    }else{
                                        //The field is required.
                                    ?>
                                        <div class="Error">
                                            <p>
                                                Please choose a value for all required fields!
                                            </p>
                                        </div>
                                    <?php
                                        $field_rqs_failed = True;
                                        break;
                                    }
                                }else{
                                    //The table requires all fields 
                                    ?>
                                        <div class="Error">
                                            <p>
                                            Please choose a value for all required fields!
                                            </p>
                                        </div>
                                    <?php
                                    $field_rqs_failed = True;
                                    break;
                                }
                                
                            }

                            if ($field_rqs_failed){
                                
                                break;
                            }
                        }
                    }
                }
                echo "<br/> <br/>";
                $contents = file_get_contents(get_sql_script_str($selectedTable, "addRecords"));
                $types = $table_types[$selectedTable];
                echo "<br/>";
                $i = 0;
                foreach ($not_required_field_replacement_arr as $occurence){
                    $contents = replace_specific_occurence($contents, $occurence - $i, '?', 'DEFAULT');//replace the string you're looking for with replacement at when it occurs $occurence times.
                    unset($types[$occurence-1]);
                    $i++;
                }
                $types = implode('', $types);
                
                echo "<br/>";
                foreach($fld_value_pairs as $key=>$value){
                    // Each field and value for the specific record.
                    echo "field:$key, value:$value <br/>";
                }
                echo $str;
                echo "<br/> <br/> <br/> <br/>".$contents."<br/>";
                echo "<br/> <br/> <br/> <br/>";
                for ($i = 0; $i < count($vals_only); $i++){
                    echo "Value$i: ".$vals_only[$i]."<br/>";
                }
                if ($field_rqs_failed){
                    $_SESSION['fields_left_unspecified'] = $_SESSION['tableName'];
                    
                }else{
                    echo "<br/>";
                    $stmt = $conn->prepare("$contents");
                    //$vals_only = array('Pikachu', 'Lightning');
                    echo $contents;
                    //bind($stmt, $vals_only, 'ss');
                    bind($stmt, $vals_only, $types);
                    $results = $stmt->execute();
                    if (!$results){
                        ?>
                        <div class="ErrorSpec">
                            <p>
                                <?php
                                    echo "Failed to insert records.";
                                    if ($conn->error){
                                        echo "<br/>Error: ".$conn->error."<br/>";
                                    }
                                ?>
                            </p>
                        </div>
                        <?php
                    }else{
                        //$reload = true;
                        $_SESSION['added'] = true;
                    }
                }
                
            }else{
                //Records aren't being added but rather options need to shown
                if ($_SESSION['fields_left_unspecified'] == $_SESSION['tableName']){
                    ?>
                    <div class="Error">
                        <p>
                            Last time you left some fields unspecified that need to be specified, please make sure to specify all required fields!
                        </p>
                    </div>
                    <?php
                    $_SESSION['fields_left_unspecified'] = False;
                }
                ?>

                <form action="addRecords.php" method="POST" class="fields">

                <?php
                for ($i = 0; $i < count($rel_arr); $i++){
                    $id = $rel_arr[$i];
                ?>
                    <div class="fieldInputs">
                        <!-- FieldName: InputBox Div-->
                        <div className="fieldName">
                            <h4>
                                <?php 
                                    echo $rel_arr[$i].":";

                                    if ($not_required_fields[$selectedTable]){
                                        if (!(in_array($rel_arr[$i], $not_required_fields[$selectedTable]))){
                                            ?>
                                            <div class="requiredField">
                                                *
                                            </div>
                                            <?php
                                        }
                                    }else{
                                        ?>
                                        <div class="requiredField">
                                            *
                                        </div>
                                        <?php 
                                    }
                                ?>
                                
                            </h4>
                        </div>
                        

                        <?php 
                            if ((!$special_flds[$id]) || ($special_flds[$id] == $selectedTable)){ //if the field isn't a foreign key or if the field is not a primary key.
                                $mask = $masks[$id];
                                
                        ?>
                                <input type="text" name="<?php echo $id; ?>" placeholder="<?= $mask; ?>"/>
                        <?php
                            }else{
                                $rel_table = $special_flds[$id];
                                $query_str = "SELECT * FROM $rel_table;";
                                $result = $conn->query($query_str);
                                $ncols = $result->field_count;
                                $nrows = $result->num_rows;
                                make_relevant_table($result, $ncols, $nrows, $rel_table, $special_flds, $accepted_special_flds, $primary_flds[$rel_table]);
                            }
                        ?>
                    </div>
                    <?php
            }
                ?>
                <input type="submit" name="AddRecords"/>
                </form>
                
                <?php
            }

        }
        
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

        function make_relevant_table($result, $ncols, $nrows, $tablename, $special_flds, $accepted_special_flds, $primary_flds){
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
                                <input type="checkbox"
                                name="checkbox<?php echo $tablename.str_replace(' ', '_', $id); ?>"
                                value="<?php echo $id; ?>"
                                id="<?php echo "checkbox$tablename";?>"
                                />
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
        
        ?> 

        
        <?php

        if ($reload){
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
            exit();
        }
        $conn->close();
        ?>
    </body>
</html>