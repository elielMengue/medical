<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private $host = 'localhost';
    private $dbname = 'gestion_medicale';
    private $username = 'root';
    private $password = '';
    public $pdo;

    public function getConnection() {
        $this->pdo = null;
        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname,
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // En production, ne pas afficher l'erreur détaillée
            error_log("Erreur de connexion BDD: " . $e->getMessage());
            throw new \Exception("Erreur de connexion au serveur");
        }
        return $this->pdo;
    }
}

?>