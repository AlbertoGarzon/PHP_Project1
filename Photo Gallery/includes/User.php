<?php
require_once('Database.php');
require_once('database_object.php');
class User extends DatabaseObject
{
    protected static $table_name ="users";
    //these attributes correspond to database fields
    protected static $db_fields = array('id', 'username', 'password',
    'first_name', 'last_name' );
    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;

    public static function find_all()
    {
        //pulls in the database instance declared in Database.php
       return self::find_by_sql("SELECT * FROM users");
    }

    public static function find_by_id($id=0)
    {
        global $database;
        $result_array = static::find_by_sql("SELECT * FROM users WHERE 
        id={$id} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql="")
    {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while($row = $database->fetch_array($result_set))
        {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    public function full_name()
    {
        if(isset($this->first_name) && isset($this->last_name))
        {
            return $this->first_name." ".$this->last_name;
        } else{
            return "";
        }
    }

    public static function authenticate($username="", $password="")
    {
        global $database;
        $username = $database->mysql_prep($username);
        $password = $database->mysql_prep($password);

        $sql = "SELECT * FROM users ";
        $sql .= "WHERE username = '{$username}' ";
        $sql .= "AND password = '{$password}' ";
        $sql .= "LIMIT 1";

        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    private static function instantiate($record)
    {
        //simple, long form to check that record exists and is an array
        $object = new self;
      //  $object->id       = $record['id'];
       // $object->username = $record['username'];
       // $object->password = $record['password'];
       // $object->first_name = $record['first_name'];
       // $object->last_name  = $record['last_name'];

       //more dynamic and short approach
       foreach($record as $attribute=>$value)
       {
           if($object->has_attribute($attribute))
           {
               $object->$attribute = $value;
           }
       }
       return $object;
    }

    private function has_attribute($attribute)
    {
        //get_object_vars returns an associative array with all attributes
        //including priate ones as the keys and their current values are the value
        $object_vars = $this->attributes();

        //here i do not care what the specific values are but just interested if they exist
        return array_key_exists($attribute, $object_vars);
    }

    protected function attributes()
    {
        $attributes = array();
        foreach(self::$db_fields as $field)
        {
            if(property_exists($this, $field))
            {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    protected function sanitized_attributes()
    {
        global $database;
        $clean_attributes = array();
        //sanitize the value before submitting
        foreach($this->attributes() as $key => $value)
        {
            $clean_attributes[$key] = $database->mysql_prep($value);
        }
        return $clean_attributes;
    }

    public function save()
    {
        return isset($this->id) ? $this->update() : $this->create();
    }

   protected function create()
   {
       global $database;
       $attributes = $this->sanitized_attributes();
       $sql = "INSERT INTO ".self::$table_name." (";
       $sql .= join(", ", array_keys($attributes));
       $sql .= ") VALUES ('";
       $sql .= join(", ", array_values($attributes));
       $sql .="')";
       if($database->query($sql))
       {
           $this->id = $database->insert_id();
           return true;
       } else {
        return false;
       }
   }

   protected function update()
{
    global $database;
    $attributes = $this->sanitized_attributes();
    $attribute_pairs = array();
    foreach($attributes as $key => $value)
    {
        $attribute_pairs[] = "{$key}='{$value}'";
    }
    $sql = "UPDATE ".self::$table_name." SET ";
    $sql .= join(", ", $attribute_pairs);
    $sql .=" WHERE id=" . $database->mysql_prep($this->id);

    $database->query($sql);
    return ($database->affected_rows() == 1) ? true : false;
}

public function delete()
{
    global $database;
    $sql = "DELETE FROM ".self::$table_name ;
    $sql .=" WHERE id=". $database->mysql_prep($this->id);
    $sql .=" LIMIT 1";
    $database->query($sql);
    return ($database->affected_rows() == 1) ? true : false;
}


}

?>