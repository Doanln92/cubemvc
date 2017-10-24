            <div class="container nav-wrapper">
                <nav id="main-nav">
                    <div class="container">
                        <div class="nav-container">
                            <div class="row">
                                <div class="col-8 col-sm-8 brand">
                                    <a href="#">
                                        <img src="@public_url('images/logo.png')" alt="TheGioiVuong.com">
                                    </a>
                                </div>
                                <div class="col-4 col-sm-4 button">
                                    <a href="#" class="btn-toggle"><i class="fa fa-bars" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        @$CHM = get_extension('CubeHtmlMenu');
                        {{$menuArgs = array('type'=>'category','args'=>array('parent_id'=>''))}}
                        {{$menu_options = array('menu_class'=>'menu','menu_id'=>'main-menu', 'submenu_class' => 'sub-menu')}}
                        {{$CHM::createHomeMenu($menuArgs, get_home_url(), null, $menu_options)}}
                        
                    </div>
                </nav>
            </div>