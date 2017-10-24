@layout(main);
    <h2>Chi tiết sản phẩm</h2>
	<div class="product-info">
		<div class="row">
            <div class="col-sm-2 ">Tên SP</div>
            <div class="col-sm-10">
                {{$product->name}}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 ">Ảnh</div>
            <div class="col-sm-10">
                <img class="max-200" src="{{$product->getFeatureImage()}}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 ">Chi tiết</div>
            <div class="col-sm-10">
                {{nl2br($product->detail)}};
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 ">Giá sản phẩm</div>
            <div class="col-sm-10">
                {{number_format($product->sell_price, 2, '.', ',')}}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 ">Gallery</div>
            <div class="col-sm-10">
                @if($galleries = $product->getGallery())
                	@foreach($galleries as $gallery)
                		<a href="@e($gallery->link?$gallery->link:'#')"><img src="{{$gallery->getUrl()}}" class="max-200" alt="{{$gallery->text}}"></a>
                	@endforeach
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 ">Người đăng</div>
            <div class="col-sm-10">
                <a href="@home_url('dashboard/products/user/'.$product->owner()->id)">{{$product->owner()->name}}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 "></div>
            <div class="col-sm-10">
                <a href="@home_url('dashboard/products/update?id='.$product->id)"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                <a href="@home_url('dashboard/products/delete?id='.$product->id)"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a>
            </div>
        </div>
	</div>
	<a href="javascript:history.back(1)" class="btn btn-outline-success">Quay lại</a>