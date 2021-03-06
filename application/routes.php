<?php
define('CACHE_MINUTES', 60);

// home
Route::get('/', function()
{
	$libraries = Cache::remember('libraries', function() {
		return DB::query('
			select title, description, slug, image, ifnull(_u.ids, 0) as users, ifnull(_o.ids, 0) as books from libraries
			left join(select id div 100000 as library_id, count(id) as ids from users group by library_id) as _u on libraries.id = _u.library_id
			left join(select id div 100000 as library_id, count(id) as ids from objects where type_id = 0 group by library_id) as _o on libraries.id = _o.library_id
			where slug != "" and title != ""
		');	
	}, CACHE_MINUTES);

	return View::make('home.index', array('libraries' => $libraries));
});

Route::get('/guide', function()
{
	return View::make('home.guide');
});
Route::get('/guide-reghaabat', function()
{
	return View::make('home.guide-reghaabat');
});


// data
Route::controller('data');
Route::controller('admin');
Route::controller('backend');

// files
Route::get('/files/(:any)', function($file)
{
	if (File::extension($file) != 'log')
		return Response::download(path('storage') .'files/'.$file, $file, array('Cache-Control' => 'max-age='. CACHE_MINUTES*60));
	return Response::error('404');
});

// library
Route::get('/(:any)', function($slug)
{
	$library = DB::query('select id, title, image, synced_at from libraries where slug = ?', array($slug));
	if ($library)
		return View::make('library.index', array('library' => $library[0]));
	return Response::error('404');
});


/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application. The exception object
| that is captured during execution is then passed to the 500 listener.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function($exception)
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Route::get('/', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});
