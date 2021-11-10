<html>
    <head>
        <title>
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
        ?>
        <?php
                    $query = "USE PPC";
                    $conn->query($query);
        ?>
        <form action="addRecords.php"> <!-- Choose table-->
            <select name="tableSelection" id="tableSelector">
                <?php
                    $query = "SHOW TABLES;";
                    $result = $conn->query($query);
                    if (!$result){
                        echo "Failed to show tables";
                    }else{
                        while ($table_name = $result->fetch_array()){
                            ?>
                            <option value=<?php echo $table_name; ?>>
                            <?php
                                echo $table_name[0];
                            ?>
                            </option>
                            <?php
                        }
                    }
                ?>
            </select>
        </form>
    </body>
</html>