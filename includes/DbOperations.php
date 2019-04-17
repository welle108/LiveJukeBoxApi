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
      if(!$this->userEmailExists($email)){
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
       if(!$this->artistEmailExists($user_id, $artist_email)){
         $stmt = $this->con->prepare("INSERT INTO artists (UserID, ArtistEmail, FirstName, LastName) VALUES (?, ?, ?, ?)");
         $stmt->bind_param("ssss", $user_id, $artist_email, $first_name, $last_name);
         if($stmt->execute()){
             return ARTIST_CREATED;
         }else{
             return ARTIST_FAILURE;
         }
       }
          return USER_ARTIST_EXISTS;
     }
     return ARTIST_USER_NOT_EXIST;
    }

    /*
      Create Show function
      Parameters: $user_id, $artist_email, $first_name, $last_name
      Returns: String containing response message
    */

    public function createShow($user_id, $name){
       if($this->userIdExists($user_id)){
         if(!$this->userShowExists($user_id, $name)){
           $stmt = $this->con->prepare("INSERT INTO shows (UserID, Name) VALUES (?, ?)");
           $stmt->bind_param("ss", $user_id, $name);
           if($stmt->execute()){
               return SHOW_CREATED;
           }else{
               return SHOW_FAILURE;
           }
         }
            return USER_SHOW_EXISTS;
       }
       return SHOW_USER_NOT_EXIST;
    }

    /*
    Create OriginalArtist function
    Parameters: $name
    Returns: String containing response message
    */

    public function createOA($name){
      if(!$this->oaNameExists($name)){
          $stmt = $this->con->prepare("INSERT INTO originalartists (Name) VALUES (?)");
          $stmt->bind_param("s", $name);
          if($stmt->execute()){
              return OA_CREATED;
          }else{
              return OA_FAILURE;
          }
      }
      return OA_EXISTS;
    }

    /*
    Create Song function
    Parameters: $name
    Returns: String containing response message
    */

    public function createSong($title, $original_artist){
      // Check if Original Artist is in DB and create if necessary
      if(!$this->oaNameExists($original_artist)){
            $this->createOA($original_artist);
          }
      $oaid = $this->getOAID($original_artist);
      if(!$this->oaSongExists($oaid, $title)){
        $stmt = $this->con->prepare("INSERT INTO songs (OAID, Title) VALUES (?, ?)");
        $stmt->bind_param("ss", $oaid, $title);
        if($stmt->execute()){
            return SONG_CREATED;
        }else{
            return SONG_FAILURE;
        }
      }

      return SONG_EXISTS;
    }

    /*
    Login User function
    Parameters: $email, $password
    Returns: String containing response message
    */

    public function userLogin($email, $password){
      if($this->userEmailExists($email)){
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

    // Returns user password for given email

    private function getUserPasswordByEmail($email){
      $stmt = $this->con->prepare("SELECT Password FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $stmt->bind_result($password);
      $stmt->fetch();

      return $password;
    }

    // Select user row with email

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

    private function userEmailExists($email){
      $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $stmt->store_result();
      return $stmt->num_rows > 0;
    }

    // Checks if the given user already has an artist with that email

    private function artistEmailExists($user_id, $artist_email){
      $stmt = $this->con->prepare("SELECT * FROM artists WHERE ArtistEmail = ? AND UserID = ?");
      $stmt->bind_param("ss", $artist_email, $user_id);
      $stmt->execute();
      $stmt->store_result();
      return $stmt->num_rows > 0;
    }

    // Checks if the given user already has an show with that name

    private function userShowExists($user_id, $name){
      $stmt = $this->con->prepare("SELECT * FROM shows WHERE Name = ? AND UserID = ?");
      $stmt->bind_param("ss", $name, $user_id);
      $stmt->execute();
      $stmt->store_result();
      return $stmt->num_rows > 0;
    }

    // Checks if the given user already has an show with that name

    private function oaNameExists($name){
      $stmt = $this->con->prepare("SELECT * FROM originalartists WHERE REPLACE(Name, ' ', '') = REPLACE(?, ' ', '')");
      $stmt->bind_param("s", $name);
      $stmt->execute();
      $stmt->store_result();
      return $stmt->num_rows > 0;
    }

    // Checks validity of given user id

    private function userIdExists($id){
      $stmt = $this->con->prepare("SELECT id FROM users WHERE id = ?");
      $stmt->bind_param("s", $id);
      $stmt->execute();
      $stmt->store_result();
      return $stmt->num_rows > 0;
    }

    // Checks if original artist already has song by given title

    private function oaSongExists($oaid, $title){
      $stmt = $this->con->prepare("SELECT title FROM songs WHERE oaid = ? AND title = ?");
      $stmt->bind_param("ss", $oaid, $title);
      $stmt->execute();
      $stmt->store_result();
      return $stmt->num_rows > 0;
    }

    // Gets ID of Original Artist

    private function getOAID($name){
      $stmt = $this->con->prepare("SELECT ID from originalartists WHERE Name = ?");
      $stmt->bind_param("s", $name);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      return  $id;
    }
  }

?>
