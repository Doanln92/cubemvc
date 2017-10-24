@layout(form);
<?php
$e = new Arr($errors);
$d = new Arr($data);
?>
<style>
    .cart-detail *[class*="col-"]{
        padding:5px;
    }
    .cart-detail .col-2{
        text-align: center;
    }
    .cart-detail .col-5{
        text-align: center;
    }
    .cart-detail .col-5.align-right{
        text-align: right;
    }
    .num{
        text-align:right;
    }

</style>

        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="widget bg-light"  style="margin-right:15px; padding:10px;">
                    <div class="widget-header">
                        <h3>Thanh toán hóa dơn</h3>
                    </div>
                    <div class="widget-content">
                    <div class="cart-detail side" style="display:none;">
                            @$qtt = $cart->get();
                            @$total = 0;
                            <div class="row">
                                <div class="col-2">
                                    SL
                                </div>
                                <div class="col-5">
                                    TÊN
                                </div>
                                <div class="col-5 align-center">
                                    THÀNH TIỀN
                                </div>
                            </div>
                            <div class="list" style="max-height:300px; overfllow-y:scroll; ">
                            @foreach($products, $p)
                                <div class="row">
                                    <div class="col-2">
                                        {{$qtt[$p->id]}}
                                    </div>
                                    <div class="col-5">
                                        {{$p->name}}
                                    </div>
                                    <div class="col-5 align-right">
                                        <?php
                                        $money = $qtt[$p->id]*$p->sell_price;
                                        $total = $total+$money;
                                        ?>
                                        
                                        {{number_format($money,2,',','.')}}
                                    </div>
                                </div>

                            @end;
                            
                            </div>
                            
                        </div>
                        <div class="buttons">
                            <a class="btn-cart-detail" href="#">Chi tiết</a>
                        </div>

                        <div class="row product-total">
                            <div class="col-6">
                                <p class="lab">Tổng sổ mặt hàng</p>
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
                        
                        
                        
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="cart_verify" value="{{md5(time())}}">
                    @foreach($inputs, $input)
                        @if($input->type=='hidden')
                        <input type="hidden" name="{{$input->name}}" id="{{$input->name}}" value=="{{$d->get($input->name)}}">
                        @else
                        <div class="form-group row">
                            <label for="{{$input->name}}" class="col-sm-3 col-form-label">{{$input->text}}</label>
                            <div class="col-sm-9">
                                @if($input->type=='file')
                                <label class="custom-file">
                                    <input type="file" id="{{$input->name}}" name="{{$input->name}}" class="custom-file-input {{$e->get($input->name)?'is-invalid':''}}">
                                    <span class="custom-file-control">{{$d->get($input->name)?$d->get($input->name):"Chọn file"}}</span>
                                </label>
                                @else
                                <?php echo Html::input(($input->type=="date"?"text":$input->type),$input->name,($input->type=='password'?'':$d->get($input->name)),$input->data, array('id'=>$input->name, 'class'=>"inp-$input->type ".($input->type!='checkbox' && $input->type!='radio'?"form-control ":"").($e->get($input->name)?'is-invalid':''), 'placeholder' => $input->text));?>
                                @endif
                                @if($e->get($input->name))
                                <div class="invalid-feedback">{{$e->get($input->name)}}</div>
                                @endif
                    
                            </div>
                        </div>
                        @endif
                    @end;

                    <div class="form-group row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">{{$btnSaveText}}</button>
                        <a href="javascript:history.back(1)" class="btn btn-light">Hủy</a>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>