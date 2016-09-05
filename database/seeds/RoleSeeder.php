<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Roles defined

        Role::create ([
            'role' =>'Administrator',
            'description' => 'Site Administrator or Site Manager'
        ]);
        Role::create ([
            'role' =>'Principal',
            'description' => 'Principal of School'
        ]);
        Role::create ([
            'role' =>'Department Head',
            'description' => 'Head of Subject/Course for School'
        ]);
        Role::create ([
            'role' =>'Teacher',
            'description' => 'Teacher in charge of class'
        ]);

        Role::create ([
            'role' =>'Non-editing Teacher',
            'description' => 'Co-teacher or Teaching Assistant'
        ]);

        Role::create ([
            'role' =>'Student',
            'description' => 'Enrolled student in class/course'
        ]);

        Role::create ([
            'role' =>'Parent',
            'description' => 'Designated parent of student enrolled in class/course'
        ]);

    	// Permissions related to students in class
    	Permission::create([
    		'permission' => 'list_students',
    		'description' => 'List all Students'
    	]);

    	Permission::create([
    		'permission' => 'list_class_students',
    		'description' => 'List Students from a class'
    	]);

    	Permission::create([
    		'permission' => 'list_course_students',
    		'description' => 'List students from a course'
    	]);

    	Permission::create([
    		'permission' => 'list_school_students',
    		'description' => 'List students from a school'
    	]);

    	Permission::create([
    		'permission' => 'list_public_students',
    		'description' => 'List non-hidden Students only'
    	]);

    	Permission::create([
    		'permission' => 'create_students',
    		'description' => 'Create or enroll new Students'
    	]);

    	Permission::create([
    		'permission' => 'modify_all_students',
    		'description' => 'Edit/Delete information of all Students'
    	]);

    	Permission::create([
    		'permission' => 'modify_course_students',
    		'description' => 'Edit/Delete information of students in a'
    	]);

    	Permission::create([
    		'permission' => 'modify_class_students',
    		'description' => 'Edit/Delete information of students in same classes'
    	]);

    	// Permissions related to status in class
    	Permission::create([
    		'permission' => 'list_status',
    		'description' => 'List public and hidden statuses'
    	]);

    	Permission::create([
    		'permission' => 'list_public_status',
    		'description' => 'List non-hidden Status only'
    	]);

    	Permission::create([
    		'permission' => 'create_status',
    		'description' => 'Create new Status'
    	]);

    	Permission::create([
    		'permission' => 'modify_status',
    		'description' => 'Edit/Delete status'
    	]);

    	// Permissions related to difficulty in class
    	Permission::create([
    		'permission' => 'list_difficulty',
    		'description' => 'List public and hidden difficulties'
    	]);

    	Permission::create([
    		'permission' => 'list_public_difficulty',
    		'description' => 'List non-hidden difficulties only'
    	]);

    	Permission::create([
    		'permission' => 'create_difficulty',
    		'description' => 'Create new difficulties'
    	]);

    	Permission::create([
    		'permission' => 'modify_difficulty',
    		'description' => 'Edit/Delete difficulties'
    	]);

    	// Permissions related to test in class
    	Permission::create([
    		'permission' => 'list_test',
    		'description' => 'List public and hidden tests'
    	]);

    	Permission::create([
    		'permission' => 'list_public_tests',
    		'description' => 'List non-hidden tests only'
    	]);

    	Permission::create([
    		'permission' => 'create_test',
    		'description' => 'Create new tests'
    	]);

    	Permission::create([
    		'permission' => 'modify_test',
    		'description' => 'Edit/Delete tests'
    	]);

    	// Permissions related to question in class
    	Permission::create([
    		'permission' => 'list_question',
    		'description' => 'List public and hidden questions'
    	]);

    	Permission::create([
    		'permission' => 'list_public_question',
    		'description' => 'List non-hidden questions only'
    	]);

    	Permission::create([
    		'permission' => 'create_question',
    		'description' => 'Create new questions'
    	]);

    	Permission::create([
    		'permission' => 'modify_question',
    		'description' => 'Edit/Delete questions'
    	]);
    	// Permissions related to skill in class
    	Permission::create([
    		'permission' => 'list_skill',
    		'description' => 'List public and hidden skills'
    	]);

    	Permission::create([
    		'permission' => 'list_public_skill',
    		'description' => 'List non-hidden skills only'
    	]);

    	Permission::create([
    		'permission' => 'create_skill',
    		'description' => 'Create new skills'
    	]);

    	Permission::create([
    		'permission' => 'modify_skill',
    		'description' => 'Edit/Delete skills'
    	]);

    	// Permissions related to track in class
    	Permission::create([
    		'permission' => 'list_track',
    		'description' => 'List public and hidden tracks'
    	]);

    	Permission::create([
    		'permission' => 'list_public_track',
    		'description' => 'List non-hidden tracks only'
    	]);

    	Permission::create([
    		'permission' => 'create_track',
    		'description' => 'Create new tracks'
    	]);

    	Permission::create([
    		'permission' => 'modify_track',
    		'description' => 'Edit/Delete tracks'
    	]);

    	// Permissions related to level in class
    	Permission::create([
    		'permission' => 'list_level',
    		'description' => 'List public and hidden levels'
    	]);

    	Permission::create([
    		'permission' => 'list_public_level',
    		'description' => 'List non-hidden levels only'
    	]);

    	Permission::create([
    		'permission' => 'create_level',
    		'description' => 'Create new levels'
    	]);

    	Permission::create([
    		'permission' => 'modify_level',
    		'description' => 'Edit/Delete levels'
    	]);
    }
}