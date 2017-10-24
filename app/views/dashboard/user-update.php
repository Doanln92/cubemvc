@layout(main);
@assign('pagetile','Cập nhật thông tin người dùng');
<?php
$e = new Arr($errors);

$fields = array('name','username','email');
$type = array('name' => 'text', 'username' => 'text', 'email' => 'email');
$text = array('name' => 'Họ tên', 'username' => 'Tên đăng nhập', 'email' => 'Email');
?>
<h4>Update User</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$user->id}}">
            @foreach($fields, $name)

            <div class="form-group row">
                <label for="{{$name}}" class="col-sm-2 col-form-label">{{$text[$name]}}</label>
                <div class="col-sm-10">
                    <input type="{{$type[$name]}}" class="form-control {{$e->get($name)?'is-invalid':''}}" id="{{$name}}" name="{{$name}}" value="@if($name!='password')<?php echo $user->{$name}; ?>@end;" placeholder="@if($name!='password'){{$text[$name]}}@else{{'Bỏ qua mật khẩu nếu không muốn thay đổi'}}@end;">
                    @if($e->get($name))
                    <div class="invalid-feedback">{{$e->get($name)}}</div>
                    @endif
                </div>
            </div>

            @end;

            <div class="form-group row">
                <label for="avatar" class="col-sm-2 col-form-label">Hình đại diện</label>
	            <div class="col-sm-10">
	                <label class="custom-file">
	                    <input type="file" id="avatar" name="avatar" class="custom-file-input {{$e->get('avatar')?'is-invalid':''}}">
	                    <span class="custom-file-control">@if($user->avatar){{$user->avatar}}@else{{"Chọn file"}}@end;</span>
	                </label>
	                @if($e->get('avatar'))
	                    <div class="cube-alert">{{$e->get('avatar')}}</div>
	                @endif
	            </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="javascript:history.back(1)" class="btn btn-light">Quay lại</a>
                </div>
            </div>
            
        </form>