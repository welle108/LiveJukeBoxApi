<?php

  class DbOperations{
      private $con;

      function __construct(){
        require_once dirname(__FILE__) . '/DbConnect.php';

        $db = new DbConnect;

        $this->con = $db->connect();
      }

      /*
          Create User function
          Parameters: $first_name, $last_name, $email, $password
          Returns: String containing response message
      */

      public function createUser($first_name, $last_name, $email, $password){
           if(!$this->emailExists($email)){
                $stmt = $this->con->prepare("INSERT INTO users (FirstName, LastName, Email, Password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);
                if($stmt->execute()){
                    return USER_CREATED;
                }else{
                    return USER_FAILURE;
                }
           }
           return USER_EXISTS;
        }

        /*
            Create Artist function
            Parameters: $user_id, $artist_email, $first_name, $last_name
            Returns: String containing response message
        */

        public function createArtist($user_id, $artist_email, $first_name, $last_name){
             if($this->userIdExists($user_id)){
                  $stmt = $this->con->prepare("INSERT INTO artists (UserID, ArtistEmail, FirstName, LastName) VALUES (?, ?, ?, ?)");
                  $stmt->bind_param("ssss", $user_id, $artist_email, $first_name, $last_name);
                  if($stmt->execute()){
                      return ARTIST_CREATED;
                  }else{
                      return ARTIST_FAILURE;
                  }
             }
             return ARTIST_USER_NOT_EXIST;
          }

        /*
            Login User function
            Parameters: $email, $password
            Returns: String containing response message
        */

        public function userLogin($email, $password){
          if($this->emailExists($email)){
              $hashed_password = $this->getUserPasswordByEmail($email);
              if(password_verify($password, $hashed_password)){
                return USER_AUTHENTICATED;
              }else{
                return USER_PASSWORD_INCORRECT;
              }
          }else{
            return USER_NOT_FOUND;
          }
        }

        private function getUserPasswordByEmail($email){
          $stmt = $this->con->prepare("SELECT Password FROM users WHERE email = ?");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $stmt->bind_result($password);
          $stmt->fetch();

          return $password;
        }

      public function getUserByEmail($email){
        $stmt = $this->con->prepare("SELECT ID, FirstName, LastName, Email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $first_name, $last_name, $email);
        $stmt->fetch();
        $user = array();
        $user['id'] = $id;
        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;
        $user['email'] = $email;

        return $user;
      }

      // Checks if user inputted email already exists in DB

      private function emailExists($email){
            $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        private function userIdExists($id){
              $stmt = $this->con->prepare("SELECT id FROM users WHERE id = ?");
              $stmt->bind_param("s", $id);
              $stmt->execute();
              $stmt->store_result();
              return $stmt->num_rows > 0;
          }
  }

?>
