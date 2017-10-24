@get_header(form);
        <article class="cube-layout with-sidebar">
            <div class="container">
                <div class="row">
                    <section class="main-content col-12 col-sm-12 col-lg-8">
                        <div class="super-box view-post with-color red border-content">
                            <div class="super-box-header type-2">
                                <h2>Giỏ hàng</h2>
                            </div>
                            <div class="super-box-content" style="padding:20px 10px;">
                            @if($products)
                                <form method="POST" id="order-form">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên sản phẩm</th>
                                                <th>Đơn giá</th>
                                                <th>Số lượng</th>
                                                <th>x</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @$ar_qtt = arc_num(1,20);
                                            @foreach($products as $n => $p)
                                            <tr id="product-info-{{$p->id}}">
                                                <td class="num">@e(($paging->current_page-1)*10+$n+1)</td>
                                                <td class="produvt-info">{{$p->name}}</td>
                                                <td class="">{{$p->sell_price}}</td>
                                                <td class="">
                                                    {{Html::getSelectTag('quantity[]',$ar_qtt,$cart->getQuantity($p->id),array('data-product' => $p->id,'id'=>'prd-qtt-'.$p->id,'class'=>'product-quantity'))}}
                                                </td>
                                                <td class="act">
                                                    <button type="button" class="btn btn-outline-danger btn-remove-from-cart" data-product="{{$p->id}}"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach;
                                        </tbody>
                                    </table>
                                </form>
                            @else
                                <div class="alert warning">
                                    <p>Bản không có sản phẩm nào trong giỏ</p>
                                </div>
                            @endif;


                            </div>
                        </div>
                        @if($products){{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}@endif;
                    </section>
                    @if($cart->count()>0)
                    <!-- side bar -->
                    <aside class="sidebar col-12 col-sm-12 col-lg-4">
                        <div class="cube-box widget">
                           <div class="widget-header">
                                <h3>Thanh toán hóa đơn</h3>
                            </div>
                            <div class="widget-content">
                                <div class="row product-total">
                                    <div class="col-6">
                                        <p class="lab">Sổ sản phẩm</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="num"><span id="product-total"><span class="cart-item-total">{{$cart->count()}}</span></span></p>
                                    </div>
                                </div>
                                
                                <div class="row money-total">
                                    <div class="col-5">
                                        <p class="lab">Tổng thành tiền</p>
                                    </div>
                                    <div class="col-7">
                                        <p class="num"><span id="munny-total">{{number_format($cart->totalMoney(),2,',','.')}}</span> Đ</p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                
                                <div class="buttons">
                                    <button type="button" class="btn btn-success btn-order-cart">Thanh toán</button>
                                    <button type="button" class="btn btn-remove-all-cart">Hủy Đơn hàng</button>
                                </div>
                            </div>
                        </div>
                        
                    </aside>
                    <!-- end sidebar -->
                    @endif;
                </div>
            </div>
        </article>
        <form action="{{url('shop/order')}}" method="post" id="order-cart">
            <input type="hidden" name="security" value="{{md5(time())}}">
        </form>
@get_footer(form);
