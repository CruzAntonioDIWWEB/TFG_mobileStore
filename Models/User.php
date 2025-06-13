<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

/**
 * Class to represent a User
 */
class User
{

    //Properties
    private $id;
    private $name;
    private $surnames;
    private $email;
    private $password;
    private $role;
    private $db;

    //Constructor
    public function __construct(){
        //Connection to the database
        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();

        error_log("User model created, DB connection: " . ($this->db ? 'success' : 'failed'));
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

    public function getRole(){
        return $this->role;
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

    public function setPassword($password){
        $this->password = $password;
    }

    public function setSurnames($surnames){
        $this->surnames = $surnames;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setRole($role){
        $this->role = $role;
    }

    /**
     * Saves the user in the database
     * @return bool true if successful, otherwise false
     */
public function saveDB(){
    try {
        error_log("Starting saveDB method");
        error_log("User data - Name: {$this->name}, Email: {$this->email}");
        
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        error_log("Password hashed successfully");

        $stmt = $this->db->prepare('INSERT INTO users (name, surnames, email, password) VALUES (:name, :surnames, :email, :password)');
        error_log("Query prepared");
        
        $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindParam(':surnames', $this->surnames, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        error_log("Parameters bound");

        $save = $stmt->execute();
        error_log("Query executed, result: " . ($save ? 'true' : 'false'));

        if ($save) {
            $this->id = $this->db->lastInsertId();
            error_log("Last insert ID: " . $this->id);
            return true;
        }

        return false;
    } catch (\PDOException $e) {
        error_log("PDO Error in saveDB: " . $e->getMessage());
        return false;
    }
}

    /**
     * Verifies if the user exists in the database by email
     * @param string $email
     * @return bool true if it exists, otherwise false
     */
    public function checkUserExists($email){
        try{
            $query = $this->db->prepare('SELECT * FROM users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
    
            return $query->rowCount() > 0;
        } catch (\PDOException $e) {
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
        $query = $this->db->prepare('SELECT * FROM users WHERE email = :email');
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
                $this->role = $user['role'];
                
                // And now save the user in the session
                $_SESSION['user'] = [
                    'id' => $this->id,
                    'name' => $this->name,
                    'surnames' => $this->surnames,
                    'email' => $this->email,
                    'role' => $this->role
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
            $query = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                $user_data = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $user_data['id'];
                $this->name = $user_data['name'];
                $this->surnames = $user_data['surnames'];
                $this->email = $user_data['email'];
                $this->role = $user_data['role'];

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
            $query = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $user_data = $query->fetch(PDO::FETCH_ASSOC);

                $this->id = $user_data['id'];
                $this->name = $user_data['name'];
                $this->surnames = $user_data['surnames'];
                $this->email = $user_data['email'];
                $this->role = $user_data['role'];

                return $this;
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error getting user by email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Function to find all the users in the database
     * @return array|false array of users if successful, otherwise false
     */
    public function getAll(){
        try {
            $query = $this->db->query("SELECT * FROM users ORDER BY id DESC");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            error_log("Error getting all users: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the user in the database
     * @return bool true if successful, otherwise false
     */

    public function updateDB(){
    try{
        // Build the base SQL query
        $sql = "UPDATE users SET name = :name, surnames = :surnames, email = :email";
        
        // Hash password if provided
        $hashedPassword = null;
        if(!empty($this->password)){
            $sql .= ", password = :password";
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        }

        // Add role if provided
        if(!empty($this->role)){
            $sql .= ", role = :role";
        }

        $sql .= " WHERE id = :id";
        
        $update = $this->db->prepare($sql);
        
        // Bind parameters individually (avoiding the reference issue)
        $update->bindParam(':id', $this->id, PDO::PARAM_INT);
        $update->bindParam(':name', $this->name, PDO::PARAM_STR);
        $update->bindParam(':surnames', $this->surnames, PDO::PARAM_STR);
        $update->bindParam(':email', $this->email, PDO::PARAM_STR);
        
        // Only bind password if it's provided
        if(!empty($this->password)){
            $update->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        }
        
        // Only bind role if it's provided
        if(!empty($this->role)){
            $update->bindParam(':role', $this->role, PDO::PARAM_STR);
        }
        
        // Debug logging
        error_log("SQL: " . $sql);
        error_log("Updating user - ID: {$this->id}, Name: {$this->name}, Email: {$this->email}");
        
        $result = $update->execute();
        
        if ($result) {
            error_log("User update successful for ID: " . $this->id);
        } else {
            error_log("User update failed for ID: " . $this->id);
            $errorInfo = $update->errorInfo();
            error_log("SQL Error: " . print_r($errorInfo, true));
        }
        
        return $result;

    } catch (\PDOException $e) {
        error_log("Error updating user: " . $e->getMessage());
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
            $query = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
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
        return $this->role === 'admin';
    }
    
}   