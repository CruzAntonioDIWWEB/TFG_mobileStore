<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

/**
 * CLase to represent a User
 */
class User
{

    //Properties
    private $id;
    private $name;
    private $surnames;
    private $email;
    private $password;
    private $rol;
    private $created_at;
    private $updated_at;
    private $db;

    //Constructor
    public function __construct(){
        //Conection to the database
        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();
    }

    //Getters and Setters
        public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getSurnames(){
        return $this->surnames;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getRol(){
        return $this->rol;
    }

    public function getCreatedAt(){
        return $this->created_at;
    }

    public function getUpdatedAt(){
        return $this->updated_at;
    }

    public function getFullName(){
        return $this->name . ' ' . $this->surnames;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setSurnames($surnames){
        $this->surnames = $surnames;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    /**
     * Saves the user in the database
     * @return bool true if successful, otherwise false
     */
    public function save(){
        try{
            // Save the user in the database
            $register = $this->db->prepare('INSERT INTO usuarios (name, surnames, email, password, rol) VALUES (:name, :surnames, :email, :password, :rol)');
            $register->bindParam(':name', $this->name, PDO::PARAM_STR);
            $register->bindParam(':surnames', $this->surnames, PDO::PARAM_STR);
            $register->bindParam(':email', $this->email, PDO::PARAM_STR);
            $register->bindParam(':password', $this->password, PDO::PARAM_STR);
            
            // Assigning "client" as the default role
            $rol = 'cliente';
            $register->bindParam(':rol', $rol, PDO::PARAM_STR);

            $save = $register->execute();
            if($save){
                $this->id = $this->db->lastInsertId();
                return true;
            }

            return false;
        } catch (\PDOException $e) {
            // Error al guardar el usuario
            error_log("Error al guardar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifies if the user exists in the database by email
     * @param string $email
     * @return bool true if it exsits, otherwise false
     */
    public function exists($email){
        try{
            $query = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            return $query->rowCOunt() > 0;
        } catch (\PDOException $e) {
            // Error al verificar el usuario
            error_log("Error al verificar usuario: " . $e->getMessage());
            return false;
        }
    }

}   