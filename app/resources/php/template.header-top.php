<?php try{ ?>            <nav id="top-nav" class="top-nav">
                <div class="container">
                    <div class="row hf">
                        <div class="col-12 col-sm-4">
                            <ul class="menu page-menu">
                                <li>HOTLINE: <span>0945786960</span></li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-8">
                            <ul class="menu acc-menu right">
                                <?php $User = get_model('User'); ?>
                                <?php if($user = $User::getCurrentLogin()){ ?>
                                    <li>Hello <a href="<?php home_url('profile'); ?>"><?php echoif($user->name); ?></a></li>
                                    <?php if($user->level>=1){ ?>
                                    <li><a href="<?php home_url('dashboard'); ?>">Manager Page</a></li>
                                    <?php } ?>
                                    <li><a href="<?php home_url('logout'); ?>">Thoát</a></li>
                                <?php } else { ?>
                                    <li><a href="<?php home_url('register'); ?>">Đăng ký</a></li>
                                    <li><a href="<?php home_url('login?next='.get_current_url(true,true)); ?>">Đăng nhập</a></li>
                                    
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>