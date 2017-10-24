@layout(main);
@assign('pagetitle','Thông tin cá nhân');

<style>
    .row{
        margin-bottom: 15px;
    }
</style>
@if($profile)
    @foreach($inputs, $input)
        <div class="row">
            <div class="col-12 col-sm-3 col-lg-2">
                {{$input->text}}
            </div>
            <div class="col-12 col-sm-9 col-lg-10">
                @e($input->name=='gender'?$input->data[$profile->{$input->name}]:$profile->{$input->name})
            </div>
        </div>
        
    @endforeach;

        <div class="row">
            <div class="col-12 col-sm-3 col-lg-2">
                Chủ đề đã tạo
            </div>
            <div class="col-12 col-sm-9 col-lg-10">
                {{$cate_total}}
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-3 col-lg-2">
                Tổng số tin bài
            </div>
            <div class="col-12 col-sm-9 col-lg-10">
                {{$post_total}}
            </div>
        </div>
@endif;