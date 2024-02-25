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
            echo '<option value="' . $city . '">' . $city . PHP_EOL . '</option>';
        }
        ?>
        </select> |
        
        <input type="text" id="searchbar" name="searchbar">
        
        <?php
        echo '<button type="button" onclick="search(\'' . $_SERVER["SERVER_ADDR"] . '\')">Search</button>';
        ?>

        <script src="scripts/main.js"></script>
    </div>

    <?php
    if ($_GET['page'] == 'search' || $_GET['page'] == '') {
        echo '<h1>Search results for "' . $_GET['searchQuery'] . '" in ' . $_GET['city'] . '</h1>';

        $citySeparated = $_GET['city'].str_split(" ");
        $citySeparated[1] = str_replace(",", "", $citySeparated[1]);
        $token = json_decode(file_get_contents("/etc/cheapie/token.json"), true);
        $pdo = new PDO("mysql:host=localhost;dbname=cheapie_db", $token["user"], $token["password"]);
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $branchRes = $pdo->query(
            "SELECT BranchID, BranchAddress, BranchPostcode, BranchCity, Country, Company FROM Branch WHERE BranchCity = '" . $citySeparated[1] .
            "' AND BranchPostcode = '" . $citySeparated[0] . "' AND Country = '" . $citySeparated[2] . "'");
        $branchArr = array();
        $companyRes = $pdo->query("SELECT CompanyID, CompanyName FROM Company");
        $productResAlt = $pdo->query("SELECT ProductID, ProductName, ProductType, PictureURL FROM Product");
        $prodTypeRes = $pdo->query(
            "SELECT * FROM ProductType WHERE TypeName = '" . ucfirst($_GET['searchQuery']) . "' OR TypeName LIKE '" . $_GET['searchQuery'] . "'");
        
        foreach ($prodTypeRes as $prodType) {
            $productRes = $pdo->query("SELECT ProductID, ProductName, ProductType, PictureURL FROM Product WHERE ProductType = " . $prodType["TypeID"]);
            
            foreach ($productRes as $row) {
                $stockTotal= $pdo->query("SELECT Product, Branch, Price, Currency FROM Stock WHERE Product = " . $row["ProductID"]);

                foreach ($stockTotal as $stock) {
                    $stockFinal = array();
                    array_push($stockFinal, $row["ProductName"], $row["PictureURL"], $prodType["TypeName"], $stock["Price"], $stock["Currency"]);

                    $branchRes2 = $pdo->query(
                        "SELECT BranchID, BranchAddress, BranchPostcode, BranchCity, Country, Company FROM Branch WHERE BranchID = '" .
                        $stock["Branch"] . ""
                    );

                    $companyRes2 = $pdo->query(
                        "SELECT CompanyName FROM Company WHERE CompanyID = " . $branchRes2[0]["Company"]
                    );

                    array_push(
                        $stockFinal, $companyRes2[0]["CompanyName"], $branchRes2[0]["BranchAddress"], $branchRes2[0]["BranchPostcode"],
                        $branchRes2[0]["BranchCity"], $branchRes2[0]["Country"]
                    );

                    echo '
                    <div>
                        <img src="' . $row["PictureURL"] . '"></img><br>
                        <h4>' . $row["ProductName"] . '</h4>
                        <p>' . $prodType["TypeName"] . '</p>
                        <p>
                            From: ' . $companyRes2[0]["CompanyName"] . '<br>
                            ' . $branchRes2[0]["BranchAddress"] . '<br>
                            ' . $branchRes2[0]["BranchPostcode"] . ' ' . $branchRes2[0]["BranchCity"] . '<br>
                            ' . $branchRes2[0]["Country"] . '
                        </p>
                        <p>' . $stock["Price"] . ' ' . $stock["Currency"] . '</p>
                    </div>';
                }
            }
        }
    } else if ($_GET['page'] == 'legal') {
        
    } else {
        echo '<h1>404 not found</h1>';
    }
    ?>

    <div>
        <p>(C) 2024 lucien-rowan and Cheapie contributors</p>
    </div>
</body>
</html>