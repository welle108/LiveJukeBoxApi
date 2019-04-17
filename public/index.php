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
            $message['message'] = 'User already has Show with that email';
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
  endpoint: createOA
  parameters: $name
  method: POST
*/

$app->post('/createoa', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('name'), $request, $response)){
        $request_data = $request->getParsedBody();
        $name = $request_data['name'];
        $db = new DbOperations;
        $result = $db->createOA($name);

        if($result == OA_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Original Artist created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == OA_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to create Original Artist';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == OA_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Original Artist already exists';
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
  parameters: $title, $original_artist
  method: POST
*/

$app->post('/createsong', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('title','original_artist'), $request, $response)){
        $request_data = $request->getParsedBody();
        $title = $request_data['title'];
        $original_artist = $request_data['original_artist'];
        $db = new DbOperations;
        $result = $db->createSong($title, $original_artist);

        if($result == SONG_CREATED){
            $message = array();
            $message['error'] = false;
            $message['message'] = 'Song created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == SONG_FAILURE){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred while attempting to create Song';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        }else if($result == SONG_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Song already exists';
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
