<?php 
	define('ERROR', -1);

	class MySQL_DB {
		private $pdo;
		public $erMessage;

		public function __construct($host, $db, $user, $pass)
		{
			//initialization of variables that
			//are inside the class for further use

			$dsn = 'mysql:host='.$host.';dbname='.$db.';charset=UTF8';
			$this->pdo = $this->connector($dsn, $user, $pass);
		}

		private function connector($dsn, $user, $pass)
		{
			//connection to mysql database.
			//on successful connection, the function 
			//returns a PDO object. if the connection 
			//is unsuccessful, the error will be written
			//to the "error" variable and return false. 
			//thus, it will be convenient to handle the error 
			//from outside the function.

			try {
				//create a PDO class object while opening a mysql connection
				$pdo = new PDO($dsn, $user, $pass);

				//attributes are applied so that errors encountered during
				//sql query can be tested
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			} catch (Exception $er) {
				$this->erMessage = $er->getMessage();
				return ERROR;
			}

			return $pdo;
		}

		private function ft_execute($query)
		{
			try {
				$stmt = $this->pdo->prepare($query);
				$stmt->execute();
			} catch (Exception $er) {
				$this->erMessage = $er->getMessage();
				return ERROR;
			}

			return true;
		}

		public function select($col_name, $table_name, $limit = NULL, $condition = NULL)
		{

			$query = 'SELECT '.$col_name.' FROM '.$table_name;
			$query .= $condition? ' WHERE '.$condition: '';
			$query .= $limit? ' LIMIT '.$limit: '';

			if($this->pdo === ERROR)
				return ERROR;

			try {
				$stmt = $this->pdo->query($query);
				
			} catch (Exception $er) {
				$this->erMessage = $er->getMessage();
				return ERROR;
			}

			return $stmt->fetchAll();
		}

		public function insert($table_name, $keys, $values)
		{
			$query = 'INSERT INTO '.$table_name.'('.implode(', ', $keys).
			') '.'VALUES('.implode(', ',$values).')';

			if($this->pdo === ERROR)
				return ERROR;

			return $this->ft_execute($query);
		}

		public function delete($table_name, $condition)
		{
			$query = 'DELETE FROM '.$table_name.' WHERE '.$condition;

			if($this->pdo === ERROR)	 
				return ERROR;

			return $this->ft_execute($query);
		}

		public function delete_all($table_name)
		{
			$query = 'DELETE FROM '.$table_name;

			if($this->pdo === ERROR)
				return ERROR;

			return $this->ft_execute($query);
		}

		public function update($table_name, $keys, $values, $condition)
		{
			if($this->pdo === ERROR)
				return ERROR;

			$query = 'UPDATE '.$table_name.' SET ';
			$query .= $keys[0].'='.$values[0];

			for ($i=1; $i < count($keys); $i++) { 
				$query .= ', '.$keys[$i].'='.$values[$i];
			}

			$query .= ' WHERE '.$condition;

			return $this->ft_execute($query);
		}

		public function update_all($table_name, $keys, $values)
		{
			if($this->pdo === ERROR)
				return ERROR;

			$query = 'UPDATE '.$table_name.' SET ';
			$query .= $keys[0].'='.$values[0];

			for ($i=1; $i < count($keys); $i++) { 
				$query .= ', '.$keys[$i].'='.$values[$i];
			}

			return $this->ft_execute($query);
		}
	}
?>