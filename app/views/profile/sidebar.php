<nav class="col-sm-3 col-md-2 d-none d-sm-block bg-light sidebar">
    @if(isset($profile))
    <div class="avatar" style="text-align:center;margin: 15px auto">
        <img src="{{$profile->getAvatar()}}" alt="{{$profile->name}}" style="max-width:100%;max-height:200px;">
        <h4>
        {{$profile->name}}
        </h4>
    </div>
    @$CHM = get_extension('CubeHtmlMenu');
    @if(_session('userid')==$profile->id)
        {{$menuArgs = array('type'=>'json','args'=>array('file'=>'profile-sidebar'))}}
    @else
        {{$menuArgs = array('type'=> 'list', 'list' => array(array('path'=>'info','text'=>"Trang cá nhân", 'title'=>"Trang cá nhân")))}}
    @endif;
    {{$menu_options = array('menu_class'=>'nav nav-pills flex-column', 'menu_item_class' => 'nav-item', 'menu_link_class' => 'nav-link')}}
    {{$CHM::createMenu($menuArgs, url('profile-info',$profile->username), 3, $menu_options)}}

    @endif;
</nav> 