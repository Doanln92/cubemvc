@layout(main);
@assign('pagetitle','Thay đổi mật khẩu');
<?php
$e = new Arr($errors);
$f = new Arr($form);
$fields = App::data()->getJSON('form/user');
$fieldList = array('oldpassword','newpassword','confirmpassword');
?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$f->get('id')}}">
            @foreach($fields, $input)
                @if(in_array($input->name,$fieldList))
                <div class="form-group row">
                    <label for="{{$input->name}}" class="col-sm-2 col-form-label">{{$input->text}}</label>
                    <div class="col-sm-6">
                        <?php echo Html::input(($input->type=="date"?"text":$input->type),$input->name,($input->type=='password'?'':$f->get($input->name)),$input->data,
                            array(
                                'id'=>$input->name,
                                'class'=>"inp-$input->type ".($input->type!='checkbox' && $input->type!='radio'?"form-control ":"").($e->get($input->name)?'is-invalid':''),
                                'placeholder' => $input->text
                                )
                            );
                            ?>
            
                        @if($e->get($input->name))
                        <div class="invalid-feedback">{{$e->get($input->name)}}</div>
                        @endif;
            
                    </div>
                </div>
                @endif;
            @end;

            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Thay đổi</button>
                <a href="javascript:history.back(1)" class="btn btn-light">Quay lại</a>
                </div>
            </div>
            
        </form>