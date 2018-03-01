import { Component, OnInit, Input } from '@angular/core';
import {Course} from '../../models/course';

@Component({
  selector: 'ag-course-detail',
  templateUrl: './course-detail.component.html',
  styleUrls: ['./course-detail.component.css']
})
export class CourseDetailComponent implements OnInit {
  @Input() selectedCourse: Course;
  constructor() { }

  ngOnInit() {
  }

}
