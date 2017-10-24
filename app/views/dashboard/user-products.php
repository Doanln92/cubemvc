@layout(main);
	<h2>Tất cả sản phẩm của {{$user->name}}</h2>
	<table class="table">
		<thead>
			<th>ID</th>
			<th>Name</th>
			<th>Feature Image</th>
			<th>Detail</th>
			<th>sell_price</th>
		</thead>
		<tbody>
			@forif($products, $p)
			<tr>
				<td>{{$p->id}}</td>
				<td><a href="{{url('dashboard_action_item','products','view-product',$p->id)}}">{{$p->name}}</a></td>
				<td><img src="{{$p->getFeatureImage()}}" alt=""></td>
				<td>{{$p->detail}}</td>
				<td>{{$p->sell_price}}</td>
			</tr>
			@else
			<tr>
				<td colspan="5">Khong co san pham nao</td>
			</tr>
			@end
		</tbody>
	</table>
	@if($products)
		{{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}
	@end
