import { Component, OnInit } from '@angular/core';
import { Course } from '../models/course';

@Component({
  selector: 'ag-gallery',
  templateUrl: './gallery.component.html',
  styleUrls: ['./gallery.component.css']
})
export class GalleryComponent implements OnInit {
  selectedCourse: Course;
  constructor() { }

  ngOnInit() {
  }
  selectCourse(course: Course) {
    this.selectedCourse = course;
  }
}
