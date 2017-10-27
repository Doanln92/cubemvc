        <footer>
            <div class="container">
                <div class="site-map row">
                @$Cat = get_model('Category');
                {{$categoryes = $Cat::where('parent_id','')->get()}}
                @forif($categoryes, $cat):
                    
                    <div class="col-4 col-sm-3 col-lg-2">
                        <ul>
                            <li class="big-cate"><a href="{{$cat->getUrl()}}">{{$cat->name}}</a></li>
                            @if($ch = $cat->getChildren()):
                                @foreach($ch as $c):
                            
                                <li><a href="{{$c->getUrl()}}">{{$c->name}}</a></li>
                
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    
                @endforif
                    
                </div>
                <div class="footer-boxes row">
                    <div class="col-12 col-sm-4">
                        <div class="cube-box-area map">
                            <div class="cube-box-header">
                                <h3><a href="#">Map</a></h3>
                            </div>
                            <div class="cube-box-content">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.927418267965!2d105.7624513149328!3d21.035589985994637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454b92b75eded%3A0x5b8ed5d074fbd3b!2zVHLGsOG7nW5nIENhbyDEkOG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2s!4v1498706523694"
                                width="100%" height="240" frameborder="0" style="border:0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-4">
                        <div class="cube-box-area tags">
                            <div class="cube-box-header">
                                <h3><a href="#">About Us</a></h3>
                            </div>
                            <div class="cube-box-content">
                                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolor magni aut beatae autem aperiam.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-4">
                        <div class="cube-box-area subcribe">
                            <div class="cube-box-header">
                                <h3>Subcribe</h3>
                            </div>
                            <div class="cube-box-content">
                                <form action="{{url('subcribe')}}" method="POST" id='footer-subcribe'>
                                    <input class="form-control form-control-lg" type="email" name="email" placeholder="Email">
                                    <button type="submit" class="btn btn-outline-success btn-block">Subcribe</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end box -->

                <div class="copy row">
                    
                    <div class="col-12 col-sm-5 col-lg-2">
                        <a href="@home_url()">
                            <img src="@public_url('images/logo.png')" alt="">
                        </a>
                    </div>
                    <div class="col-12 col-sm-7 col-lg-5">
                        <h5>Bản quyền</h5>
                        <p>&COPY; 2017 Thế Giới Vuông. All rights reserved.</p>
                        <p>Tổng Biên Tập: <span>Lê Ngọc Doãn</span></p>

                    </div>
                    <div class="col-12 col-sm-12 col-lg-5">
                        <h5>Liên hệ quảng cáo</h5>
                        <p>Tòa soạn Thế Giới Vuông, 172 đường Bà Triệu, tp. Hòa Bình</p>
                        <p>Email: <a href="mailto:lienhe@thegioivuong.com">lienhe@thegioivuong.com</a></p>
                    </div>

                </div>
            </div>
        </footer>