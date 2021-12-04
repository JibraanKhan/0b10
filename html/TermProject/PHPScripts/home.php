<html>
    <head>
        <title>
            Home
        </title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma-rtl.min.css">
        <link rel="stylesheet" href="home.css">
    </head>
    <body>
        <div class="background_img">

        </div>
    <nav class="navbar" role="navigation" aria-label="main navigation" style="background-color:rgba(245, 245, 245, 1);">
            <a class="navbar-item" href="/team/html/TermProject/PHPScripts/home.php" style="background-color:rgba(245, 245, 245, 1);">
                <img class="" src="../Images/TeamRocketLogo-removebg-preview.v1.png" width="60" height="100">
            </a>

            <div class="navbar-start">
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/readRecordsFiles/readRecords.php" class="readRecords" style="background-color:rgba(245, 245, 245, 1);">Read Records</a>
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/addRecordsFiles/addRecords.php" class="addRecords" style="background-color:rgba(245, 245, 245, 1);">Add Records</a>
            </div>
            <div class="navbar-end">
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/deleteRecordsFiles/deleteRecords.php" class="deleteRecords" style="background-color:rgba(245, 245, 245, 1);">Delete Records</a>
                <a class="navbar-item is-spaced" href="/team/html/TermProject/PHPScripts/updateRecordsFiles/updateRecords.php" class="updateRecords" style="background-color:rgba(245, 245, 245, 1);">Update Records</a>
            </div>
           
        </nav>

        <div class="box">
            <div class="content" id="welcome">
                <h1>Welcome to our website!</h1>
                <p>
                    This website was made by Shelby Young, Selam Van Voorhis, and Jibraan Khan for the purpose of the client: Pokemon Procurement Services. We got a request to make our client a website back in September of 2021. 
                    The client wanted to match their customers with pokemon and keep track of the orders placed by customers. The Pokemon Procurement Center also had staff members who retrieved the pokemon, and the client wanted to keep track of which costumes were
                    being used by each staff member when the staff member was in the field. The client also wanted to keep track of all the pokemon that get sighted, so that the staff members can go and retrieve those pokemon quickly to match them with customers. 
                </p>
                <p>
                    We happily accepted the request and have since been working on this website meeting every week to design the database and make a product we can be proud of and that represents our service to our amazing clientele.
                </p>
            </div>

            <div class="content" id="mission_statement">
                <h3>Mission Statement</h3>
                <p>
                    Our mission is to provide staff with up-to-date information to enable them to place Pokémon in healthy and happy homes.
                </p>
            </div>

            <div class="content" id="challenges">
                <h3>Challenges</h3>
                <ul>
                    <li>Designing the database to effectively achieve the client's needs.</li>
                    <li>Deciding which deletion rules to use for each relationship.</li>
                    <li>Implementing the database using generics and making accommodations for invalid input from the user.</li>
                    <li>Keeping up with some of the deadlines such as the php pages when we wanted to implement generics.</li>
                </ul>
            </div>
        </div>
        
    </body>
</html>