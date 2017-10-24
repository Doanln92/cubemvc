
            <nav class="col-sm-3 col-md-2 d-none d-sm-block bg-light sidebar">
            @$CHM = get_extension('CubeHtmlMenu');
            @if(get_model('User')::getCurrentLogin()->level < 1)
                @$filename = 'user-sidebar';
            @else
                @$filename = 'admin-sidebar';
            @endif
            {{$menuArgs = array('type'=>'json','args'=>array('file'=>$filename))}}
            {{$menu_options = array('menu_class'=>'nav nav-pills flex-column', 'menu_item_class' => 'nav-item', 'menu_link_class' => 'nav-link')}}
            {{$CHM::createMenu($menuArgs, Cube::getUrl('dashboard'), 2, $menu_options)}}
            </nav> 