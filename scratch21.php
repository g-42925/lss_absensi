<?php
define('BASEPATH', TRUE);
require_once '/opt/lampp/htdocs/lss_absensi/application/config/database.php';
$dsn = 'mysql:host='.$db['default']['hostname'].';dbname='.$db['default']['database'];
$user = $db['default']['username'];
$password = $db['default']['password'];

try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Test checkJumlahPola without CodeIgniter instance to see if it causes issues?
    // Actually, I can just include the helper and mock get_instance
    class CI_Controller {
        public $db;
        public function __construct() {
            global $dbh;
            $this->db = new class($dbh) {
                public $dbh;
                public function __construct($dbh) { $this->dbh = $dbh; }
                public function query($sql) {
                    $stmt = $this->dbh->query($sql);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return new class($result) {
                        public $res;
                        public function __construct($res) { $this->res = $res; }
                        public function row_array() { return $this->res[0] ?? null; }
                    };
                }
            };
        }
    }
    
    $ci = new CI_Controller();
    function get_instance() {
        global $ci;
        return $ci;
    }
    
    require_once '/opt/lampp/htdocs/lss_absensi/application/helpers/i_helper.php';
    
    $res = checkJumlahPola(32, 2374);
    echo "Result: " . $res . "\n";

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
