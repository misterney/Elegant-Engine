<?php
class DataBase_pdo extends DataBase {
    
    public function __construct($data) {
	$this->data = Settings::load('database');
        
        $this->data['port'] = ';port=' . $this->data['port'];
        $this->data['charset']= ';charset=' . $this->data['charset'];

        if(!empty($this->data['port'])) {
            $this->db = new PDO('mysql:dbname='.$this->data['base'].';host='.$this->data['host'].$this->data['port'].$this->data['charset'], $this->data['user'], $this->data['pass']);
        }
        else {
            $this->db = new PDO('mysql:dbname='.$this->data['base'].';host='.$this->data['host'].$this->data['charset'], $this->data['user'], $this->data['pass']);
        }
    }
    
    public function set_charset($charset='cp1251_koi8') {
        
    }
    
    public function fetch_array_all($query,$param='') {
        if($q = $this->query($query)) {
            if($param!='') {
                return $q->fetchAll($param);
            }
            else {
                return $q->fetchAll();
            }
        }
        else {
            return false;
        }
    }
    
    public function fetch_array($obj, $param='') {
        if(!is_object($obj)) {
            return false;
        }
            if($param!='') {
                return $obj->fetch($param);
            }
            else {
                return $obj->fetch();
            }
    }
    
    public function fetch_object($obj) {
        if(!is_object($obj)) {
            return false;
        }
        return $obj->fetchObject();
    }
    
    public function execute($query, $param='') {
        if($param!='') {
            $sth = $this->db->prepare($query);
	     $sth->execute($param);
	     return $sth->fetch(PDO::FETCH_ASSOC);
        } else {
            $sth = $this->db->prepare($query);
	     $sth->execute();
	     return $sth->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    public function query($query) {
        return $this->db->query($query);
    }
    
    public function quote($string) {
        return $this->db->quote($string);
    }
    
}

