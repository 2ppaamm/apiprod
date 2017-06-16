import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminCourseCreateComponent } from './admin-course-create.component';

describe('AdminCourseCreateComponent', () => {
  let component: AdminCourseCreateComponent;
  let fixture: ComponentFixture<AdminCourseCreateComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AdminCourseCreateComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminCourseCreateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
