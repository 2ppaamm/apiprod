<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('/', 'DashboardController');

Route::post('/qa', 'CheckAnswerController@index');
Route::post('/qa/answer', 'CheckAnswerController@answer');

Route::resource('users', 'UserController');
Route::get('users/{users}/performance', 'UserController@performance');
Route::post('courses/{courses}', 'CourseController@copy');
Route::get('questions/search_init', 'QuestionController@search_init');
Route::post('questions/search', 'QuestionController@search');
//Route::put('courseimage/{courses}', 'CourseController@updateImage');
Route::resource('courses', 'CourseController');
Route::resource('difficulties', 'DifficultyController');
Route::resource('fields', 'FieldController');
Route::resource('houses', 'HouseController');
Route::resource('levels', 'LevelController', ['except' =>['create', 'edit']]);
Route::resource('permissions', 'PermissionController', ['except' =>['create', 'edit']]);
Route::resource('roles', 'RoleController', ['except' =>['create', 'edit']]);
Route::resource('units', 'UnitController', ['except' =>['create', 'edit']]);
Route::resource('courses.houses', 'CourseHouseController', ['except' => ['edit', 'create']]);
Route::resource('courses.users', 'CourseUserController', ['except' => ['edit', 'create']]);
Route::resource('houses.users', 'HouseUserController', ['except' => ['edit', 'create']]);
Route::resource('courses.tracks', 'CourseTrackController', ['except' => ['edit', 'create']]);
Route::resource('houses.tracks', 'HouseTrackController', ['except' => ['edit', 'create']]);
Route::resource('users.tests', 'UserTestController', ['except' => ['edit', 'create']]);
Route::resource('tracks', 'TrackController', ['except' =>['edit']]);
Route::resource('tests', 'TestController', ['except' =>['create', 'edit']]);
Route::resource('types', 'TypeController');
Route::get('skills/{skills}/passed','SkillController@usersPassed');
Route::post('skills/search', 'SkillController@search');
Route::resource('skills', 'SkillController', ['except' =>['edit']]);
Route::resource('questions', 'QuestionController', ['except' =>['edit']]);
Route::resource('enrolments', 'EnrolmentController', ['except' => ['edit', 'create']]);
Route::resource('skills.questions', 'SkillQuestionsController', ['except' => ['edit', 'create']]);
Route::resource('tracks.skills', 'TrackSkillController', ['except' => ['edit', 'create']]);

Route::get('users/{username}/logs','LogController@show');
Route::get('logs', 'LogController@index');

Route::get('/api/protected', 'DashboardController@index');
Route::get('/enrols/users', 'EnrolmentController@user_houses');
Route::get('/enrols/teachers', 'EnrolmentController@teacher_houses');
Route::post('/test/protected', 'DiagnosticController@index');
Route::post('test/mastercode', 'DiagnosticController@store');
Route::post('/test/answers', 'DiagnosticController@answer');
