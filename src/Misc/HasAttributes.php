<?php


namespace Abiturma\PhpFints\Misc;


trait HasAttributes
{
    protected $attributes = [];
    
    protected $appends = []; 

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        
        $getter = "get{$this->toStudlyCase($name)}Attribute"; 
        
        if(method_exists($this,$getter)) {
            return $this->$getter(); 
        }
        
        return null;
    }

    public function getAttributes()
    {
        return $this->attributes; 
    }

    public function getAppendedAttributes()
    {
        $result = []; 
        
        foreach($this->appends as $value) {
            $result[$value] = $this->$value; 
        }
        
        return $result; 
        
    }
    
    public function toArray()
    {
        return array_merge($this->attributes,$this->getAppendedAttributes()); 
    }

    protected function toStudlyCase($string)
    {
        $parts = explode('_',$string);
        $parts = array_map(function($part) {
            return ucfirst($part);
        },$parts);
        return implode('',$parts); 
    }

    
}