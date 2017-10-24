
@layout(main);
        <h1>Dashboard</h1>
<style>

</style>
        <section class="row placeholders">
            
            	<div class="col-12 col-sm-6">
            		<h3>Bài viết mới</h3>
            		@if($posts)
			        <div class="table-responsive">
			            <table class="table table-striped">
			                <thead>
			                    <tr>
			                        <th>Title</th>
			                        <th>Cate</th>
									<th>Ngày</th>
			                        <th class="text-center">Views</th>
			                    </tr>
			                </thead>
			                <tbody>
								@foreach($posts, $post)
									
								<tr>
									<td><a href="{{$post->getUrl()}}">{{$post->title}}</a></td>
									<td>{{$post->getCategory()->name}}</td>
									<td class="text-right">{{$post->getTime('d/m/Y')}}</td>
									<td class="text-center">{{$post->view}}</td>
									
								</tr>

			                    @endforeach

			                </tbody>
			            </table>
			        </div>
			        @endif
            	</div>
            	<div class="col-12 col-sm-6">
            		<h3>Chủ đề</h3>
			        @if($categories)
			        
			        <div class="table-responsive">
			            <table class="table table-striped">
			                <thead>
			                    <tr>
			                        <th>Slug</th>
			                        <th>Created by</th>
			                        
			                    </tr>
			                </thead>
			                <tbody>
			                    @foreach($categories, $cat)
			                    
			                    <tr>
			                        <td>{{$cat->name}}</td>
			                        <td>{{$cat->getOwner()->name}} ({{$cat->getOwner()->username}})</td>
			                        
			                    </tr>

			                    @endforeach

			                </tbody>
			            </table>
			        </div>
			        @endif
            	</div>
            	<div class="col-12 col-sm-6">
            		<h3>Sản phẩm mới</h3>
					<div class="table-responsive">
					    <table class="table table-striped">
							<thead>
								<th>Ảnh</th>
								<th>Name</th>
								<th>Sell Price</th>
								<th>Onwer</th>
								
							</thead>
							<tbody>
								@forif($products, $p)
								<tr>
									<td class=""><img src="{{$p->getFeatureImage()}}" alt=""></td>
									<td><a href="{{url('dashboard_action_item','products','view-product',$p->id)}}">{{$p->name}}</a></td>
									<td class="text-right">{{$p->sell_price}}</td>
									<td><a href="{{url('dashboard_action_item','products','user-product',$p->owner()->id)}}">{{$p->owner()->name}}</a></td>
								</tr>
								@else
								<tr>
									<td colspan="7">Khong co san pham nao</td>
								</tr>
								@end;
								
							</tbody>
						</table>
					</div>
            	</div>
            	<div class="col-12 col-sm-6">
            		<h3>Người dùng mới</h3>
					<div class="table-responsive">
					    <table class="table table-striped">
							<thead>
						        <th>Avatar</th>
						        <th>Name</th>
						        <th>Email</th>
						    </thead>
						    <tbody>
						        @forif($users, $p)
						        <tr>
						            <td><img style="max-width: 90px; max-height: 90px;" src="{{$p->getAvatar()}}" alt=""></td>
						            <td><a href="{{url('profile-action',$p->username,'info')}}">{{$p->name}}</a></td>
						            <td>{{$p->email}}</td>
						            
						        </tr>
						        @else
						        <tr>
						            <td colspan="3">Khong co san pham nao</td>
						        </tr>
						        @endforif
						    </tbody>
						</table>
					</div>
            	</div>
            
        </section>
    </main>

    