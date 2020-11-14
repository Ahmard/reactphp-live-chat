<?php return array (
  0 => 
  array (
    'GET' => 
    array (
      '/api/hello' => 
      array (
        'prefix' => '/api/hello',
        'append' => '',
        'prepend' => '',
        'namespace' => 'App\\Http\\Controllers\\',
        'name' => 'api.',
        'controller' => 'world',
        'method' => 'GET',
        'middleware' => '',
      ),
    ),
  ),
  1 => 
  array (
    'GET' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/chat/private/check\\-user/([^/]+)|/api/chat/private/fetch\\-conversations/([^/]+)()|/api/chat/private/get\\-conversation\\-status/(\\d+)/([^/]+)()|/api/chat/private/(\\d+)/([^/]+)()()|/api/notes/([^/]+)()()()()|/api/notes/(\\d+)/([^/]+)()()()()|/api/notes/(\\d+)/move/(\\d+)/([^/]+)()()()()|/api/categories/([^/]+)()()()()()()()|/api/categories/(\\d+)/open/([^/]+)()()()()()()()|/api/categories/(\\d+)/([^/]+)()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 
            array (
              'prefix' => '/api/chat/private/check-user/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'ChatController@checkUser',
              'method' => 'GET',
              'middleware' => 'auth',
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
              'prefix' => '/api/chat/private/fetch-conversations/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'ChatController@fetchConversations',
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
              'prefix' => '/api/chat/private/get-conversation-status/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'ChatController@getConversationStatus',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          5 => 
          array (
            0 => 
            array (
              'prefix' => '/api/chat/private/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'ChatController@fetchMessages',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          6 => 
          array (
            0 => 
            array (
              'prefix' => '/api/notes/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'NoteController@list',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'primaryToken' => 'primaryToken',
            ),
          ),
          7 => 
          array (
            0 => 
            array (
              'prefix' => '/api/notes/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'NoteController@view',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          8 => 
          array (
            0 => 
            array (
              'prefix' => '/api/notes/{noteId:\\d+}/move/{catId:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'NoteController@move',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'noteId' => 'noteId',
              'catId' => 'catId',
              'primaryToken' => 'primaryToken',
            ),
          ),
          9 => 
          array (
            0 => 
            array (
              'prefix' => '/api/categories/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'CategoryController@list',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'primaryToken' => 'primaryToken',
            ),
          ),
          10 => 
          array (
            0 => 
            array (
              'prefix' => '/api/categories/{id:\\d+}/open/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'CategoryController@open',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          11 => 
          array (
            0 => 
            array (
              'prefix' => '/api/categories/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'CategoryController@view',
              'method' => 'GET',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
        ),
      ),
    ),
    'POST' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/chat/private/(\\d+)/([^/]+)|/api/notes/([^/]+)()()|/api/categories/([^/]+)()()())$~',
        'routeMap' => 
        array (
          3 => 
          array (
            0 => 
            array (
              'prefix' => '/api/chat/private/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'ChatController@send',
              'method' => 'POST',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          4 => 
          array (
            0 => 
            array (
              'prefix' => '/api/notes/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'NoteController@add',
              'method' => 'POST',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'primaryToken' => 'primaryToken',
            ),
          ),
          5 => 
          array (
            0 => 
            array (
              'prefix' => '/api/categories/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'CategoryController@add',
              'method' => 'POST',
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
    'PUT' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/notes/(\\d+)/([^/]+)|/api/categories/(\\d+)/([^/]+)())$~',
        'routeMap' => 
        array (
          3 => 
          array (
            0 => 
            array (
              'prefix' => '/api/notes/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'NoteController@update',
              'method' => 'PUT',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          4 => 
          array (
            0 => 
            array (
              'prefix' => '/api/categories/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'CategoryController@rename',
              'method' => 'PUT',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
        ),
      ),
    ),
    'DELETE' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/notes/(\\d+)/([^/]+)|/api/categories/(\\d+)/([^/]+)())$~',
        'routeMap' => 
        array (
          3 => 
          array (
            0 => 
            array (
              'prefix' => '/api/notes/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'NoteController@delete',
              'method' => 'DELETE',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
          4 => 
          array (
            0 => 
            array (
              'prefix' => '/api/categories/{id:\\d+}/{primaryToken}',
              'append' => '{primaryToken}',
              'prepend' => '',
              'namespace' => 'App\\Http\\Controllers\\User\\',
              'name' => 'api.',
              'controller' => 'CategoryController@delete',
              'method' => 'DELETE',
              'middleware' => 'auth',
            ),
            1 => 
            array (
              'id' => 'id',
              'primaryToken' => 'primaryToken',
            ),
          ),
        ),
      ),
    ),
  ),
);