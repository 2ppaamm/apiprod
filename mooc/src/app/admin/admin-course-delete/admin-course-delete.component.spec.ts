import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminCourseDeleteComponent } from './admin-course-delete.component';

describe('AdminCourseDeleteComponent', () => {
  let component: AdminCourseDeleteComponent;
  let fixture: ComponentFixture<AdminCourseDeleteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AdminCourseDeleteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminCourseDeleteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
