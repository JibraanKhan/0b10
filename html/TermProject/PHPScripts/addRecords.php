<html>
    <head>
        <title>
            <?php
            session_start();
            $reload = False;
            ?>
            addRecords.php
        </title>
    </head>
    <body>
        <h1 class="Add Records">Add Records</h1>
        <?php
        $dbse = "PPC";
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
        if (isset($_POST['Costume_Types'])){
            echo '<h1>Costume_Types</h1>';
        }
        //print_r($_POST);
        ?>
        
        <?php
                    $query = "USE PPC";
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
                ?>
                        <option>
                            <?php echo $table_name[0]; ?>
                        </option>
                <?php
                        }
                    }
                ?>
                </select>
                <button type="submit">Select</button>            
            </form>
        <?php
        
        

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

        $table_information = array(
            'Costume_Types' => array('s'),
            'Costumes_Inventory' => array('ss'),
            'Costumes_Rented' => array('iiss'),
            'Customers' => array('ssss'),
            'Orders' => array('sii'),
            'Pokemon' => array('ss'),
            'Pokemon_Inventory' => array('sd'),
            'Sightings' => array('sssi'),
            'Staff' => array('ss'),
        );

        $special_flds = array(
            'Cust_ID' => 'Customers',
            'Inventory_ID' => 'Pokemon_Inventory',
            'Staff_ID' => 'Staff',
            'Costume_ID' => 'Costumes_Inventory',
        );

        function bind($statement, $values, $types){
            return $statement->bind_param($types, ...$values);
        }

        if ($_SESSION['tableName']){
            $selectedTable = $_SESSION['tableName'];
            $rel_arr = $flds[$selectedTable];
            if (isset($_POST['AddRecords'])){
                echo "Records are being added...<br/>";
                $fld_value_pairs = array();
                $vals_only = array();
                for ($i = 0; $i < count($rel_arr); $i++){
                    $id = $rel_arr[$i];
                    if (isset($_POST[$id])){
                        $value = $_POST[$id];
                        $fld = $id;
                        $fld_value_pairs[$fld] = $value;
                        $vals_only[$i] = $value;
                    }
                }
                echo "<br/> <br/> <br/> <br/>";
                $str = "../SQLScripts/".$selectedTable."/addRecords".$selectedTable.".sql";
                $contents = file_get_contents($str);
                foreach($fld_value_pairs as $key=>$value){
                    // Each field and value for the specific record.
                    
                    
                    echo "$key, $value <br/>";
                }
                echo $str;
                echo "<br/> <br/> <br/> <br/>".$contents."<br/>";
                echo "<br/> <br/> <br/> <br/>";
                for ($i = 0; $i < count($vals_only); $i++){
                    echo "Value: ".$vals_only[$i]."<br/>";
                }
                echo "Reached point";
                $stmt = $conn->prepare("$contents");
                //$vals_only = array('Pikachu', 'Lightning');

                //bind($stmt, $vals_only, 'ss');
                bind($stmt, $vals_only, $table_information[$selectedTable][0]);

                $results = $stmt->execute();
                if (!$results){
                    echo $conn->error;
                    echo "Failed to insert records!\n";
                }
                $reload = true;
            }else{
                ?>
                <form action="addRecords.php" method="POST">

                <?php
                for ($i = 0; $i < count($rel_arr); $i++){
                    $id = $rel_arr[$i];
                    ?>
                    <div className="fieldInputs">
                        <!-- FieldName: InputBox Div-->
                        <h4>
                            <?php 
                                echo $rel_arr[$i].":";
                            ?>
                        </h4>

                        <?php 
                            if (!$special_flds[$id]){
                        ?>
                                <input type="text" name="<?php echo $id; ?>"/>
                        <?php
                            }else{
                                $rel_arr = $special_flds[$id];
                                $query_str = "SELECT * FROM $rel_arr;";
                                $result = $conn->query($query_str);
                                $ncols = $result->field_count;
                                $nrows = $result->num_rows;
                                //echo "before function call";
                                //make_relevant_table($result, $ncols, $nrows);
                        ?>
                                
                        <?php
                        ?>

                        <?php
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
        /*
        function make_relevant_table($result, $ncols, $nrows){
            echo "function called";
            ?>
            <table>
                <thead>
                    <tr>
                        <?php
                        $resar = $result->fetch_all();
                            while ($fld = $result->fetch_field()){
                                ?>
                                <th>
                                    <?php
                                        echo $fld->name;
                                    ?>
                                </th>
                                <?php
                            }
                        ?>
                    </tr>
                </thead>

                <tbody>
                    <!--
                    <?php
                    
                    echo "rows: $nrows \ncolumns: $ncols";
                        for ($i = 0; $i < $nrows; $i++){
                            ?>
                            <tr>
                            <?php
                            for ($j = 0; $j < $ncols; $i++){
                                echo $resar[$i][$j];
                            }
                            ?>

                            </tr>
                            <?php
                        }
                    
                    ?>
                    -->
                </tbody>
            </table>
            <?php
        }
        */
        
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
