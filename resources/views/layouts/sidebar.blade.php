<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
      <div>
        <img src="/assets/images/logo/logo-icon-2.png" class="logo-icon" alt="logo icon">
      </div>
      <div>
        <h4 class="logo-text text-center">Assessment System</h4>
      </div>
      <div class="toggle-icon ms-auto">
        <ion-icon name="menu-sharp"></ion-icon>
      </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
          <a href="/dashboard">
            <div class="parent-icon">
              <ion-icon name="home-sharp"></ion-icon>
            </div>
            <div class="menu-title">Dashboard</div>
          </a>
        </li>
        @can('superadmin')
        <li class="menu-label">Administrator</li>
        <li>
          <a href="/dashboard/user">
            <div class="parent-icon">
              <ion-icon name="person-add-sharp"></ion-icon>
            </div>
            <div class="menu-title">User Management</div>
          </a>
        </li>
        <li>
          <a href="/dashboard/project">
            <div class="parent-icon">
              <ion-icon name="briefcase-sharp"></ion-icon>
            </div>
            <div class="menu-title">Project Management</div>
          </a>
        </li>
        <li>
          <a href="javascript:;">
            <div class="parent-icon">
              <ion-icon name="stats-chart-sharp"></ion-icon>
            </div>
            <div class="menu-title">Summary</div>
          </a>
        </li>
      @endcan
      @can('admin')
        <li class="menu-label">Admin Projek</li>
        <li>
          <a href="/dashboard/admin/project">
            <div class="parent-icon">
              <ion-icon name="briefcase-sharp"></ion-icon>
            </div>
            <div class="menu-title">Project Management</div>
          </a>
        </li>
        <li>
          <a href="/dashboard/admin/assignment">
            <div class="parent-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
            </div>
            <div class="menu-title">User Assignment</div>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="parent-icon">
              <ion-icon name="newspaper-sharp"></ion-icon>
            </div>
            <div class="menu-title">Article Assessment</div>
          </a>
        </li>
        <li>
          <a href="javascript:;">
            <div class="parent-icon">
              <ion-icon name="stats-chart-sharp"></ion-icon>
            </div>
            <div class="menu-title">Summary</div>
          </a>
        </li>
      @endcan
    </ul>
    <!--end navigation-->
</aside>