@get_header(form);
<article class="cube-layout with-sidebar">
    <div class="container">
        <div class="row">
            <section class="main-content col-12 col-sm-12 col-lg-8">
            <div class="super-box view-post with-color red border-content">
                <div class="super-box-header type-2">
                    <h2>{{$pagetitle}}</h2>
                </div>
                <div class="super-box-content" style="padding:20px 10px;">
                    @view_content();
                </div>
            </div>
            
            </section>

            <!-- side bar -->
            <aside class="sidebar col-12 col-sm-12 col-lg-4">
                <div class="cube-box widget">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.927418267965!2d105.7624513149328!3d21.035589985994637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454b92b75eded%3A0x5b8ed5d074fbd3b!2zVHLGsOG7nW5nIENhbyDEkOG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2s!4v1498706523694"
                        width="100%" height="240" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
                @if($products = get_model('Product')->orderBy('view','DESC')->orderBy('id','DESC')->limit(5)->get())
                <div class="cube-box with-color border-content">
                    <div class="cube-box-header type-2">
                        <h3><a href="#Hot">#Bán chạy</a></h3>
                    </div>
                    <div class="cube-box-content">
                        <div class="post-list">
                            @foreach($products as $p)
                            <div class="post-item row">
                                <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                                    <a href="@home_url('shop/view-product/'.$p->id)"><img src="{{$p->getFeatureImage()}}" alt="{{$p->name}}"></a>
                                </div>
                                <div class="post-meta col-12 col-sm-9 col-lg-9">
                                    <h4 class="post-title"><a href="@home_url('shop/view-product/'.$p->id)">{{$p->name}}</a></h4>
                                    <p>{{Str::short($p->detail,40)}}</p>
                                </div>
                            </div>
                            @endforeach
                            

                        </div>
                    </div>

                </div>
                @endif
                

            </aside>
        </div>
    </div>
</article>
@get_footer(form);