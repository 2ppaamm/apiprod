import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { Course } from '../../models/course';
import { CourseService } from '../../services/course.service';
import {Observable} from 'rxjs/Observable';

@Component({
  selector: 'ag-course-list',
  templateUrl: './course-list.component.html',
  styles: []
})
export class CourseListComponent implements OnInit {

  courses: Observable<Course[]>;

  selectedCourse: Course;

  @Output() selectedEvent: EventEmitter<Course> = new EventEmitter<Course>();
  
  constructor(private courseService: CourseService) { }

  ngOnInit() {
    this.courses = this.courseService.getCourses();
  }
  onSelect(course: Course) {
    this.selectedEvent.emit(course);
  }
}
