import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'ag-admin-course-delete',
  templateUrl: './admin-course-delete.component.html',
  styleUrls: ['./admin-course-delete.component.css']
})
export class AdminCourseDeleteComponent implements OnInit, OnDestroy {
  id: any;
  params: any;
  constructor(private activatedRoute: ActivatedRoute, private courseService: CourseService, private router: Router) { }

  ngOnInit() {
    this.params = this.activatedRoute.params.subscribe(params => this.id = params['id']);
    this.courseService.deleteCourse(this.id).subscribe(
    data => {
      console.log(data);
      this.router.navigate(['/admin/courses']);
    },
    error =>  console.log(<any>error));
  }
  ngOnDestroy() {
   this.params.unsubscribe();
  }
}
