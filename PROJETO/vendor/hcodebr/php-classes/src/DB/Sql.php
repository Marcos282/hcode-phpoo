<?php 

namespace Hcode\DB;

class Sql {

//	const HOSTNAME = "187.62.224.202";
//	const USERNAME = "root";
//	const PASSWORD = "";
//	const DBNAME = "db_ecommerce";
    
        

	private $conn;
        private $hostname;
        private $username;
        private $password;
        private $dbname;
                

	public function __construct($config_db = array())
	{
        
            $this->hostname = $config_db['HOSTNAME'];
            $this->username = $config_db['USERNAME'];
            $this->password = $config_db['PASSWORD'];
            $this->dbname = $config_db['DBNAME'];
                    

		$this->conn = new \PDO(
			"mysql:dbname=". $this->dbname.";host=".$this->hostname, 
			$this->username,
                        $this->password
			
		);

	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>