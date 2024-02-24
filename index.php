<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheapie UI</title>
</head>
<body>
    <div>
        <a href="index.php?page=home">Cheapie</a> |
        <a href="index.php?page=legal">Imprint</a> |

        <label for="city">City</label>
        <select name="city" id="city">
        <?php
        //echo '<label for="city">City</label>';
        $selectedCountry = $_POST["country"];
        $token = json_decode(file_get_contents("/etc/cheapie/token.json"), true);
        $pdo = new PDO("mysql:host=localhost;dbname=cheapie_db", $token["user"], $token["password"]);
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $cityRes = $pdo->query("SELECT BranchPostcode, BranchCity, Country FROM Branch ORDER BY BranchCity ASC");
        $cityArr = array();

        foreach ($cityRes as $row) {
            array_push($cityArr, $row["BranchPostcode"] . " " . $row["BranchCity"] . ", " . $row["Country"]);
        }

        $cityArrUnique = array_unique($cityArr);
        
        foreach ($cityArrUnique as $city) {
            echo '<option value="$city">' . $city . PHP_EOL . '</option>';
        }
        ?>
        </select> |
        
        <input type="text" id="searchbar" name="searchbar" onkeyup="search()">
        <button type="button" onclick="search()">Search</button>
        <script src="scripts/main.js"></script>
    </div>

    <?php
    if ($_GET['page'] == 'home' || $_GET['page'] == '') {
        echo '<h1>Home</h1>';
        //echo '<label for="city">City</label>';

        $token = json_decode(file_get_contents("/etc/cheapie/token.json"), true);
        $pdo = new PDO("mysql:host=localhost;dbname=cheapie_db", $token["user"], $token["password"]);
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $cityRes = $pdo->query("SELECT BranchPostcode, BranchCity FROM Branch");
        $cityArr = array();

        foreach ($cityRes as $row) {
            array_push($cityArr, $row["BranchPostcode"] . " " . $row["BranchCity"]);
        }

        $cityArrUnique = array_unique($cityArr);
        
        foreach ($cityArrUnique as $city) {
            echo $city . PHP_EOL . "<br>";
        }

    } else if ($_GET['page'] == 'legal') {
        echo '<h1>Imprint</h1>';
        echo '<p>Responsible for Cheapie</p>';

        $imprint = json_decode(file_get_contents("/etc/cheapie/imprint.json"), true);

        echo '
        <p>
            '.$imprint["name"].'<br>
            '.$imprint["address"].'<br>
            '.$imprint["city"].'<br>
            '.$imprint["country"].'
        </p>
        ';
    } else {
        echo '<h1>404 not found</h1>';
    }
    ?>

    <div>
        <p>(C) 2024 lucien-rowan and Cheapie contributors</p>
    </div>
</body>
</html>