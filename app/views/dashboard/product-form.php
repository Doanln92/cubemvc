@layout(form);
<?php
$e = new Arr($errors);
$d = new Arr($data);
?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$d->get('id')}}">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <h4 style="margin-bottom: 20px;">Thông tin sản phẩm</h4>
                @foreach($inputs, $input)
                    <div class="form-group row">
                        <label for="{{$input->name}}" class="col-sm-3 col-form-label">{{$input->text}}</label>
                        <div class="col-sm-9">
                            @if($input->type=='file')
                            <label class="custom-file {{$e->get($input->name)?'is-invalid':''}}">
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

                </div>
                <div class="col-12 col-lg-4">
                    <h4>Gallery</h4>
                    @if($galleries = $product->getGallery())
                        <div class="gallery-images row">
                        @foreach($galleries as $gallery)
                            <div class="gallery-item col-12 col-lg-6" id="gallery-item-{{$gallery->id}}">
                                <div class="gallery-img">
                                    <img src="{{$gallery->getUrl()}}" alt="{{$gallery->text}}">
                                </div>
                                <div class="buttons">
                                    <button type="button" class="btn-remove-gallery btn btn-danger" data-id="{{$gallery->id}}"><i class="fa fa-trash"></i> Xóa</button>
                                </div>
                            </div>
                            
                        @end;
                        <hr>
                        </div>
                    @endif

                    <?php
                    $gallery_total = $d->get('gallery_total')?$d->get('gallery_total'):1;
                    ?>

                    <div id="gallery-upload" class="gallery-upload">
                    @for($i=0; $i < $gallery_total; $i++)

                        <div id="galery-file-{{$i}}" class="gallery-add-image">
                            <div class="gallery-image-file">
                                <input type="file" name="gallery_file_{{$i}}" class="form-control">
                            </div>
                            <div class="gallery-image-text">
                                <input type="text" name="gallery_text[{{$i}}]" class="form-control" value="{{$d->get('gallery_text.'.$i)}}" placeholder="Chú thích (tùy chọn)">
                            </div>
                            <div class="gallery-image-file">
                                <input type="text" name="gallery_link[{{$i}}]" class="form-control" value="{{$d->get('gallery_link.'.$i)}}" placeholder="Liên kết (tùy chọn)">
                            </div>
                        </div>
                    @endfor
                    </div>
                    <div class="buttons" style="text-align: right;">
                        <input type="hidden" name="gallery_total" id="gallery_total" value="{{$d->get('gallery_total')}}">
                        <button type="button" id="button-add-gallery-item">Thêm file</button>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">{{$btnSaveText}}</button>
                <a href="javascript:history.back(1)" class="btn btn-light">Hủy</a>
                </div>
            </div>
            
        </form>