            <nav id="top-nav" class="top-nav">
                <div class="container">
                    <div class="row hf">
                        <div class="col-12 col-sm-4">
                            <ul class="menu page-menu">
                                <li>HOTLINE: <span>0945786960</span></li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-8">
                            <ul class="menu acc-menu right">
                                @$User = get_model('User');
                                @if($user = $User::getCurrentLogin())
                                    <li>Hello <a href="@home_url('profile')">{{$user->name}}</a></li>
                                    @if($user->level>=1)
                                    <li><a href="@home_url('dashboard')">Manager Page</a></li>
                                    @endif
                                    <li><a href="@home_url('logout')">Thoát</a></li>
                                @else
                                    <li><a href="@home_url('register')">Đăng ký</a></li>
                                    <li><a href="@home_url('login?next='.get_current_url(true,true))">Đăng nhập</a></li>
                                    
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>