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
    Returns: Array with result message and id of new or existing song
    */

    public function createSong($title, $original_artist, $artist_id, $url){
      // Check if Original Artist is in DB and create if necessary
      if(!$this->oaNameExists($original_artist)){
            $this->createOA($original_artist);
          }
      $oaid = $this->getOAID($original_artist);
      $song_exists = $this->oaSongExists($oaid, $title);
      if(!$song_exists['exists']){
        $stmt = $this->con->prepare("INSERT INTO songs (OAID, Title) VALUES (?, ?)");
        $stmt->bind_param("ss", $oaid, $title);
        if($stmt->execute()){
          $result = array();
          $result['message'] = SONG_CREATED;
          $result['id'] = $this->con->insert_id;
          $result['link_created'] = $this->addSongURL($result['id'], $artist_id, $url);
          $this->addSongtoArtist($result['id'], $artist_id);
            return $result;
        }else{
          $result['message'] = SONG_FAILURE;
            return $result;
        }
      }

      $result['message'] = SONG_EXISTS;
      $result['id'] = $song_exists['id'];
      $url_exists = $this->songUrlExists($result['id'], $artist_id);
      if($url_exists){
        $result['url_exists'] = true;
        return $result;
      }
      $result['link_created'] = $this->addSongURL($result['id'], $artist_id, $url);
      $this->addSongtoArtist($result['id'], $artist_id);
      return $result;
    }

    /*
      Inserts song url into songlinks table
    */

    private function addSongURL($song_id, $artist_id, $url){
      $stmt = $this->con->prepare("INSERT INTO songlinks (SongID, ArtistID, URL) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $song_id, $artist_id, $url);
      if($stmt->execute()){
        return SONG_URL_CREATED;
      }
      return SONG_URL_FAILURE;
    }

    /*
      Creates relationship between song and artist
    */

    private function addSongtoArtist($song_id, $artist_id){
      $stmt = $this->con->prepare("INSERT INTO artistsongs (ArtistID, SongID) VALUES (?, ?)");
      $stmt->bind_param("ss", $artist_id, $song_id);
      if($stmt->execute()){
        return ARTIST_SONG_CREATED;
      }
      return ARTIST_SONG_FAILURE;
    }

    //Checks if user inpu url is new and inserts if true

    private function songUrlExists($song_id, $artist_id){
        $stmt = $this->con->prepare("SELECT URL FROM songlinks WHERE SongID = ? AND ArtistID = ?");
        $stmt->bind_param("ss", $song_id, $artist_id);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
          return true;
        }
        return false;
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
      $stmt = $this->con->prepare("SELECT ID FROM songs WHERE oaid = ? AND title = ?");
      $stmt->bind_param("ss", $oaid, $title);
      $stmt->execute();
      $stmt->store_result();
      if($stmt->num_rows > 0){
        $stmt = $this->con->prepare("SELECT ID FROM songs WHERE oaid = ? AND title = ?");
        $stmt->bind_param("ss", $oaid, $title);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $result = array();
        $result['exists'] = true;
        $result['id'] = $id;
        return $result;
      }else{
        $result = array();
        $result['exists'] = false;
        return $result;
      }

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
