<?php
$d = new Arr($data);
$e = new Arr($errors);



?>
@layout(form);
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="{{$d->get('id')}}">
        @foreach($inputs, $input)
    <div class="form-group row">
        <label for="post-{{$input->name}}" class="col-sm-2 col-form-label">{{$input->text}}</label>
        <div class="col-sm-10">
            <?php
            $type = $input->type=="date"?"text":$input->type;
            $value = $input->type=='password'?'':$d->get($input->name);
            $class = "inp-$input->type ".($input->type!='checkbox' && $input->type!='radio'?"form-control ":"").($e->get($input->name)?'is-invalid':'');
            $data = ($input->name=='cat_id')?get_select_post_cat_id($d->get('id')):$input->data;
            $properties = array('id'=>'post-'.$input->name, 'class'=> $class, 'placeholder' => $input->text);

            echo Html::input($type,$input->name,$value,$data,$properties);
            ?>

            @if($e->get($input->name))
            <div class="invalid-feedback">{{$e->get($input->name)}}</div>
            @endif;

        </div>
    </div>
    @end;
    <div class="form-group row">
        <label for="image" class="col-sm-2 col-form-label">Hình minh họa</label>
        <div class="col-sm-10">
            <label class="custom-file">
                <input type="file" id="image" name="image" class="custom-file-input {{$e->get('short_desc')?'is-invalid':''}}">
                <span class="custom-file-control">{{$d->get('image')}}</span>
            </label>
            @if($e->get('image'))
                <div class="cube-alert">{{$e->get('image')}}</div>
            @endif
            

        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
        <button type="submit" class="btn btn-primary">{{$btnSaveText}}</button>
        <a href="javascript:history.back(1)" class="btn btn-light">Quay lại</a>
        </div>
    </div>
</form>
    <script>
        var tinymce_url = "@public_url('/res/tinymce')";
    </script>
