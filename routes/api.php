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

Route::get('/loadall', 'LoadController@loadall');
Route::get('/loadquestions', 'LoadQuestions@loadall');
Route::get('/loadsecondary', 'LoadSecondary@loadall');

Route::post('/qa', 'CheckAnswerController@index');
Route::post('/qa/answer', 'CheckAnswerController@answer');

Route::resource('users', 'UserController');
Route::post('courses/{courses}', 'CourseController@copy');
Route::resource('courses', 'CourseController', ['except' =>['create', 'edit']]);
Route::resource('houses', 'HouseController', ['except' =>['create', 'edit']]);
Route::resource('levels', 'LevelController', ['except' =>['create', 'edit']]);
Route::resource('courses.houses', 'CourseHouseController', ['except' => ['edit', 'create']]);
Route::resource('courses.users', 'CourseUserController', ['except' => ['edit', 'create']]);
Route::resource('houses.users', 'HouseUserController', ['except' => ['edit', 'create']]);
Route::resource('courses.tracks', 'CourseTrackController', ['except' => ['edit', 'create']]);
Route::resource('houses.tracks', 'HouseTrackController', ['except' => ['edit', 'create']]);
Route::resource('users.tests', 'UserTestController', ['except' => ['edit', 'create']]);
Route::resource('tracks', 'TrackController', ['except' =>['edit']]);
Route::resource('tests', 'TestController', ['except' =>['create', 'edit']]);
Route::resource('skills', 'SkillController', ['except' =>['edit']]);
Route::resource('questions', 'QuestionController', ['except' =>['create', 'edit']]);
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
