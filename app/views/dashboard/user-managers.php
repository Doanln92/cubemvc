@layout(main);
@$sort_list = array('id','name','email','created_at','cate_total','post_total','product_total');
@$name_list = array('id'=>'ID','avatar' => 'Ảnh đại diện','name'=>'Họ Tên','email'=>'email','cate_total' => 'Chủ đề đã tạo','post_total' => 'Số tin bài', 'product_total' => 'Số sản phẩm');


<h2>User</h2>
@if($users):
@include('search-form');
<table class="table">
    <thead>
        @include('table-header', compact('name_list','sort_list'));
		
        <th style="text-align: right;"><a href="{{url('dashboard_action','users','add')}}" class="btn btn-success">Thêm mới</a></th>
    </thead>
    <tbody>
        @forif($users, $p)
        <tr>
            <td>{{$p->id}}</td>
            <td><img src="{{$p->getAvatar()}}" alt=""></td>
            <td><a href="{{url('profile-action',$p->username,'info')}}">{{$p->name}}</a></td>
            <td>{{$p->email}}</td>
            <td>{{$p->cate_total}}</td>
            <td>{{$p->post_total}}</td>
            <td>{{$p->product_total}}</td>
            
            <td style="text-align: right;">
                <a href="@home_url('dashboard/users/update?id='.$p->id)"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                <a href="@home_url('dashboard/users/delete?id='.$p->id)"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a>
            </td>
            
        </tr>
        @end
        <tr>
            <td colspan="6">
            @if($users)
                {{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}
            @end
            </td>
            <td colspan="2" style="text-align: right;">
            <a href="{{url('dashboard_action','users','add')}}" class="btn btn-success">Thêm mới</a>
            </td>
        </tr>
    </tbody>
</table>
@else
    <p class="alert alert-warning">
        Danh sách trống
    </p>
@end