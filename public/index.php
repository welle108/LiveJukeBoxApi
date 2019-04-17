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
      $response_data['message'] = 'Login Failed. Invalid Credential';

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



function haveEmptyParameters($required_params, $response){
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
