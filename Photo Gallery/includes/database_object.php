<?php

class DatabaseObject
{



    private static function instantiate($record)
    {
        //simple, long form to check that record exists and is an array
        //the get_called_class method helps resolve the erly static binding problem
        //and allows for late static binding
        $object = new static;
      //  $object->id       = $record['id'];
       // $object->username = $record['username'];
       // $object->password = $record['password'];
       // $object->first_name = $record['first_name'];
       // $object->last_name  = $record['last_name'];

       //more dynamic and short approach
       foreach($record as $attribute => $value)
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
        $object_vars = get_object_vars($this);

        //here i do not care what the specific values are but just interested if they exist
        return array_key_exists($attribute, $object_vars);
    }
    public static function find_all()
    {
        //pulls in the database instance declared in Database.php
       return static::find_by_sql("SELECT * FROM users");
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

}

?>