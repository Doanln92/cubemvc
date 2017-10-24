@layout(main);
@assign('pagetitle','Tất cả sàn phẩm');

@$sort_list = array('id','name','sell_price','created_at','created_by','view');
@$name_list = array('id'=>'ID','feature_image' => 'Ảnh sản phẩm','name'=>'Tên sản phẩm','detail'=>'Mô tả','sell_price' => 'Giá bán','created_at' => 'Thời gian','created_by' => 'Người đăng','view'=>'Lượt xem');



<style>
th,td{
	max-width:200px;
}</style>
	<h2>Tất cả sản phẩm</h2>
	@include('search-form')
	
	<div class="table-responsive">
	    <table class="table table-striped">
			<thead>
				@include('table-header', compact('name_list','sort_list'));
				
				<th style="text-align: right;">
					<a href="@home_url('dashboard/products/add')" class="btn btn-success">Thêm mới</a>
				</th>
			</thead>
			<tbody>
				@forif($products, $p)
				<tr>
					<td>{{$p->id}}</td>
					<td><img src="{{$p->getFeatureImage()}}" alt=""></td>
					<td><a href="{{url('dashboard_action_item','products','view-product',$p->id)}}">{{$p->name}}</a></td>
					<td>{{$p->detail}}</td>
					<td>{{$p->sell_price}}</td>
					<td>{{$p->getTime('M d, Y')}}</td>
					
					<td><a href="{{url('dashboard_action_item','products','user-product',$p->owner()->id)}}">{{$p->owner()->name}}</a></td>
					<td>{{$p->view}}</td>
					
					<td>
						<a href="@home_url('dashboard/products/update?id='.$p->id)"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
						<a href="@home_url('dashboard/products/delete?id='.$p->id)"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a>
					</td>
				</tr>
				@else
				<tr>
					<td colspan="7">Khong co san pham nao</td>
				</tr>
				@end;
				<tr>
					<td colspan="6">
						@if($products)
							{{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}
						@end;

					</td>
					<td colspan="3" style="text-align: right;">
						<a href="@home_url('dashboard/products/add')" class="btn btn-success">Thêm mới</a>
					</td>

				</tr>
			</tbody>
		</table>
	</div>