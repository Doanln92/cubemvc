@layout(main);
<h4>Add user</h4>
<?php
$e = new Arr($errors);
$d = new Arr($data);
?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$d->get('id')}}">
            @foreach($inputs, $input)
                <div class="form-group row">
                    <label for="{{$input->name}}" class="col-sm-2 col-form-label">{{$input->text}}</label>
                    <div class="col-sm-6">
                        @if($input->type=='file')
                        <label class="custom-file">
                            <input type="file" id="{{$input->name}}" name="{{$input->name}}" class="custom-file-input {{$e->get($input->name)?'is-invalid':''}}">
                            <span class="custom-file-control">{{$d->get($input->name)?$d->get($input->name):"Chọn file"}}</span>
                        </label>
                        @else
                        <?php echo Html::input(($input->type=="date"?"text":$input->type),$input->name,($input->type=='password'?'':$d->get($input->name)),$input->data, array('id'=>$input->name, 'class'=>"inp-$input->type ".($input->type!='checkbox' && $input->type!='radio'?"form-control ":"").($e->get($input->name)?'is-invalid':''), 'placeholder' => $input->text));?>
                        @endif
                        @if($e->get($input->name))
                        <div class="invalid-feedback">{{$e->get($input->name)}}</div>
                        @endif
            
                    </div>
                </div>
            @end;

            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">{{$btnSaveText}}</button>
                <a href="javascript:history.back(1)" class="btn btn-light">Hủy</a>
                </div>
            </div>
            
        </form>