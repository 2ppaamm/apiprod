import {Routes} from "@angular/router";
import {AdminImageListComponent} from "./admin-image-list/admin-image-list.component";
import {DashboardComponent} from "./dashboard.component";

export const adminRoutes: Routes = [
  { path: '', component: DashboardComponent},
  { path: 'images', component: AdminImageListComponent}
];