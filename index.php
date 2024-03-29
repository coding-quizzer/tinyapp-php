<?php

// $urls = array(array("long_url" => "http://lighthouselabs.ca", "short_url" => "b2xVn2"), array("long_url" => "http://google.com", "short_url" => "9sm5xK"), 5 => array("long_url" => 'http://youtube.com', "short_url" => "are45"));
include_once './views/initial_urls.php';

// First define some routes for the application
// (Based on tutorial at https://beamtic.com/creating-a-router-in-php)

$routes = [
  [
    'string' => '/',
    'methods' => ['GET', 'HEAD'],
    'function' => 'index'
  ],
  [
    'string' => '/urls',
    'methods' => ['GET', 'POST', 'HEAD'],
    'function' => 'index'
  ],
  [
    'string' => '/urls/new',
    'methods' => ['GET', 'HEAD'],
    'function' => 'new'
  ],
  [
    'pattern' => '/^\/urls\/[a-zA-Z0-9]+/',
    'methods' => ['GET', 'HEAD'],
    'function' => 'show'
  ]
];

// --------------------
// ------ The Router---
// -------------------
$parsed_url = parse_url($_SERVER['REQUEST_URI']);
$requested_path = $parsed_url['path'];
if ($requested_path != "/") {
  $requested_path = rtrim($requested_path, "/");
}
foreach ($routes as $route) {
  // Verify that used paramaters are allowed by requested resource
  // Note... A POST request can also contain GET parameters
  // since they are included in the URL
  // We therefore verify both parameter types.

  if (isset($route['get_params'])) {
    handle_parameters($route['get_params'], $_GET);
  }

  if (isset($route['post_params'])) {
    handle_parameters($route['post_params'], $_POST);
  }

  // Check if the route is recognized
  if (isset($route['pattern'])) {
    if (!preg_match($route['pattern'], $requested_path, $matches)) {
      continue;
    }
  } else if (isset($route['string'])) {
    if ($route['string'] !== $requested_path) {
      continue;
    }

  } else {
    // If required parameter was missing (string or pattern)
    throw new Exception("Missing required parameter (string or pattern) in route.");
  }



  // Check that the request method is supported
  if (!in_array($_SERVER['REQUEST_METHOD'], $route['methods'])) {
    echo 'Invalid Method';
    echo implode(' ', $route['methods']);
    respond(
      405,
      '<h1>405 Method Not Allowed. Please Try again.</h1>',
      ['allow' => implode(', ', $route['methods'])]
    );
  }

  // If everything was ok, try to call the related feature
  if (isset($route['function'])) {
    // Make sure the route handler is callable
    if (!is_callable('feature_' . $route['function'])) {
      $content = '<h1>500 Internal Server Error</h1>';
      $content .= '<p>Specified route-handler does not exist.</p>';
      $content .= '<pre>' . htmlspecialchars($route['function']) . '</pre>';

      respond(500, $content);
    }

    // If we got any RegEx matches
    if (isset($matches[0])) {
      call_user_func('feature_' . $route['function'], $matches, $urls);
    } else {

      call_user_func('feature_' . $route['function'], $urls);
    }
  } else {
    throw new Exception("Missing required parameter (function) in route.");
  }
}

// If the route is not recognized by the router
respond(404, '<h1>404 Not Found</h1><p>Page not recognized...</p>');

// ------------------
// ---- Functions ---
// ------------------
function feature_index($urls)
{

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_long_url = $_POST['longURL'];
    $new_short_url = uniqid();
    array_push($urls, array("long_url" => $new_long_url, "short_url" => $new_short_url));

    $content = "{\"long_url\": \"";
    $content .= $new_long_url;
    $content .= "\", \"short_url\": \"";
    $content .= $new_short_url;
    $content .= "\" }";
    respond(303, $content, ['Location' => '/urls']);
  }


  if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    ob_start();
    include './views/url_index.php';
    $content = ob_get_clean();
    respond(200, $content);
  }

}

echo 'All Matches' . $matches;
function feature_show($matches, $urls)
{
  // define('short_url', "9sm5xK");
  // define('long_url', "http://www.google.com");
  $split_path = explode('/', $matches[0]);
  define('short_url', $split_path[2]);
  $url_data = array_find_with_callback($urls, function ($value) use ($matches) {
    $value['long_url'] == $matches[0];
  });
  // $short_urls = array_column($urls, 'short_url');
  // $url_index = array_search(short_url, $short_urls);
  if ($url_data === false) {
    respond(404, '<h1>404 Not Found</h1><p>Could not find the short url: ' . short_url . '.</p>');
  }

  $url_key = $url_data['key'];

  define('long_url', $urls[$url_key]['long_url']);

  ob_start();
  include './views/url_show.php';
  $content = ob_get_clean();
  respond(200, $content);
}

function feature_new()
{
  ob_start();
  include './views/url_new.php';
  $content = ob_get_clean();
  respond(200, $content);
}

function respond($code, $html, $headers = [])
{
  $default_headers = ['content-type' => 'text/html; charset=utf-8'];
  $headers = $headers + $default_headers;
  http_response_code($code);
  foreach ($headers as $key => $value) {
    header($key . ': ' . $value);
  }
  echo $html;
  exit();
}

function handle_parameters($allowed_parameters, $post_or_get_parameters)
{

  $invalid_parameters = [];
  foreach ($post_or_get_parameters as $param_name => $param_value) {
    if (!in_array($param_name, $allowed_parameters)) {
      $invalid_parameters[] = $param_name;
    }
  }

  if ($invalid_parameters !== []) {
    echo '<p><b>Invalid request:</b> parameters not allowed.</p>';
    echo '<pre>';
    foreach ($invalid_parameters as $invalid_key => $invalid_name) {
      $invalid_name . "\n";
    }
    echo '</pre>';
    exit();
  }
}

function array_find_with_callback($array, $callback)
{
  $index = 0;
  foreach ($array as $key => $value) {
    if ($callback($value)) {
      return array('index' => $index, 'key' => $key, 'value' => $value);
    }
    $index++;
  }
  return false;
}

?>