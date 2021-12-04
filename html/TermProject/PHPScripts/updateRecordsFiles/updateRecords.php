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

        //echo "string: $str <br/>";
        return $str;
    }

    function make_relevant_table($result, $fields, $primary_fields){
        $ncols = $result->field_count;
        $nrows = $result->num_rows;
        if ($nrows == 0){
            $_SESSION['Error'] = 'No records exist.';
            $_SESSION['PreviousTable'] = $_SESSION['tableName'];
            return;
        }
        ?>
        <table class="table is-spaced is-bordered is-fullwidth is-striped is-hoverable">
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
                        //echo "Field:$fld_name<br/>";
                        //print_r($primary_fields);
                        if (in_array($fld_name, $primary_fields)){ //To help construct id, we need to know the indices
                            //echo "In array";
                            array_push($primary_field_indices, $i);
                        }
                        $i++;
                        }
                    //print_r($field_indices);
                    ?>
                    <th>Edit?</th>
                </thead>

                <tbody>
                    <?php
                    //print_r($primary_field_indices);
                    for ($i = 0; $i < $nrows; $i++){
                        $id = "";
                        foreach ($primary_field_indices as $prim_fld_index){
                            $id = $id.$resar[$i][$prim_fld_index];
                        }
                        //echo "ID:$id";

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
                            $id = str_replace(' ', '_', $id);
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

    function make_relevant_table_for_special_fields($result, $tablename, $special_flds, $accepted_special_flds, $primary_flds, $value, $field_name, $relavant_table_name){
        //echo "Table: $relavant_table_name";
        $nrows = $result->num_rows;
        $ncols = $result->field_count;
        ?>
        <table class="table is-spaced is-bordered is-striped is-hoverable">
            <thead>
                <tr>
                    <?php
                    $resar = $result->fetch_all();
                    $disgusting_fields = array();
                    $i = 0;
                    //echo "Value:$value";
                    $rel_primary_flds_indices = array();
                    $rel_primary_flds = $primary_flds;
                        while ($fld = $result->fetch_field()){
                            $fld_name = $fld->name;
                            //echo $fld_name;
                            if (in_array($fld_name, $rel_primary_flds)){ //If the relevant primary field exists for the index and if it is equal to the field name.
                                //echo "Pushing";
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
                        //echo "Primary fields and their indices:";
                        //print_r($rel_primary_flds_indices);
                        //echo "<br/>";
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
                        $id_prefix = "$tablename$field_name";
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
                                $id = $id_prefix.$id;
                                $id = str_replace(' ', '_', $id);
                                //echo "ID: ".$id;
                            ?>
                                <input type="checkbox"
                                name="checkbox<?php echo $id; ?>"
                                value="<?php echo $id; ?>"
                                id="<?php echo "checkbox$relavant_table_name";?>"
                                checked
                                />
                                <?php
                            }else{
                                $id = $id_prefix.$id;
                                //echo "ID: ".$id;
                                ?>
                                <input type="checkbox"
                                name="checkbox<?php echo $id; ?>"
                                value="<?php echo $id; ?>"
                                id="<?php echo "checkbox$relavant_table_name";?>"
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
            
            document.querySelectorAll("#checkbox" + "<?php echo $relavant_table_name?>").forEach((checkbox) => {
                
                checkbox.addEventListener('click', (event) => {
                    if (checkbox.checked){ //If the checkbox has just been checked to be true.
                        document.querySelectorAll("#checkbox" + "<?php echo $relavant_table_name?>").forEach((checkboxother) => {
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
    
    function make_edit_page($values, $record, $result, $fields){
        while ($fld = $result->fetch_field()){
            $fld_name = $fld->name;

            if (in_array($fld_name, $fields)){
                //If the field exists.
                ?>
                <div>
                    <?php echo $fld_name; ?>
                </div>
                <?php
            }
        }
    }

    //Relevant variables

    $primary_flds = array( //All of the primary fields for each table.
        'Costume_Types' => array('Costume_Type', 'Costume'),
        'Costumes_Inventory' => array('Costume_ID'),
        'Costumes_Rented' => array('Staff_ID', 'Costume_ID'),
        'Customers' => array('Cust_ID'),
        'Orders' => array('Order_ID'),
        'Pokemon' => array('Pokemon_Name', 'Pokemon_Species'),
        'Pokemon_Inventory' => array('Inventory_ID'),
        'Sightings' => array('Pokemon_Name', 'Sighting_Location', 'Sighting_Time', 'Location_Sighted', 'Time_Sighted', 'Pokemon_Species'),
        'Staff' => array('Staff_ID'),
    );

    $special_flds = array( //All the fields that need a table representation to show records to choose
        'Cust_ID' => 'Customers',
        'Inventory_ID' => 'Pokemon_Inventory',
        'Ordered_Pokemon' => 'Pokemon',
        'Staff_ID' => 'Staff',
        'Costume_ID' => 'Costumes_Inventory',
        'Pokemon_Species' => 'Pokemon',
        'Pokemon_Name' => 'Pokemon',
        'Costume_Type' => 'Costume_Types',
        'Costume' => 'Costume_Types'
    );

    $accepted_special_flds = array( //All fields that I want them to be able to change.
        'Pokemon_Species' => 'Pokemon',
        'Pokemon_Name' => 'Pokemon',
        'Costume_Type' => 'Costume_Types',
        'Costume' => 'Costume_Types'
    );

    $masks = array( //All of the place holders that show them what to enter into the text box.
        'Costume' => 'Meowth Costume, Office Jenny Costume',
        'Size' => 'S, M, L, XL',
        'CheckoutDate' => 'YYYY-MM-DD hh:mm:ss',
        'DueDate' => 'YYYY-MM-DD hh:mm:ss',
        'LastName' => 'Smith',
        'FirstName' => 'John',
        'Address'=> 'House #30, Veridian Forest',
        'Phone' => '(502)982-5012',
        'Pokemon_Species'=> 'Charmander, Pikachu',
        'Type' => 'Fire, Water, Grass',
        'Price' => '5.50, 6.25, 6',
        'Location_Sighted' => 'Cerulean City',
        'Time_Sighted' => 'YYYY-MM-DD hh:mm:ss',
        'Number_Of_Pokemon_Sighted' => '5, 2',
        'Staff_LastName' => 'Smith',
        'Staff_FirstName' => 'John',
        'SoldFor' => '5.50, 6.25, 6',
    );

    $not_required_fields = array( //All the optional fields
        'Costumes_Rented' => array('CheckoutDate', 'DueDate', 'Rental_CheckoutDate', 'Rental_DueDate', 'Rental_ReturnedDate', 'ReturnedDate'),
        'Customers' => array('Phone', 'Cust_Phone'),
        'Orders' => array('Inventory_ID', 'SoldFor', 'Orders_SoldFor', 'Pokemon')
    );

   $cant_edit = array(
       'Costume_Types' => True
   );
    
    $flds = array( //Used to display fields
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


    $as_fields = array(
        'Costume_Types' => array(
            'Costume' => 'Costume_Type'
        ),
        'Costumes_Inventory' => array(
            'Costume_ID' => 'Costume_ID',
            'Costume' => 'Costume_Type',
            'Size' => 'Costume_Size'
        ),
        'Costumes_Rented' => array(
            'Costume_ID' => 'Costume_ID',
            'Staff_ID' => 'Staff_ID',
            'CheckoutDate' => 'Rental_CheckoutDate',
            'DueDate' => 'Rental_DueDate',
            'ReturnedDate' => 'Rental_ReturnedDate',
            'Staff_FirstName' => 'Staff_FirstName',
            'Staff_LastName' => 'Staff_LastName',
            'Costume' => 'Costume_Type',
            'Size' => 'Costume_Size'
        ),
        'Customers' => array(
            'Cust_ID' => 'Cust_ID',
            'FirstName' => 'Cust_FirstName',
            'LastName' => 'Cust_LastName',
            'Address' => 'Cust_Address',
            'Phone' => 'Cust_Phone'
        ),
        'Orders' => array(
            'Order_ID' => 'Order_ID',
            'Cust_ID' => 'Cust_ID',
            'Inventory_ID' => 'Inventory_ID',
            'Ordered_Pokemon' => 'Order_ID',
            'SoldFor' => 'Order_SoldFor',
            'Customers_FirstName' => 'Cust_FirstName',
            'Customers_LastName' => 'Cust_LastName',
            'Inventory_Pokemon' => 'Inventory_ID',
            'Inventory_Pokemon_Name' => 'Pokemon_Name',
            'Price' => 'Pokemon_Price'
        ),
        'Pokemon' => array(
            'Pokemon_Species' => 'Pokemon_Name',
            'Type' => 'Pokemon_Type'
        ),
        'Pokemon_Inventory' => array(
            'Inventory_ID' => 'Inventory_ID',
            'Pokemon_Species' => 'Pokemon_Name',
            'Price' => 'Pokemon_Price'
        ),
        'Sightings' => array(
            'Pokemon_Species' => 'Pokemon_Name',
            'Location_Sighted' => 'Sightings_Location',
            'Time_Sighted' => 'Sightings_Time',
            'Number_Of_Pokemon_Sighted' => 'Sightings_NumPokemon'
        ),
        'Staff' => array(
            'Staff_ID' => 'Staff_ID',
            'FirstName' => 'Staff_FirstName',
            'LastName' => 'Staff_LastName'
        )
    );

    $query_strs = array(
        'Costume_Types' => 'SELECT Costume_Type as Costume FROM Costume_Types;',
        'Costumes_Inventory' => 'SELECT Costume_ID, Costume_Type as Costume, Costume_Size as Size FROM Costumes_Inventory;',
        'Costumes_Rented' => 'SELECT Costume_ID, Staff_ID, Rental_CheckoutDate as CheckoutDate, Rental_DueDate as DueDate, Rental_ReturnedDate as ReturnedDate, Staff_FirstName, Staff_LastName, Costume_Type as Costume, Costume_Size as Size FROM Costumes_Rented LEFT JOIN Staff USING (Staff_ID) LEFT JOIN Costumes_Inventory USING (Costume_ID);',
        'Customers' => 'SELECT Cust_ID, Cust_FirstName as FirstName, Cust_LastName as LastName, Cust_Address as Address, Cust_Phone as Phone FROM Customers;',
        'Orders' => 'SELECT Order_ID, Cust_ID, Inventory_ID, Orders.Pokemon_Name as Ordered_Pokemon, Order_SoldFor as SoldFor, Cust_FirstName as Customers_FirstName, Cust_LastName as Customers_LastName, Pokemon_Inventory.Pokemon_Name as Inventory_Pokemon, Pokemon_Price as Price FROM Orders LEFT JOIN Customers USING (Cust_ID) LEFT JOIN Pokemon_Inventory USING (Inventory_ID);',
        'Pokemon' => 'SELECT Pokemon_Name as Pokemon_Species, Pokemon_Type as Type FROM Pokemon;',
        'Pokemon_Inventory' => 'SELECT Inventory_ID, Pokemon_Name as Pokemon_Species, Pokemon_Price as Price FROM Pokemon_Inventory;',
        'Sightings' => 'SELECT Pokemon_Name as Pokemon_Species, Sightings_Location as Location_Sighted, Sightings_Time as Time_Sighted, Sightings_NumPokemon as Number_Of_Pokemon_Sighted FROM Sightings;',
        'Staff' => 'SELECT Staff_ID, Staff_FirstName as FirstName, Staff_LastName as LastName FROM Staff;'
    );

    $flds2 = array( //Used to edit fields
        'Costumes_Inventory' => array('Costume', 'Size'), 
        //Costume_Type, Costume_Size
        'Costumes_Rented' => array('CheckoutDate', 'DueDate', 'ReturnedDate'), 
        //Rental_CheckoutDate, Rental_DueDate, Rental_ReturnedDate
        'Customers' => array('FirstName', 'LastName', 'Address', 'Phone'),
        //Cust_FirstName, Cust_LastName, Cust_Address, Cust_Phone
        'Orders' => array('Ordered_Pokemon', 'Cust_ID', 'Inventory_ID', 'SoldFor'),
        //Pokemon_Name, Cust_ID, Inventory_ID, Order_SoldFor
        'Pokemon' => array('Type'),
        //Pokemon_Type
        'Pokemon_Inventory' => array('Pokemon_Species', 'Price'),
        //Pokemon_Name, Pokemon_Price
        'Sightings' => array('Number_Of_Pokemon_Sighted'),
        //
        'Staff' => array('FirstName', 'LastName'),
    );

    $dates = array(
        'Sighting_Time' => true,
        'Rental_CheckoutDate' => true,
        'Rental_DueDate' => true,
        'Rental_ReturnedDate' => true,
        'CheckoutDate' => true,
        'DueDate' => true,
        'ReturnedDate' => true
    );

    $fld_types = array(
        'Costume' => 's',
        'Size' => 's',
        'CheckoutDate' => 's',
        'DueDate' => 's',
        'ReturnedDate' => 's',
        'FirstName' => 's',
        'LastName' => 's',
        'Address' => 's',
        'Phone' => 's',
        'Ordered_Pokemon' => 's',
        'Cust_ID' => 'i',
        'Inventory_ID' => 'i',
        'SoldFor' => 'd',
        'Type' => 's',
        'Pokemon_Species' => 's',
        'Price' => 'd',
        'Number_Of_Pokemon_Sighted' => 'i',
        'Costume_ID' => 'i',
        'Staff_ID' => 'i',
        'Order_ID' => 'i',
        'Costume_Type' => 's',
        'Pokemon_Name' => 's',
        'Sighting_Location' => 's',
        'Sighting_Time' => 's',
        'Location_Sighted' => 's',
        'Time_Sighted' => 's',
    )
?>

<!DOCTYPE html>

<html>
    <head>
    <link rel="stylesheet" href="../main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma-rtl.min.css">
    <?php
        
    ?>
    <title>Update Records</title>
    </head>
    <body>
        
    <nav class="navbar has-shadow" role="navigation" aria-label="main navigation">
            <a class="navbar-item" href="/team/html/TermProject/PHPScripts/home.php">
                <img class="" src="../../Images/TeamRocketLogo-removebg-preview.v1.png" width="60" height="100">
            </a>

            <div class="navbar-start">
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/readRecordsFiles/readRecords.php" class="readRecords">Read Records</a>
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/addRecordsFiles/addRecords.php" class="addRecords">Add Records</a>
            </div>
            <div class="navbar-end">
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/deleteRecordsFiles/deleteRecords.php" class="deleteRecords">Delete Records</a>
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/updateRecordsFiles/updateRecords.php" class="updateRecords">Update Records</a>
            </div>
           
        </nav>
        <br>
        <br>
        <br>
        <br>
        <h1>Update Records</h1>
        <?php 

        if (isset($_POST['hi'])){
            echo "<h1>Hi!</h1>";
        }

        if (isset($_GET['hi'])){
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

        if ($_SESSION['updated']){
            ?>
            <h4 class="NotificationDiv">
                <p class="Confirmation">
                    <?php
                        echo "Successfully updated records!"
                    ?>
                </p>
            
            </h4>
            
            <?php
            $_SESSION['updated'] = False;
        }

if (isset($_GET['tablesSelector'])){
    $_SESSION['tableName'] = $_GET['tablesSelector'];
}


?>
<form action="updateRecords.php" method="GET"> <!-- Choose table-->
    <aside class="menu">
        <label for="tableSelector" class="menu-label">Table:</label>  
        <br>
        <select name="tablesSelector" id="tableSelector">
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
                        if (!$cant_edit[$table_name[0]]){
                    ?>
                    <option>
                        <?php echo $table_name[0]; ?>
                    </option>
                    <?php
                        }
                    }                 
                }
            }
            ?>
        </select>
    </aside>
    
    <!--
        Need a label for the selector becuase it helps to 
        later find the selected table
    -->
    <div class="dropdown is-active">
        
    </div>
    <button type="submit">Select</button>            
</form>

        <?php
            $table_name = $_SESSION['tableName'];
        if ($table_name){
            //Ok, so it has two states:
            //Currently editting
            //Not started editing, need to pick record

            //First, lets see the state it is in
            $result = $conn->query($query_strs[$table_name]);
            $resar = $result->fetch_all(); 
            $nrows = $result->num_rows;
            $ncols = $result->field_count;
            $edit = False;
            $primary_fields_for_table = $primary_flds[$table_name];
            $record_index = 0;
            $primary_field_indices = array();
            $primary_field_indices_with_fieldname = array();
            $field_by_indices = array();
            $i = 0;
            while ($fld = $result->fetch_field()){
                $fld_name = $fld->name;
                $field_by_indices[$fld_name] = $i;
                //echo $fld_name;
                if (in_array($fld_name, $primary_fields_for_table)){
                    array_push($primary_field_indices, $i);
                    $primary_field_indices_with_fieldname[$fld_name] = $i;
                }
                $i++;
            }
            
            for ($i = 0; $i < $nrows; $i++){ //Check if edit button has been pressed
                $id = "";
                $record = $resar[$i];

                foreach ($primary_field_indices as $primary_field_index){ 
                    $id = $id.$record[$primary_field_index];
                }
                $id = str_replace(' ', '_', $id);
                if ($_GET[$id]){ //Found that edit button was pressed
                    $result = $conn->query($query_strs[$table_name]);
                    $resar = $result->fetch_all(); 
                    $record = $resar[$i];
                    foreach ($primary_field_indices_with_fieldname as $fld => $val){
                        $primary_field_indices_with_fieldname[$fld] = $record[$val];
                    }
                    $edit = $record;
                    break;
                }
            }
            
            
            if ($_POST['Edited']){
                $edit = False;
                //$reload = True; Needs to become true after the edits have been applied to all the records
            }

            if ($edit){
                //Editing state
               
                $_SESSION["primary_keys"] = $primary_field_indices_with_fieldname;
                $result = $conn->query($query_strs[$table_name]);
                $resar = $result->fetch_all();
                $relevant_flds = $flds2[$table_name]; //All the editable fields and how they are displayed   
                $primary_key_field_value_index = array();
                //echo "Edit: ";
                //print_r($edit);
                ?>
                <br>
                <br>
                <form action="updateRecords.php" method="POST">
                    <?php
                    for ($i = 0; $i < count($relevant_flds); $i++){
                        //Traverse through each field, show the field name and the input they should enter.
                        $rel_fld = $relevant_flds[$i];
                        $real_fld = $as_fields[$table_name][$rel_fld];
                        $field_index = $field_by_indices[$rel_fld];
                        $val = $edit[$field_index];
                        if (!$dates[$rel_fld]){
                            $val = str_replace(' ', '_', $val);
                        }
                        //echo "<br/>Specific field:".$field_index."<br/>";
                        ?>
                        <div>
                            <div class="FieldName">
                                <?php echo $rel_fld.":<br/><br/>"; ?>
                            </div>
                            <div class="FieldInput">
                                <?php
                                    //Check if the field is special or not, if not then just do a text input, otherwise present a table.
                                    if (!$special_flds[$rel_fld]){
                                        $id = $table_name.$rel_fld;
                                        //echo "ID: $id";
                                        ?>
                                        <input type="text" name="<?php echo $id; ?>" value="<?php echo $val; ?>"/>
                                        <?php
                                    }else{
                                        $result = $conn->query("SELECT * FROM $special_flds[$rel_fld];");
                                        make_relevant_table_for_special_fields($result, $table_name, $special_flds, $accepted_special_flds, $primary_flds[$special_flds[$rel_fld]], $val, $rel_fld, $special_flds[$rel_fld]);
                                        //$result, $tablename, $special_flds, $accepted_special_flds, $primary_flds, $value
                                    }
                                ?>
                            </div>
                        </div>
                        
                        <?php
                    }?>
                    <input type="submit" name="Edited" value="Publish Edits"/>
                </form>
                
                <?php
            }else{
                //Not editing but records might need updating from edit
                if ($_POST['Edited']){
                    //echo "<br/>Primaries:<br/>";
                    $primary_keys_and_values = $_SESSION["primary_keys"];
                    //print_r($primary_keys_and_values);
                    $dirc = get_sql_script_str($table_name, "updateRecords");
                    //echo "<br/>Directory:$dirc<br/>";
                    $query_str = file_get_contents("$dirc");
                    //echo "<br/>Query:$query_str<br/>";
                    $_fields_with_respective_values = array();
                    $values = array();
                    $rel_flds = $flds2[$table_name];
                    $query = $query_strs[$table_name];
                    $result = $conn->query($query);
                    $ncols = $result->field_count;
                    $nrows = $result->num_rows;
                    $resar = $result->fetch_all();
                    $type_str = '';
                    $null_occurences = array();
                    $not_required_fields_for_table = $not_required_fields[$table_name];
                    while ($fld = $result->fetch_field()){
                        $fld_name = $fld->name;
                        if (in_array($fld_name, $rel_flds)){
                            $record_added = false;
                            //echo "In array";
                            $id = $table_name.$fld_name;
                            if (!$special_flds[$fld_name]){
                                //echo "ID: $id<br/>";
                                //Not special field
                                $id = str_replace(' ', '_', $id);
                                if ($_POST[$id]){
                                    $val = htmlspecialchars($_POST[$id]);
                                    if (!$dates[$fld_name]){
                                        $val = str_replace(' ', '_', $val);
                                    }

                                    //echo "<br/>Ok, $fld_name has value specified.<br/>";
                                    $_fields_with_respective_values[$fld_name] = $val; 
                                    
                                    //echo "Pushing values $val";
                                    array_push($values, $val);
                                    $record_added = true;
                                    $type_str = $type_str.$fld_types[$fld_name];                                    
                                }
                            }else{
                                //It is a special field so check for each thing inside that special table.
                                $rel_special_table = $special_flds[$fld_name];
                                $primary_fields_for_spec = $primary_flds[$rel_special_table];
                                $query_n = "SELECT * FROM $rel_special_table;";
                                $result_n = $conn->query($query_n);
                                $nrows_n = $result_n->num_rows;
                                $ncols_n = $result_n->field_count;
                                $resar_n = $result_n->fetch_all();
                                $primary_field_indices_n = array();
                                $k = 0;
                                while ($fld_n = $result_n->fetch_field()){
                                    $fld_name_n = $fld_n->name;

                                    if (in_array($fld_name_n, $primary_fields_for_spec)){
                                        array_push($primary_field_indices_n, $k);
                                    }

                                    $k++;
                                }
                                
                                for ($i = 0; $i < $nrows_n; $i++){
                                    $record_id = "";
                                    $record = $resar_n[$i];
                                    foreach ($primary_field_indices_n as $primary_field_index){
                                        $record_id = $record_id.$record[$primary_field_index];
                                    }
                                    $id = $table_name.$fld_name;
                                    $id = $id.$record_id;
                                    $id = "checkbox".$id;
                                    //echo "ID:".$id."<br/>";
                                    $id = str_replace(' ', '_', $id);
                                    if ($_POST[$id]){
                                        $val = htmlspecialchars($record_id);
                                        $val = str_replace(' ', '_', $val);
                                            
                                        $_fields_with_respective_values[$fld_name] = $val; 
                                        array_push($values, $val);
                                        $record_added = true;
                                        $type_str = $type_str.$fld_types[$fld_name];
                                    }
                                }
                            }
                            echo "Not required fields:<br/>";
                            print_r($not_required_fields_for_table);
                            if (!$record_added){
                                if (!in_array($fld_name, $not_required_fields_for_table)){
                                    $_SESSION['Error'] = "You must specify a value for $fld_name.";
                                    $_SESSION['PreviousTable'] = $_SESSION['tableName'];
                                    header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
                                    exit();
                                }else{
                                    //echo "Pushing...";
                                    array_push($null_occurences, array_search($fld_name, $rel_flds));
                                }
                            }
                        }
                    }
                    $n = 0;
                    asort($null_occurences);
                    //echo "Query: $query_str<br/>";
                    foreach ($null_occurences as $occurence){
                        $actual_occurence = $occurence + 1;
                        //echo "<br/>Actual Occurence:$actual_occurence<br/>";
                        $query_str = replace_specific_occurence($query_str, $actual_occurence - $n, '?', 'NULL');
                        $n++;
                    }

                    echo "New query:<br/> $query_str<br/>";
                    if ($reload){
                        //Ok, reload the page and redirect to a get request so that post don't repeat
                        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
                        exit();
                    }

                    foreach ($primary_keys_and_values as $fld => $val){
                        $_fields_with_respective_values[$fld] = $val;
                        array_push($values, $val);
                        //echo "Field: $fld<br/> FieldType: $fld_types[$fld]<br/>";
                        $type_str = $type_str.$fld_types[$fld];
                    }
                    //echo "Types:".$type_str."<br/>";
                    //echo "Fields & Values:<br/>";
                    //print_r($_fields_with_respective_values);
                    echo "<br/>Values:<br/>";
                    print_r($values);
                    echo "<br/>";

                    
                    $stmt = $conn->prepare("$query_str");
                    if ($stmt == false){
                        die("Prepare() failed: ".htmlspecialchars($stmt->error));
                    }

                    $binding = bind($stmt, $values, $type_str);
                    /*if ($binding == false){
                        die('bind_param() failed: '.htmlspecialchars($conn->error));
                    }*/
                    $result_exec = $stmt->execute();
                    echo "<br/>";
                    /*if ($result_exec == false){
                        die('execute() failed: '.htmlspecialchars($stmt->error));
                    };*/

                    if (!$result_exec){
                        $_SESSION["Error"] = "Error: $conn->error";
                    }else{
                        $_SESSION['updated'] = True;
                    }
                        //Ok, reload the page and redirect to a get request so that post don't repeat
                        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
                        exit();
                    /*echo "Values:<br/>";
                    print_r($values);
                    echo "<br/>";*/
                }
                $fields = $flds[$table_name];
                $result = $conn->query($query_strs[$table_name]);
                ?>
                <form action="updateRecords.php" method="GET">
                    <?php
                    make_relevant_table($result, $fields, $primary_fields_for_table);
                    ?>
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
        <h4 class="NotificationDiv">
            <p class="Error">
            <?php
                echo $_SESSION['Error'];
            ?>
            </p>
            
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