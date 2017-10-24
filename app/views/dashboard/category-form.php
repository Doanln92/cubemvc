<?php
$d = new Arr($data);
$e = new Arr($errors);



?>
@layout(form);
<form method="POST">
    <input type="hidden" name="id" value="{{$d->get('id')}}">
        @foreach($inputs, $input)
    <div class="form-group row">
        <label for="{{$input->name}}" class="col-sm-2 col-form-label">{{$input->text}}</label>
        <div class="col-sm-6">
            <?php
            $type = $input->type=="date"?"text":$input->type;
            $value = $input->type=='password'?'':$d->get($input->name);
            $class = "inp-$input->type ".($input->type!='checkbox' && $input->type!='radio'?"form-control ":"").($e->get($input->name)?'is-invalid':'');
            $data = ($input->name=='parent_id')?get_select_cat_parent($d->get('id')):$input->data;
            $properties = array(
                    'id'=>$input->name,
                    'class'=> $class,
                    'placeholder' => $input->text
                    );

            echo Html::input($type,$input->name,$value,$data,$properties);
            ?>

            @if($e->get($input->name))
            <div class="invalid-feedback">{{$e->get($input->name)}}</div>
            @endif;

        </div>
    </div>
    @end;


    <div class="form-group row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
        <button type="submit" class="btn btn-primary">{{$btnSaveText}}</button>
        <a href="javascript:history.back(1)" class="btn btn-light">Quay lại</a>
        </div>
    </div>
</form>