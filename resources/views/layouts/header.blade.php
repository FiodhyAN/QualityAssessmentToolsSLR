<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
        <div class="mobile-menu-button">
            <ion-icon name="menu-sharp"></ion-icon>
        </div>
        <div class="top-navbar-right ms-auto">

            <ul class="navbar-nav align-items-center">
                <li><i class="fadeIn animated bx bx-fullscreen" id="fullscreen-btn"></i></li>
                <li class="nav-item dropdown dropdown-user-setting">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                        data-bs-toggle="dropdown">
                        <div class="user-setting">
                            <img src="/assets/images/logo/avatar.png" class="user-img" alt="">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="javascript:;">
                                <div class="d-flex flex-row align-items-center gap-2">
                                    <img src="/assets/images/logo/avatar.png" alt="" class="rounded-circle"
                                        width="54" height="54">
                                    <div class="">
                                        <h6 class="mb-0 dropdown-user-name">{{ auth()->user()->name }}</h6>
                                        <small class="mb-0 dropdown-user-designation text-secondary">
                                            @if (auth()->user()->is_superAdmin == true)
                                                Administrator
                                            @elseif (auth()->user()->is_admin == true)
                                                Admin Projek
                                            @else
                                                Assessor
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <hr class="dropdown-divider">
                        <li>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <ion-icon name="lock-closed"></ion-icon>
                                    </div>
                                    <div class="ms-3"><span>Change Password</span></div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/logout">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <ion-icon name="log-out-outline"></ion-icon>
                                    </div>
                                    <div class="ms-3"><span>Logout</span></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

        </div>
    </nav>
</header>

<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="change-password" method="POST" action="/change-password">
                @csrf
                <div class="modal-body">
                    <label for="password">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
