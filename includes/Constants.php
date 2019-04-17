<?php
  //DB Connection args
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASSWORD', '');
  define('DB_NAME', 'livejukebox');

  // Create user responses
  define('USER_CREATED', 101);
  define('USER_EXISTS', 102);
  define('USER_FAILURE', 103);

  // Create artist responses
  define('ARTIST_CREATED', 104);
  define('ARTIST_EXISTS', 105);
  define('ARTIST_USER_NOT_EXIST', 106);

  // User authentication responses
  define('USER_AUTHENTICATED', 201);
  define('USER_NOT_FOUND', 202);
  define('USER_PASSWORD_INCORRECT', 203);
 ?>
