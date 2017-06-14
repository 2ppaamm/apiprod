import { Injectable } from '@angular/core';
import { Course} from '../models/course';
import { Http, Response } from '@angular/http';
import 'rxjs/Rx';
import {Observable} from 'rxjs';

@Injectable()
export class CourseService {

  constructor(private http:Http) { }
  getCourses(): Observable<Course[]> {
	return this.http.get('http://localhost/courses')
	  .map((response: Response) => response.json());
  }
}
