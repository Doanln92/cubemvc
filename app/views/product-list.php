@layout('form');
        <div class="product-list row">
            @forif($products, $p)
                <div class="product-item col-6 col-sm-4 col-lg-3">
                    
                    <div class="product">
                        <div class="baseinfo">
                            <div class="thumb">
                                <a href="@home_url('shop/view-product/'.$p->id)"><img src="{{$p->getFeatureImage()}}" alt="{{$p->name}}"></a>
                            </div>
                            <div class="meta">
                                <h4><a href="@home_url('shop/view-product/'.$p->id)">{{$p->name}}</a></h4>
                                <p class="price">
                                    Giá: <span class="price-text">{{$p->sell_price}} Đ</span>
                                </p>
                            </div>
                        </div>
                        <div class="product-detail">
                            <div class="name-price">
                                <h4 class="product-name">
                                    <a href="@home_url('shop/view-product/'.$p->id)">{{$p->name}}</a>
                                </h4>
                                <span class="price-text">{{$p->sell_price}} Đ</span>
                            </div>
                            <div class="detail-info">
                                <p>{{Str::short($p->detail,50)}}</p>
                            </div>
                            <div class="buttons">
                                <button type="button" class="btn btn-warning btn-add-to-cart" data-product="{{$p->id}}">Thêm vào giỏ hàng</button>
                            </div>
                        </div>
                    </div>

                </div>
            @end;
            
        </div>
{{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}
