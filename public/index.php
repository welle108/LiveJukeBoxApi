<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../includes/DbOperations.php';
$app = new \Slim\App([
    'settings'=>[
          'displayErrorDetails'=>true]]);

/*
  endpoint: createUser
  parameters: FirstName, LastName, Email, Password
  method: POST
*/

$app->post('/createuser', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('first_name', 'last_name', 'email', 'password'), $request, $response)){
        $request_data = $request->getParsedBody();
        $first_name = $request_data['first_name'];
        $last_name = $request_data['last_name'];
        $email = $request_data['email'];
        $password = $request_data['password'];
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $db = new DbOperations;
        $result = $db->createUser($first_name, $last_name, $email, $hash_password);

        if($result == USER_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'User created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == USER_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == USER_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'User Already Exists';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/*
  endpoint: createArtist
  parameters: $user_id, $artist_email, $first_name, $last_name
  method: POST
*/

$app->post('/createartist', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('user_id', 'artist_email', 'first_name', 'last_name'), $request, $response)){
        $request_data = $request->getParsedBody();
        $user_id = $request_data['user_id'];
        $artist_email = $request_data['artist_email'];
        $first_name = $request_data['first_name'];
        $last_name = $request_data['last_name'];
        $db = new DbOperations;
        $result = $db->createArtist($user_id, $artist_email, $first_name, $last_name);

        if($result == ARTIST_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Artist created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == ARTIST_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to create Artist';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == ARTIST_USER_NOT_EXIST){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Artist User does not exist';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == USER_ARTIST_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'User already has Artist with that email';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/*
  endpoint: createShow
  parameters: $user_id
  method: POST
*/

$app->post('/createshow', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('user_id', 'name'), $request, $response)){
        $request_data = $request->getParsedBody();
        $user_id = $request_data['user_id'];
        $name = $request_data['name'];
        $db = new DbOperations;
        $result = $db->createShow($user_id, $name);

        if($result == SHOW_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Show created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == SHOW_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to create Show';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == SHOW_USER_NOT_EXIST){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Show User does not exist';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == USER_SHOW_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'User already has Show with that name';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});


/*
  endpoint: createSong
  parameters: $title, $original_artist, $artist_id, $url
  method: POST
*/

$app->post('/createsong', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('title','original_artist','artist_id', 'url'), $request, $response)){
        $request_data = $request->getParsedBody();
        $title = $request_data['title'];
        $original_artist = $request_data['original_artist'];
        $artist_id = $request_data['artist_id'];
        $url = $request_data['url'];
        $db = new DbOperations;
        $result = $db->createSong($title, $original_artist, $artist_id, $url);

        if($result['message'] == SONG_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Song created successfully. ID: ' . $result['id'];
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result['message'] == SONG_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to create Song';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result['message'] == SONG_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Song already exists. ID: ' . $result['id'];
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/*
  endpoint: addArtistToShow
  parameters: $artist_id, $show_id
  method: POST
*/

$app->post('/addartistshow', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('artist_id', 'show_id'), $request, $response)){
        $request_data = $request->getParsedBody();
        $artist_id = $request_data['artist_id'];
        $show_id = $request_data['show_id'];
        $db = new DbOperations;
        $result = $db->addArtistToShow($artist_id, $show_id);

        if($result == SHOW_ARTIST_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Artist added to Show successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == SHOW_ARTIST_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to add Artist to Show';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == SHOW_ARTIST_EXIST){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Artist is already in Show';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/*
  endpoint: addSongToShow
  parameters: $show_id, $song_id, $artist_id
  method: POST
*/

$app->post('/songtoqueue', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('show_id', 'song_id', 'artist_id'), $request, $response)){
        $request_data = $request->getParsedBody();
        $show_id = $request_data['show_id'];
        $song_id = $request_data['song_id'];
        $artist_id = $request_data['artist_id'];
        $db = new DbOperations;
        $result = $db->addSongToShow($show_id, $song_id, $artist_id);

        if($result == SHOW_SONG_ADDED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Song added to Show successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == SHOW_SONG_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to add Song to Show.';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/*
endpoint: getUserArtists()
parameters: $email, $password
method: POST

Returns all Artists belonging to User after verification
*/

$app->post('/getuserartists', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('email', 'password'), $request, $response)){
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];
        $password = $request_data['password'];
        $db = new DbOperations;
        $artists = $db->getUserArtists($email, $password);
        $response_data = array();
        if($artists == 128){
          $response_data['error'] = true;
          $response_data['message'] = "INCORRECT_USER_CREDENTIALS";
        }
        else{
          $response_data['error'] = false;
          $response_data['artists'] = $artists;
        }

        $response->write(json_encode($response_data));

        return $response;
}});

/*
endpoint: getUserArtists()
parameters: $email, $password
method: POST

Returns all Artists belonging to User after verification
*/

$app->post('/getshowinfo', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('email', 'password', 'show_id'), $request, $response)){
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];
        $password = $request_data['password'];
        $show_id = $request_data['show_id'];
        $db = new DbOperations;
        $songs = $db->getShowInfo($email, $password, $show_id);
        $response_data = array();
        if($songs == 128){
          $response_data['error'] = true;
          $response_data['message'] = "INCORRECT_USER_CREDENTIALS";
        }
        else{
          $response_data['error'] = false;
          $response_data['songs'] = $songs;
        }

        $response->write(json_encode($response_data));

        return $response;
}});

/*

  endpoint: userLogin
  parameters: $email, $password
  method: POST

  Attempts to authenticate user with email and password

*/

$app->post('/userlogin', function(Request $request, Response $response){
  if(!haveEmptyParameters(array('email', 'password'), $response)){
    $request_data = $request->getParsedBody();
    $email = $request_data['email'];
    $password = $request_data['password'];

    $db = new DbOperations;
    $result = $db->userLogin($email, $password);

    if($result == USER_AUTHENTICATED){

      $user = $db->getUserByEmail($email);
      $response_data = array();
      $response_data['error'] = false;
      $response_data['message'] = 'Login Succesful';
      $response_data['user'] = $user;

      $response->write(json_encode($response_data));

      return $response
          ->withHeader('Content-type', 'application/json')
          ->withStatus(200);


    }else if($result == USER_NOT_FOUND){

      $response_data = array();
      $response_data['error'] = true;
      $response_data['message'] = 'Login Failed. User does not exist';

      $response->write(json_encode($response_data));

      return $response
          ->withHeader('Content-type', 'application/json')
          ->withStatus(404);

    }else if($result == USER_PASSWORD_INCORRECT){
      $response_data = array();
      $response_data['error'] = true;
      $response_data['message'] = 'Login Failed. Incorrect password';

      $response->write(json_encode($response_data));

      return $response
          ->withHeader('Content-type', 'application/json')
          ->withStatus(404);
    }
  }

  return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(422);

});

function haveEmptyParameters($required_params, $request, $response){
    $error = false;
    $error_params = '';
    $request_params = $_REQUEST;
    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true;
            $error_params .= $param . ', ';
        }
    }
    if($error){
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error;
}

$app->run();
