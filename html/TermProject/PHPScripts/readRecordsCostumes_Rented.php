<html>
    <head>
        <title>
            readRecords.php
        </title>
    </head>
    <body>
        <h1 class="Read Records">Read Records From Costumes Rented Table</h1>
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
        ?>
        <?php
                    $query = "USE PPC";
                    $conn->query($query);
        ?>
        <form action="readRecords.php"> <!-- Choose table-->
            <select name="tableSelection" id="tableSelector">
                <?php
                    $query = "SHOW TABLES;";
                    $result = $conn->query($query);
                    if (!$result){
                        echo "Failed to show tables";
                    }else{
                        mysql_select_db('database');
                        $result = mysql_query('SELECT * FROM Costumes_Rented');}
                    if (!$result) {
                            echo "Failed to show tables";
                        }
                    $nrows= $result->num_rows;
                    $ncols= $result->field_count;
                    $resar==$result->fetch_all();
        
                        for ($i=0; $i<nrows; $i++){
                    ?>
                         <tr><?php
                            for ($j=0; $j<$ncols; $j++){
                                $val = $resar[$i][$j]; ?>
                                
                                <td><?php echo $val; ?> </td>
                                    <?php
                                    }
                                ?> </tr><?php
                            }
                            ?>

                        }
                        ?>
                       <option value=<?php echo $result; ?>>

                        </option>
                   
            </select>
        </form>
    </body>
</html> 