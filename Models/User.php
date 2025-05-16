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
    public function saveDB(){
        try{
            // Save the user in the database
            $stmt = $this->db->prepare('INSERT INTO$users (name, surnames, email, password, rol) VALUES (:name, :surnames, :email, :password, :rol)');
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':surnames', $this->surnames, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
            
            // Assigning "client" as the default role
            $rol = 'cliente';
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);

            $save = $stmt->execute();
            if($save){
                $this->id = $this->db->lastInsertId();
                return true;
            }

            return false;
        } catch (\PDOException $e) {
            // Error saving the user
            error_log("Error saving user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifies if the user exists in the database by email
     * @param string $email
     * @return bool true if it exsits, otherwise false
     */
    public function checkUserExists($email){
        try{
            $query = $this->db->prepare('SELECT * FROM$users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            return $query->rowCOunt() > 0;
        } catch (\PDOException $e) {
            // Error verifying the user
            error_log("Error verifying user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifies if the user exists in the database by email and password
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return mixed user object if it exists, otherwise false
     */
    public function login($email, $password, $remember = false){

        // Validate the email and password
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        // Verify if the user exists
        $query = $this->db->prepare('SELECT * FROM$users WHERE email = :email');
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        if($query->rowCount() > 0){
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // If the password is correct
            if(password_verify($password, $user['password'])){
                // Set the user properties
                $this->id = $user['id'];
                $this->name = $user['name'];
                $this->surnames = $user['surnames'];
                $this->email = $user['email'];
                $this->rol = $user['rol'];
                $this->created_at = $user['created_at'];
                $this->updated_at = $user['updated_at'];
                
                // And now save the user in the session
                $_SESSION['user'] = [
                    'id' => $this->id,
                    'name' => $this->name,
                    'surnames' => $this->surnames,
                    'email' => $this->email,
                    'rol' => $this->rol
                ];

                $_SESSION['login'] = true;

                if($remember){
                    // Set a cookie to remember the user for 30 days
                    setcookie('emailLogin', $this->email, time() + (30 * 24 * 60 * 60), "/");
                } else {
                    // If remember is not ticked, delete the cookie
                    setcookie('emailLogin', '', time() - 3600, "/");
                }

                // Return the user object
                return $this;

            } else {
                // If the password is incorrect
                $_SESSION['error_login'] = "Incorrect password";
                return false;
            }
        } else {
            // If the user does not exist
            $_SESSION['error_login'] = "User does not exist";
            return false;
        }
    }

    /**
     * Obtains the user by ID
     * @param int $id
     * @return mixed user object if it exists, otherwise false
     */
    public function getUserById($id){
        try{
            $query = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                $user_data = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $user_data['id'];
                $this->name = $user_data['name'];
                $this->surnames = $user_data['surnames'];
                $this->email = $user_data['email'];
                $this->rol = $user_data['rol'];
                $this->created_at = $user_data['created_at'];
                $this->updated_at = $user_data['updated_at'];

                return $this;
            }
            return false;
        } catch (\PDOException $e) {
            // Error obtaining the user
            error_log("Error obtaining user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtains a user by email
     * @param string $email
     * @return mixed user object if it exists, otherwise false
     */

     public function getUserByEmail($email){
try {
            $query = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $user_data = $query->fetch(PDO::FETCH_ASSOC);

                $this->id = $user_data['id'];
                $this->name = $user_data['name'];
                $this->surnames = $user_data['surnames'];
                $this->email = $user_data['email'];
                $this->rol = $user_data['rol'];
                $this->created_at = $user_data['created_at'];
                $this->updated_at = $user_data['updated_at'];

                return $this;
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error al obtener usuario por email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Function to find all the users in the database
     * @return array|false array of users if successful, otherwise false
     */
    public function getAll(){
        try {
            $query = $this->db->query("SELECT * FROM usuarios ORDER BY id DESC");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the user in the database
     * @return bool true if successful, otherwise false
     */

    public function update(){
        try{
            $sql = "UPDATE usuarios SET name = :name, surnames = :surnames, email = :email";
            $params = [
                ':name' => $this->name,
                ':surnames' => $this->surnames,
                ':email' => $this->email,
                ':id' => $this->id
            ];

            // If there is a new password, add it to the query
            if(!empty($this->password)){
                $sql .= ", password = :password";
                $params[':password'] = $this->password;
            }

            // If there is a role, add it to the query
            if(!empty($this->rol)){
                $sql .= ", rol = :rol";
                $params[':rol'] = $this->rol;
            }

            $sql .= " WHERE id = :id";
            $update = $this->db->prepare($sql);

            foreach($params as $param => $value){
                if ($param == ':id'){
                    $update->bindParam($param, $value, PDO::PARAM_INT);
                } else {
                    $update->bindParam($param, $value, PDO::PARAM_STR);
                }
            }

            return $update->execute();

        } catch (\PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes the user from the database
     * @return bool true if successful, otherwise false
     */
    public function delete()
    {
        try {
            $query = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();
        } catch (\PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Close the session
     */
    public function logout(){
        $_SESSION['user'] = null;
        $_SESSION['login'] = false;

        // Destroy the cookie
        if(isset($_COOKIE['emailLogin'])){
            setcookie('emailLogin', '', time() - 3600, "/");
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Verifies if the user is admin
     * @return bool true if the user is admin, otherwise false
     */
    public function isAdmin(){
        return $this->rol === 'admin';
    }
    
}   