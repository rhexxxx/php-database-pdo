<?php 

class DB{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;

    private $pdo;
    private $stmt;

    public function __construct(){
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->name;
        $option =[
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try{
            $this->pdo = new PDO($dsn,$this->user,$this->pass,$option);
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }
    
    public function create($table, $data ) {
        $fields = implode(', ',array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        try {
            $this->stmt = $this->pdo->prepare($sql);
            foreach ($data as $key => $value) {
                $this->stmt->bindValue(":$key", $value);
            }
            return $this->stmt->execute();
        } catch(PDOException $e) {
            die($e->getMessage());
        }   
    }

    public function read($table, $where = []){
        $sql = "SELECT * FROM $table"; 
        if(!empty($where)){
            $conditions = [];
            foreach ($where as $key => $value){
                $conditions[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        try {
            $this->stmt = $this->pdo->prepare($sql);
            foreach($where as $key => $value) {
                $this->stmt->bindValue(":$key", $value);
            }
            $this->stmt->execute();
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function update($table, $data, $where){
        $fields = "";
        $conditions = "";

        foreach ($data as $key =>$value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ', ');

        foreach ($where as $key => $value){
            $conditions .= "$key = :where_$key AND ";
        }
        $conditions = rtrim($conditions, " AND ");  
        $sql = "UPDATE $table SET $fields WHERE $conditions";

        try {
            $this->stmt = $this->pdo->prepare($sql);
            foreach($data as $key => $value){
                $this->stmt->bindValue(":$key", $value);
            }
            foreach($where as $key => $value){
                $this->stmt->bindValue(":where_$key", $value);
            }
            return $this->stmt->execute();
        } catch (PDOException $e){
            die($e->getMessage());
        }
    }

    public function delete($table, $where) {
        $conditions = "";
        foreach ($where as $key => $value) {
            $conditions .= "$key = :$key AND ";
        } 
        $conditions = rtrim($conditions, " AND ");
        $sql = "DELETE FROM $table WHERE $conditions";
        try {
                $this->stmt = $this->pdo->prepare($sql);
                foreach($where as $key => $value) {
                    $this->stmt->bindValue(":$key", $value);
                }
                return $this->stmt->execute();
            } catch(PDOException $e){
                die($e->getMessage());
            }
    }
}