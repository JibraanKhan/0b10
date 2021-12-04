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

    function make_relevant_table($result, $fields, $primary_fields, $table_name){
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
                    <th>Delete?</th>
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
                            $id = $table_name.$id;
                            //echo "Set ID: ".$id;
                            ?>
                            <td>
                                <input type="checkbox"
                                name="<?php echo $id; ?>"
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
        <input type="submit" name="Deleted" value="Delete Records">
        <?php
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
        'Costumes_Rented' => array('CheckoutDate', 'DueDate', 'Rental_CheckoutDate', 'Rental_DueDate'),
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
        'Costumes_Rented' => 'SELECT Costume_ID, Staff_ID, Rental_CheckoutDate as CheckoutDate, Rental_DueDate as DueDate, Rental_ReturnedDate as ReturnedDate, Staff_FirstName, Staff_LastName, Costume_Type as Costume, Costume_Size as Size FROM Costumes_Rented INNER JOIN Staff USING (Staff_ID) INNER JOIN Costumes_Inventory USING (Costume_ID);',
        'Customers' => 'SELECT Cust_ID, Cust_FirstName as FirstName, Cust_LastName as LastName, Cust_Address as Address, Cust_Phone as Phone FROM Customers;',
        'Orders' => 'SELECT Order_ID, Cust_ID, Inventory_ID, Orders.Pokemon_Name as Ordered_Pokemon, Order_SoldFor as SoldFor, Cust_FirstName as Customers_FirstName, Cust_LastName as Customers_LastName, Pokemon_Inventory.Pokemon_Name as Inventory_Pokemon, Pokemon_Price as Price FROM Orders INNER JOIN Customers USING (Cust_ID) INNER JOIN Pokemon_Inventory USING (Inventory_ID);',
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
    <link rel="stylesheet" href="../all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma-rtl.min.css">
    <?php
        
    ?>
    <title>Delete Records</title>
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
        <h1>Delete Records</h1>
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

        if ($_SESSION['deleted']){
            ?>
            <div class="Confirmation">
                <p>
                    Successfully deleted records!
                </p>
            </div>
            <?php
            $_SESSION['deleted'] = False;
        }
        if (isset($_GET['tablesSelector'])){
            $_SESSION['tableName'] = $_GET['tablesSelector'];
        }


        ?>
        <form action="deleteRecords.php" method="GET"> <!-- Choose table-->
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
            
            
            

            
                //Not editing but records might need updating from edit
            if ($_POST['Deleted']){
                $fields = $flds[$table_name];
                $result = $conn->query($query_strs[$table_name]);
                $resar = $result->fetch_all();
                $nrows = $result->num_rows;
                $ncols = $result->field_count;
                $dirc = get_sql_script_str($table_name, 'deleteRecords');
                //echo "<br/>Directory:$dirc<br/>";
                $query_str = file_get_contents("$dirc");
                //echo "<br/>Query:$query_str<br/>";
                
                for ($i = 0; $i < $nrows; $i++){
                    $id = $table_name;
                    $record_id = array();
                    $type_str = '';
                    foreach ($primary_field_indices_with_fieldname as $fld=>$index){
                        $id = $id.$resar[$i][$index];
                        array_push($record_id, $resar[$i][$index]);
                        $type_str = $type_str.$fld_types[$fld];
                    }
                    $id = str_replace(' ', '_', $id);
                    //echo "ID: $id";
                    //echo "<br/>Records id:<br/>";
                    //print_r($record_id);
                    //echo "<br/>Types:$type_str<br/>";
                    if ($_POST[$id]){
                        //echo "Delete No.$i<br/>";
                        $stmt = $conn->prepare("$query_str");
                        if ($stmt == false){
                            die("Prepare() failed: ".htmlspecialchars($stmt->error));
                        }
                        bind($stmt, $record_id, $type_str);
                        $result_exec = $stmt->execute();

                        if (!$result_exec){
                            $_SESSION['Error'] = $conn->error;
                            $_SESSION['PreviousTable'] = $_SESSION['tableName'];
                            //echo "<br/>FAILED<br/>";
                        }else{
                            $_SESSION['deleted'] = true;
                        }
                        
                    }
                }
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
                exit();
            }
            $fields = $flds[$table_name];
            $result = $conn->query($query_strs[$table_name]);
            ?>
            <form action="deleteRecords.php" method="POST">
                <?php
                make_relevant_table($result, $fields, $primary_fields_for_table, $table_name);
                ?>
            </form>
            <?php
            
            
            
        }
        ?>
    </body>
</html>

<?php

    if ($_SESSION['Error'] && ($_SESSION['PreviousTable'] == $table_name)){
        $_SESSION['PreviousTable'] = False;
        ?>
        <div class="Error">
            <h4 class="Error">
                <?php
                    echo $_SESSION['Error'];
                ?>
            </h4>
        </div>
        
        <?php
    }

    if ($reload){
        //Ok, reload the page and redirect to a get request so that post don't repeat
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
        exit();
    }
    $conn->close();
?>