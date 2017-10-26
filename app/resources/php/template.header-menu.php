<?php try{ ?>            <div class="container nav-wrapper">
                <nav id="main-nav">
                    <div class="container">
                        <div class="nav-container">
                            <div class="row">
                                <div class="col-8 col-sm-8 brand">
                                    <a href="#">
                                        <img src="<?php public_url('images/logo.png'); ?>" alt="TheGioiVuong.com">
                                    </a>
                                </div>
                                <div class="col-4 col-sm-4 button">
                                    <a href="#" class="btn-toggle"><i class="fa fa-bars" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php $CHM = get_extension('CubeHtmlMenu'); ?>
                        <?php $menuArgs = array('type'=>'category','args'=>array('parent_id'=>'')); ?>
                        <?php $menu_options = array('menu_class'=>'menu','menu_id'=>'main-menu', 'submenu_class' => 'sub-menu'); ?>
                        <?php echoif($CHM::createHomeMenu($menuArgs, get_home_url(), null, $menu_options)); ?>
                        
                    </div>
                </nav>
            </div><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>