<?php
require_once("Database.php");

class Photograph 
{
    protected static $table_name ="photographs";
    protected static $db_fields = array('id', 'filename', 'type', 'size', 'caption');
    public $id;
    public $filename;
    public $type;
    public $size;
    public $caption;

    private $temp_path;
    protected $upload_dir = "images";
    public $errors = array();

    protected $upload_error = array(
        UPLOAD_ERR_OK           => "No error found",
        UPLOAD_ERR_INI_SIZE     => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE    => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL      => "Partial upload",
        UPLOAD_ERR_NO_FILE      => "No file",
        UPLOAD_ERR_NO_TMP_DIR   => "No temporary directory",
        UPLOAD_ERR_CANT_WRITE   => "Can't write to disk",
        UPLOAD_ERR_EXTENSION    => "File upload stopped by extension"

    );

    public function attach_file($file)
    {
        //perform error checking on the form parameters
        if(!$file || empty($file) || !is_array($file))
        {
            $this->errors[] = "No file was uploaded";
            return false;
        } elseif($file['error'] != 0)
        {
            $this->errors[] = $this->upload_error[$file['error']];
            return false;
        }
            //set object attributes to the form parameters
            $this->temp_path   = $file['tmp_name'];
            $this->filename    = basename($file['name']);
            $this->type        = $file['type'];
            $this->size        = $file['size'];

            return true;
    }

    private static function instantiate($record)
    {
      
        $object = new static;
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
        $object_vars = $this->attributes();

        //here i do not care what the specific values are but just interested if they exist
        return array_key_exists($attribute, $object_vars);
    }
    public static function find_all()
    {
        //pulls in the database instance declared in Database.php
       return static::find_by_sql("SELECT * FROM ".self::$table_name.";");
    }

    public static function find_by_id($id=0)
    {
        global $database;
        $result_array = static::find_by_sql("SELECT * FROM ".self::$table_name. " WHERE 
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
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
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
        if(isset($this->id))
        {
            $this->update();
        } else{
            if(!empty($this->errors)){return false;}
            if(strlen($this->caption) > 255)
            {
                $this->errors[] = "The caption can only be 255 characters long.";
                return false;
            }

            if(empty($this->filename) || empty($this->temp_path))
            {
                $this->errors[] = "The file location was not availble";
                return false;
            }

            //Determine the target path
            $target_path = "../../public/{$this->upload_dir}/{$this->filename}";

            //Make sure that the file does not already exist
            if(file_exists($target_path))
            {
                $this->errors[] = "The file {$this->filename} already exists.";
                return false;
            }
            //attempt to move the file
            if(move_uploaded_file($this->temp_path, $target_path))
            {
               if($this->create())
               {
                   //done with this temp path
                   unset($this->temp_path);
                   return true;
               } 

            } else{
                $this->errors[] = "The file upload failed.";
                return false;
            }

        }
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
        $attribute_pairs[] = "{$key}= '{$value}'";
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