<!-- sidebar -->
                    <aside class="sidebar col-12 col-sm-12 col-lg-4">
                        <div class="cube-box widget ads">
                            <div class="content">
                                <a href="#"><img src="@public_url('images/banner-1.gif')" alt="Quảng cáo"></a>
                            </div>
                        </div>
                        @if($posts = Models\Post::where('id>0')->orderBy('view','DESC')->limit(10)->get())
                        <div class="cube-box with-color border-content">
                            <div class="cube-box-header">
                                <h3><a href="{{url('popular')}}">Popular</a></h3>
                            </div>
                            <div class="cube-box-content">
                                <div class="post-list">
                                    @foreach($posts as $p)
                                    <div class="post-item row">
                                        <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                                            <a href="{{$p->getUrl()}}"><img src="{{$p->getFeatureImage()}}" alt="{{$p->title}}"></a>
                                        </div>
                                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                                            <h4 class="post-title"><a href="{{$p->getUrl()}}">{{$p->title}}</a></h4>
                                            <p>{{$p->short_desc}}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                    

                                </div>
                            </div>

                        </div>
                        @endif
                        
                        <div class="cube-box widget ads">
                            <div class="content">
                                <a href="#"><img src="@public_url('images/banner-2.jpg')" alt="Quảng cáo"></a>
                            </div>
                        </div>

                    </aside>
                    <!-- end sidebar -->