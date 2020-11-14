<?php return array (
  0 => 
  array (
    'GET' => 
    array (
      '/' => 
      array (
        'prefix' => '/',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'index',
        'controller' => 'MainController@index',
        'method' => 'GET',
        'middleware' => '',
      ),
      '/register' => 
      array (
        'prefix' => '/register',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'register',
        'controller' => 'AuthController@showRegisterForm',
        'method' => 'GET',
        'middleware' => '',
      ),
      '/login' => 
      array (
        'prefix' => '/login',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'login',
        'controller' => 'AuthController@showLoginForm',
        'method' => 'GET',
        'middleware' => '',
      ),
      '/forgot-password' => 
      array (
        'prefix' => '/forgot-password',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'forgot-password',
        'controller' => 'AuthController@forgot-password',
        'method' => 'GET',
        'middleware' => '',
      ),
      '/server' => 
      array (
        'prefix' => '/server',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\Server\\',
        'name' => 'server.',
        'controller' => 
        Closure::__set_state(array(
        )),
        'method' => 'GET',
        'middleware' => 'auth',
      ),
      '/server/admin' => 
      array (
        'prefix' => '/server/admin',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\Server\\Admin\\',
        'name' => 'server.admin.index',
        'controller' => 'MainController@index',
        'method' => 'GET',
        'middleware' => 'auth',
      ),
      '/chat' => 
      array (
        'prefix' => '/chat',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'chat.index',
        'controller' => 'MainController@chatIndex',
        'method' => 'GET',
        'middleware' => '',
      ),
      '/chat/public' => 
      array (
        'prefix' => '/chat/public',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'chat.public',
        'controller' => 'MainController@publicChat',
        'method' => 'GET',
        'middleware' => '',
      ),
    ),
    'POST' => 
    array (
      '/register' => 
      array (
        'prefix' => '/register',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'register.submit',
        'controller' => 'AuthController@doRegister',
        'method' => 'POST',
        'middleware' => '',
      ),
      '/login' => 
      array (
        'prefix' => '/login',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'login.submit',
        'controller' => 'AuthController@doLogin',
        'method' => 'POST',
        'middleware' => '',
      ),
      '/server/admin/add' => 
      array (
        'prefix' => '/server/admin/add',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\Server\\Admin\\',
        'name' => 'server.admin.add',
        'controller' => 'MainController@add',
        'method' => 'POST',
        'middleware' => 'auth',
      ),
    ),
  ),
  1 => 
  array (
    'GET' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/home/([^/]+)|/chat/private/([^/]+)()|/note/([^/]+)()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 
            array (
              'prefix' => '/home/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\',
              'name' => 'index-logged',
              'controller' => 'MainController@index',
              'method' => 'GET',
              'middleware' => '',
            ),
            1 => 
            array (
              'primaryToken' => 'primaryToken',
            ),
          ),
          3 => 
          array (
            0 => 
            array (
              'prefix' => '/chat/private/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\',
              'name' => 'chat.private',
              'controller' => 'User\\ChatController@privateChat',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'primaryToken' => 'primaryToken',
            ),
          ),
          4 => 
          array (
            0 => 
            array (
              'prefix' => '/note/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => '',
              'controller' => 'NoteController@index',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'primaryToken' => 'primaryToken',
            ),
          ),
        ),
      ),
    ),
  ),
);