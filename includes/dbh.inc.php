<?php
$server = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "myfirstdatabase";

$conn = mysqli_connect($server, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed" . mysqli_connect_error());
}

date_default_timezone_set('America/Denver');
$dateTime = date('Y-m-d H:i:s');


/*

PUT IN A PASSWORD!
class Dbh {
    private $host;
    private $dbusername;
    private $dbpassword;
    private $dbname;
    private $charset;

    public function connect() {
        $this->host = "localhost";
        $this->dbusername = "root";
        $this->dbpassword = "";
        $this->dbname = "myfirstdatabase";
        $this->charset = "utf8mb4";

        try {
            $dsn = "mysql:host=".$this->host.";dbname=".$this->dbname.";charset=".$this->charset;
            $pdo = new PDO($dsn, $this->dbusername, $this->dbpassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: ".$e->getMessage());
        }
    }
}


$pdo = new Dbh;
$pdo->connect(); 
echo "you are connected, bitch.";






$host = 'localhost';
$dbname = 'myfirstdatabase';
$dbusername = 'root';
$dbpassword = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOEXCEPTION $e) {
    die("Connection failed: " . $e->getMessage());
}

*/
