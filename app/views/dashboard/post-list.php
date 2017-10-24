@$sort_list = array('id','title','cate_name','view','post_time','posted_by');
@$name_list = array('id'=>'ID','feature_image' => 'Thumbnail','title'=>'Tiêu để','cate_name'=>'chủ đề','view'=>'Lượt xem','post_time'=>'Thời gian','posted_by'=>'Người đăng');
@$req = request();

@layout(main);
<style>
th,td{
	max-width:200px;
}</style>
        <h1>Tin Bài</h1>
        @if($posts)
        @include('search-form')
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <!-- 	id	cat_id	title	slug	content	short_desc	image	post_time	view	posted_by	privacy -->
                    <tr>
                        @include('table-header', compact('name_list','sort_list'));
                        <th><a href="{{url('dashboard/posts/add')}}" class="btn btn-outline-primary">Thêm</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts, $post)
                    
                    <tr>
                        <td>{{$post->id}}</td>
                        <td><img src="{{$post->getFeatureImage()}}" alt="{{$post->title}}"></td>
                        <td>{{$post->title}}</td>
                        <td>{{$post->getCategory()->name}}</td>
                        <td>{{$post->view}}</td>
                        <td>{{$post->post_time}}</td>
                        <td>{{$post->getOwner()->name}} ({{$post->getOwner()->username}})</td>
                        <td>
                            <a href="{{url('dashboard_action','posts','update')}}{{'?id='.$post->id}}">Sửa</a> /
                            <a href="{{url('dashboard_action','posts','delete')}}{{'?id='.$post->id}}">Xóa</a>
                        </td>
                        
                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
        {{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}
        @else
        <p class="alert alert-warning" role="alert">Không có Bài viết nào!</p>
        @endif