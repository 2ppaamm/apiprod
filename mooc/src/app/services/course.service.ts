import { Injectable } from '@angular/core';
import { Course} from '../models/course';
import {Http, Response, Headers, RequestOptions} from "@angular/http";
import 'rxjs/Rx';
import {Observable} from 'rxjs';

@Injectable()
export class CourseService {

  constructor(private http:Http) { }
  getCourses(): Observable<Course[]> {
	return this.http.get('http://localhost/courses')
	  .map((response: Response) => response.json())
	  .catch((error:any) => Observable.throw(error.json().error || {message:"Server Error"} ));
  }
  addCourse(course: Object): Observable<Course[]> {
	let headers = new Headers({ 'Content-Type': 'application/json' });
	let options = new RequestOptions({ headers: headers }); 

    return this.http.post('http://localhost/courses', course, options)
      .map((response: Response) => response.json())
      .catch((error:any) => Observable.throw(error.json().error || {message:"Server Error"} ));
  }

  getCourse(id: String): Observable<Course[]> {
	  return this.http.get('http://localhost/courses/' + id)
	    .map((response: Response) => response.json())
        .catch((error:any) => Observable.throw(error.json().error || {message:"Server Error"} ));
  }
  updateCourse(course: Object): Observable<Course[]> {
    const url = 'http://localhost/courses/'+ course["id"];
    return this.http.put(url, course)
      .map((response: Response) => response.json())
      .catch((error:any) => Observable.throw(error.json().error || {message:"Server Error"}));
  }
  deleteCourse(id: String): Observable<Course[]> {
    let apiUrl = 'http://localhost/courses';
    let url = `${apiUrl}/${id}`;
    return this.http.delete(url)
      .map((response: Response) => response.json())
      .catch((error:any) => Observable.throw(error.json().error || {message:"Server Error"}));
  }  
}
