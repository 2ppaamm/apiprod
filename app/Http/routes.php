<?php


//Route::get('/', function () {
//	$user = Auth::loginUsingId(2);
//    return view('welcome');
//});
Route::resource('/', 'DashboardController');

//Route::get('/loadall', 'LoadController@loadall');
//Route::get('/loadquestions', 'LoadQuestions@loadall');

//Route::post('/qa', 'CheckAnswerController@index');
//Route::post('/qa/answer', 'CheckAnswerController@answer');

Route::resource('users', 'UserController');
Route::post('courses/{courses}', 'CourseController@copy');
Route::resource('courses', 'CourseController', ['except' =>['create', 'edit']]);
//Route::resource('mastercodes', 'MasterCodeController', ['except' =>['create', 'edit', 'update']]);
Route::resource('houses', 'HouseController', ['except' =>['create', 'edit']]);
Route::resource('levels', 'LevelController', ['except' =>['create', 'edit']]);
Route::resource('courses.houses', 'CourseHouseController', ['except' => ['edit', 'create']]);
Route::resource('courses.users', 'CourseUserController', ['except' => ['edit', 'create']]);
Route::resource('houses.users', 'HouseUserController', ['except' => ['edit', 'create']]);
Route::resource('courses.tracks', 'CourseTrackController', ['except' => ['edit', 'create']]);
Route::resource('houses.tracks', 'HouseTrackController', ['except' => ['edit', 'create']]);
Route::resource('users.tests', 'UserTestController', ['except' => ['edit', 'create']]);
Route::resource('tracks', 'TrackController', ['except' =>['create', 'edit']]);
Route::resource('tests', 'TestController', ['except' =>['create', 'edit']]);
Route::resource('skills', 'SkillController', ['except' =>['create', 'edit']]);
Route::resource('questions', 'QuestionController', ['except' =>['create', 'edit']]);
Route::resource('enrolments', 'EnrolmentController', ['except' => ['edit', 'create']]);
Route::resource('skills.questions', 'SkillQuestionsController', ['except' => ['edit', 'create']]);
Route::resource('tracks.skills', 'TrackSkillController', ['except' => ['edit', 'create']]);
//Route:: post('oauth/access_token', function(){
//	return response()->json(Authorizer::issueAccessToken());
//});

Route::get('users/{username}/logs','LogController@show');
Route::get('logs', 'LogController@index');

//Route::get('/auth0/callback', array('middleware' => 'auth0.jwt', function() {
//    echo "Hello ". Auth0::jwtuser()->name. ", your email is ". Auth0::jwtuser()->sub;
//    dd(Auth0::jwtuser());
//}));

Route::get('/api/protected', 'DashboardController@index');
Route::post('/test/protected', 'DiagnosticController@index');
Route::post('test/mastercode', 'DiagnosticController@store');
Route::post('/test/answers', 'DiagnosticController@answer');

/* Post answers and game scores
*/

//Route:: post('answers', 'AnswerController@checkQuiz');
//Route:: post('game_score', 'UserController@game_score');
//Route:: get('game_level', 'UserController@game_level');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

