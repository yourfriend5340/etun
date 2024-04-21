<div class="divhead">
    <a class="navbar-brand" href="https://www.google.com">
        <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
    </a>
</div> 
        <!-- Nav bar offcanvas -->

        <nav class="navbar navbar-expand-lg navbar-light mb-0 me-0 pe-2 justify-content-end w-10" style="background-color: ##e3f2fd;">
             
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="offcanvas offcanvas-end" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" tabindex="-1">
                
                <div class="offcanvas-header mb-0 pb-0" style="background-color: #e3f2fd;">
                    <h3 class="offcanvas-title" id="offcanvasExampleLabel">功能選單</h3>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body justify-content-start navbar-light" style="background-color: #e3f2fd;">
                    <hr class="d-lg-none text-info m-0 p-0">

                    <ul class="navbar-nav flex-wrap flex-row">
                        <li class="nav-item mx-2">
                            <a class="nav-link" aria-current="page" href="home"> 萬宇首頁</a>
                        </li>
                      
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="newuser">人事管理</a>
                        </li>
                        
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="schedule">出勤管理</a>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#">巡邏查詢</a>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#">權限管理</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#">還沒想到</a>
                        </li>

                        <div class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">dropdown</a>
                            
                            <!-- 下拉式選單dropdown level1 -->
                            <ul class="dropdown-menu" style="background-color: ##e3f2fd;">
                                <li><a class="dropdown-item" href="#">Layer1</a></li>
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Layer1_2</a>

                                    <!-- 子選單level2 -->
                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                        <li><a class="dropdown-item" href="#">Layer2</a></li>
                                        <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Layer2_1</a>
                            
                                            <!-- 子選單level3 -->
                                            <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                                <li><a class="dropdown-item" href="#">Layer3</a></li>
                                            </ul>    
                                    
                                        </li><!-- end of level2 li-->
                                    </ul><!-- end of level2 ul-->
                                </li> <!-- end of level1 li -->
                            </ul> <!-- end of level1 ul-->

                        </div>
                    </ul>

                </div>

            </div>
        </nav>
    

