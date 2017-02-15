<?php 
require_once("MysqlPdo.php");
class User extends MysqlPdo {
    public $user_id;
    public $name;
    public $address;
    public $picture;
    public function addUserTest(User $User){
        $sql = "INSERT INTO users (name,address,picture) VALUES (:name, :address, :picture)";
        $insert = $this->query($sql,array("name"=>"$User->name","address"=>"$User->address","picture"=>"$User->picture")); 
        $user_id = $this->lastInsertId();  
        if (!$user_id) {
            throw new Exception('Usuario no creado');
        }
        $User->user_id = $user_id;
        return $User;
    }
}
?>