@layout(form);
@assign('formtitle','Đăng ký thành viên');

<?php
$f = new Arr($data);
$e = new Arr($errors);
?>


<form method="POST" enctype="multipart/form-data">
    @foreach($inputs, $input)
    <div class="form-group row">
        <label for="{{$input->name}}" class="col-sm-2 col-form-label">{{$input->text}}</label>
        <div class="col-sm-6">
            <?php
            $type = $input->type=="date"?"text":$input->type;
            $value = $input->type=='password'?'':$f->get($input->name);
            $properties = array(
                    'id'=>$input->name,
                    'class'=>"inp-$input->type ".($input->type!='checkbox' && $input->type!='radio'?"form-control ":"").($e->get($input->name)?'is-invalid':''),
                    'placeholder' => $input->text
                    );

            echo Html::input($type,$input->name,$value,$input->data,$properties);
            ?>

            @if($e->get($input->name))
            <div class="invalid-feedback">{{$e->get($input->name)}}</div>
            @endif;

        </div>
    </div>
    @end;

    <div class="form-group row">
        <label for="avatar" class="col-sm-2 col-form-label">Hình đại diện</label>
        <div class="col-sm-6">
            <label class="custom-file">
                <input type="file" id="avatar" name="avatar" class="custom-file-input {{$e->get('avatar')?'is-invalid':''}}">
                <span class="custom-file-control">Chọn file</span>
            </label>
            @if($e->get('avatar'))
                <div class="cube-alert">{{$e->get('avatar')}}</div>
            @endif
        </div>
    </div>
    
    <div class="form-group row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
        <button type="submit" class="btn btn-primary">Đăng ký</button>
        <a href="javascript:history.back(1)" class="btn btn-light">Quay lại</a>
        </div>
    </div>
    
</form>