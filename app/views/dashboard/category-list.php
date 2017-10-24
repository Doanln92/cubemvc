@$sort_list = array('id','name','slug','parent_id','created_by');
@$name_list = array('id'=>'ID','name'=>'Tên','slug'=>'Đường dẫn','description'=>'Mô tả', 'parent_id' => 'Chủ đề cha','created_by'=>'Người tạo');
@$req = request();
@layout(main);
        <h1>Chủ đề</h1>

        @if($categories)
        @include('search-form')
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        @include('table-header', compact('name_list','sort_list'));
                        <th><a href="{{url('dashboard/categories/add')}}" class="btn btn-success">Thêm mới</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories, $cat)
                    
                    <tr>
                        <td>{{$cat->id}}</td>
                        <td>{{$cat->name}}</td>
                        <td>{{$cat->slug}}</td>
                        <td>{{$cat->description}}</td>
                        <td>{{$cat->getParentName()}}</td>
                        <td>{{$cat->getOwner()->name}} ({{$cat->getOwner()->username}})</td>
                        <td>
                            <a href="{{url('dashboard/categories/update?id='.$cat->id)}}">Sửa</a> /
                            <a href="{{url('dashboard/categories/delete?id='.$cat->id)}}">Xóa</a>
                        </td>
                        
                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
        {{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}
        @else
        <p class="alert alert-warning" role="alert">Không có chủ đề nào!</p>
        @endif