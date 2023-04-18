<?php
trait Database{
    private $connection;

    /**
     * Connect wit database.
     *
     * @return void
     */
    public function connect(){
        $servername = "localhost";
        $username = "root";
        $password = "4815162342";
        $dbname = "axisbits";

        $this->connection = mysqli_connect($servername, $username, $password, $dbname);

        if(!$this->connection){
            throw new Exception("Connection failed: " . mysqli_connect_error());
        }
    }

    /**
     * Run sql query
     *
     * @return mixed
     */
    public function query($sql){
        try{
            $result = mysqli_query($this->connection, $sql);
            if($result === false){
                throw new Exception(mysqli_error($this->connection));
            }
            return $result;
        }catch(Exception $e){
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Create table in sql
     *
     * @param string $table - name new table
     * @param string $columns - columns in new table
     * @return void
     */
    public function tableCreate($table, $columns){
        $this->connect();
        $sql = "CREATE TABLE " . $table . " (" . $columns . ")";
        $this->query($sql);
    }

    /**
     * Insert data in sql table
     *
     * @param string $table - table, where data will be inserted
     * @param string $columns - columns in table
     * @param string $values - values, that will be inserted to table
     * @return void
     */
    public function tableInsert($table, $columns, $values){
        $this->connect();
        $sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES ('" . $values . "')";
        $this->query($sql);
    }

    /**
     * Get id of object from table, by field and value
     *
     * @param string $table
     * @param string $field
     * @param string $value
     * @return void
     */
    public function tableSelectId($table, $field, $value){
        $this->connect();
        $result = $this->query("SELECT id FROM " . $table . " WHERE " . $field . " = '" . $value . "' LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        return $row && isset($row['id']) ? $row['id'] : false;
    }
}

abstract class MainObject{
    use Database;

    public $name;
    public $table;
    public $id;

    //for table creation
    abstract function table();

    //for insert data to sql
    abstract function insert();

    /**
     * Get id of object by name, or insert object to db and get id to $id value
     *
     * @return void
     */
    function getId(){
        $this->id = $this->tableSelectId($this->table, "name", $this->name);
        if(!$this->id){
            $this->insert();
            $this->id = $this->tableSelectId($this->table, "name", $this->name);
        }
    }
}

class Company extends MainObject{
    use Database;

    public function __construct($name){
        $this->name = $name;
        $this->table = "companies";
        $this->getId();
    }

    public function table(){
        $this->tableCreate($this->table, "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(250) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    public function insert(){
        $this->tableInsert($this->table, "name", $this->name);
    }
}

class Office extends MainObject{
    use Database;

    public $company;

    public function __construct($name, $company){
        $this->name = $name;
        $this->company = $company;
        $this->table = "offices";
        $this->getId();
    }

    public function table(){
        $this->tableCreate($this->table, "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(250) NOT NULL, company_id int NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    public function insert(){
        $this->tableInsert($this->table, "name, company_id", implode("','", [$this->name, $this->company]));
    }

    /**
     * Get array of offices, sorted by total salary of employees
     *
     * @return array
     */
    public function getSalarySorted(){
        $sql = "SELECT offices.name, SUM(employees.salary) AS total_salary 
                FROM offices 
                JOIN employees ON offices.id = employees.office_id 
                GROUP BY offices.name 
                ORDER BY total_salary DESC";
        $this->connect();
        $rows = $this->query($sql);
        $results = [];
        if($rows){
            while($row = mysqli_fetch_assoc($rows)){
                $results[] = $row;
            }
        }
        return $results;
    }

    /**
     * Get offices, that has count of employees between some range
     *
     * @param string $start1
     * @param string $end1
     * @param string $start2
     * @param string $end2
     * @return array
     */
    public function getEmployeeBetween($start1, $end1, $start2, $end2){
        $results = [];
        if($start1 && $end1 && $start2 && $end2){
            $sql = "SELECT offices.name, COUNT(*) AS employee_count
                FROM offices
                JOIN employees ON offices.id = employees.office_id
                GROUP BY offices.id";
            $this->connect();
            $rows = $this->query($sql);
            if($rows){
                while($row = mysqli_fetch_assoc($rows)){
                    if(($row['employee_count'] > $start1 && $row['employee_count'] < $end1) || ($row['employee_count'] > $start2 && $row['employee_count'] < $end2)){
                        $results[] = $row;
                    }
                }
            }
        }
        return $results;
    }
}

class Employee extends MainObject{
    use Database;

    public $office;
    public $salary;

    public function __construct($name, $office, $salary){
        $this->name = $name;
        $this->office = $office;
        $this->salary = $salary;
        $this->table = "employees";
        $this->getId();
    }

    public function table(){
        $this->tableCreate($this->table, "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(250) NOT NULL, salary int NOT NULL, office_id int NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    public function insert(){
        $this->tableInsert($this->table, "name, salary, office_id", implode("','", [$this->name, $this->salary, $this->office]));
    }
}