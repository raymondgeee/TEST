<?php
class PMSDatabase
{
    private $sqlQuery;
    private $DBServer;
    private $DBUser;
    private $DBPass; 
    private $DBName; 
    private $db; 

    private $tableName; 
    private $fields; 
    private $values; 

    public function __construct()
    {
        
        $this->DBServer = "localhost";
        $this->DBUser = "root";
        $this->DBPass = "";
        $this->DBName = "";
        $this->dbConnect();
        
    }

    private function dbConnect()
    {
        $this->db = new mysqli($this->DBServer, $this->DBUser, $this->DBPass, $this->DBName);
        $this->db->set_charset("utf8");
        if ($this->db->connect_error)
        {
            trigger_error('Database connection failed: '  . $this->db->connect_error, E_USER_ERROR);
        }
    }

    public function setTableName($table)
    {
        $this->tableName = $table;
    }

    public function setFieldsValues($field, $value)
    {
        $this->fields[] = $field;
        $this->values[] = $this->db->real_escape_string($value);
    }

    public function insert()
    {
        $sqlDataQuery = $this->sqlQuery;
        
        if($sqlDataQuery != "")
        {
            $sql = "INSERT INTO ".$this->tableName." (".implode(", ",$this->fields).") VALUES ({$sqlDataQuery})";
            $queryInsert = $this->db->query($sql);
            if(!$queryInsert)
            {
                return $this->db->error;
            }
        }
        else
        {
            $sql = "INSERT INTO ".$this->tableName." (".implode(", ",$this->fields).") VALUES ('".implode("', '",$this->values)."')";
            $queryInsertAgain = $this->db->query($sql);
            if(!$queryInsertAgain)
            {
                return $this->db->error;
            }
        }

        $this->sqlQuery = "";
        $this->fields = Array ();
        $this->values = Array ();
    }

    public function update($whereQuery)
    {
        $x = 0;
        $valuesArray = Array();
        foreach ($this->fields AS $key) 
        {
            $valuesArray[] = $key."='".$this->values[$x]."'";
        }

        $sql = "UPDATE ".$this->tableName." SET ".implode(", ", $valuesArray)." WHERE ".$whereQuery;
        $queryUpdate = $this->db->query($sql);
        if(!$queryUpdate)
        {
            return $this->db->error;
        }
        else
        {
            return $this->db->affected_rows;
        }

        $this->fields = Array ();
        $this->values = Array ();
    }

    public function delete($whereQuery)
    {
        $sql = "DELETE FROM ".$this->tableName." WHERE ".$whereQuery;
        $queryDelete = $this->db->query($sql);
        if(!$queryDelete)
        {
            return $this->db->error;
        }
        else
        {
            return $this->db->affected_rows;
        }
    }
    
    public function getLastRecords($keyValue, $whereQuery)
    {
        $id = "ERROR FOUND ON YOUR QUERY";
        $sql = "SELECT ".$keyValue." FROM ".$this->tableName." ".$whereQuery;
        $queryLastId = $this->db->query($sql);
        if(!$queryLastId)
        {
            return $this->db->error;
        }
        else
        {
            if($queryLastId AND $queryLastId->num_rows > 0)
            {
                $resultLastId = $queryLastId->fetch_assoc();
                $id = $resultLastId[$keyValue];
            }

            return $id;
        }
    }

    public function getRecords()
    {
        $results = Array ();
        $sql = $this->sqlQuery;
        $queryRecords = $this->db->query($sql);
        if($queryRecords AND $queryRecords->num_rows > 0)
        {
            while($resultRecords = $queryRecords->fetch_assoc())
            {
                $results[] = $resultRecords;
            }
        }

        $this->sqlQuery = "";
        return $results;
    }

    public function setSQLQuery($sqlData)
    {
        $this->sqlQuery = $sqlData;
    }
}
?>
