<?php
    session_start();
    $reload = False;
    // Helper Functions
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

    function make_relevant_table($result, $ncols, $nrows){
        ?>
        <table>
            <thead>
                <tr>
                    <?php
                    $resar = $result->fetch_all();
                        while ($fld = $result->fetch_field()){
                            $fld_name = $fld->name;

                            echo "<th>".$fld_name."</th>";
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
                        $id = $resar[$i][0];

                        for ($j = 0; $j < $ncols; $j++){
                            echo "<td>".$resar[$i][$j]."</td>";
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
        </table>
        <?php
    }

    //Helper variables
//Cust_LastName, Cust_FirstName, Cust_Address, Cust_Phone
    $fields = array(
        'Cust_LastName'=>True,
        'Cust_FirstName'=>True,
        'Cust_Address'=>True,
        'Cust_Phone'=>True
    );

    $types = array('s', 's', 's', 's', 'i');
    $primary_field = 'Cust_ID';
?>

<html>
    <head>
        <link rel="stylesheet" href="table.css">
    </head>

    <body>
        <h1>Customers</h1>
    </body>
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

        $conn->query("USE PPC;");

        $query_str = "SELECT * FROM Customers;";
        $result = $conn->query($query_str);
        $ncols = $result->field_count;
        $nrows = $result->num_rows;
        $resar = $result->fetch_all();
        $edit = False;

        for ($i = 0; $i < $nrows; $i++){
            $id =  $resar[$i][0];
            if (isset($_POST[$id])){
                $edit = True;
                break;
            }
        }

        if (isset($_POST['Edited'])){
            $primary_val = $_SESSION['recordPrimaryKey'];
            $query_spec_str = file_get_contents(get_sql_script_str("Customers", "updateRecords"));
            //echo "prim_val:".$primary_val."<br/>";
            
            $fld_val_pairs = array();
            $occurence = 1;
            $occurence_arr = array();
            $values = array();
            foreach ($fields as $field=>$bool){
                $identifier = $primary_val."_".$field;
                if (isset($_POST[$identifier])){
                    $value = htmlspecialchars($_POST[$identifier]);
                    $value = str_replace(' ', '_', $value);
                    if ($value == 'NULL'){
                        array_push($occurence_arr, $occurence);
                    }else{
                        array_push($values, $value);
                        $fld_val_pairs[$field] = $value;
                    }
                    $occurence++;
                }
            }




            for ($i = 0; $i < count($occurence_arr); $i++){
                $occurence = $occurence_arr[$i];
                $query_spec_str = replace_specific_occurence($query_spec_str, $occurence - $i, '?', 'NULL');
                unset($types[$i]);
            }
            $types = implode('', $types);
            array_push($values, $primary_val);


            $stmt = $conn->prepare("$query_spec_str");
            bind($stmt, $values, $types);
            $result = $stmt->execute();

            if (!$result){
                echo $conn->error;
            }
            


            $_SESSION['recordPrimaryKey'] = False;
            //$reload = True;
        }

        if (!$edit || $_POST['Edited']){
            $result = $conn->query($query_str);
            ?>

            
            <form action="updateRecordsCustomers.php" method="POST">
            <?php
            make_relevant_table($result, $ncols, $nrows);
            ?>
            </form>
            <?php
        }else{
            $_SESSION['recordPrimaryKey'] = $id;
            //echo "Session val:".$_SESSION['recordPrimaryKey'];
            ?>
            <form action="updateRecordsCustomers.php" method="POST">
                
                <table>
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $result = $conn->query($query_str);
                            $resar = $result->fetch_all();
                            $ncols = $result->field_count;
                            $nrows = $result->num_rows;

                            $record;
                            $record_index;
                            for ($i = 0; $i < $nrows; $i++){
                                if ($resar[$i][0] == $id){
                                    $record_index = $i;
                                    $record = $resar[$i];
                                    break;
                                }
                            }
                            $field_indices = array();
                            $i = 0;
                            while ($fld = $result->fetch_field()){
                                $fld_name = $fld->name;
                                if ($fields[$fld_name]){
                                    array_push($field_indices, $i);
                                }
                                $i++;
                            }
                            $i = 0;

                            foreach ($fields as $field=>$bool){
                                $value = $resar[$record_index][$field_indices[$i]];
                                $identifier = $id." ".$field;
                                $idenifier = str_replace(' ', '_', $identifier);
                                ?>
                                <tr>
                                    <td><?php echo $field;?></td>
                                    <td>
                                        <input type="text"
                                               name="<?php echo $identifier; ?>"
                                               value="<?php echo $value; ?>"
                                               id="textbox"
                                        />
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
                <input type="submit" name="Edited" id="submit">
            </form>

            <script>
                /*
                var submit = document.querySelector('#submit');
                submit.addEventListener('click', (event)=> {
                    document.querySelectorAll('#textbox').forEach((textBox) => {
                        window.alert(textBox.name)
                        window.alert(textBox.name=="2 Costume_Type");
                    })
                })
                */
            </script>

            <?php
            
        }
        
        

        if ($reload){
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
            exit();
        }
        $conn->close();
    ?>
</html>