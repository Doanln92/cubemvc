@layout('main');

<div class="super-box with-color green border-content">
    <div class="super-box-header">
        <h2><a href="{{url('listnews',$category->slug)}}">{{$category->name}}</a></h2>
    </div>
    <div class="super-box-content">
        <div class="post-list">
            @forif($posts, $p)

            <div class="post-item row">
                <div class="post-thumb col-12 col-sm-5 col-lg-4">
                    <a href="{{$p->getUrl()}}"><img src="{{$p->getFeatureImage(320,200)}}" alt="{{$p->title}}"></a>
                </div>
                <div class="post-detail col-12 col-sm-7 col-lg-8">
                    <h4 class="post-title"><a href="{{$p->getUrl()}}">{{$p->title}}</a></h4>
                    <p class="post-meta">
                        <!-- <span class="meta post-author">
                            <span class="glyphicon glyphicon-user"></span> <span>Author</span>
                        </span> -->
                        <span class="meta post-category">
                            <a href="{{$p->getCategory()->getUrl()}}"><i class="fa fa-folder" aria-hidden="true"></i> <span>{{$p->getCategory()->name}}</span></a>
                        </span>
                        <span class="meta post-time">
                        <i class="fa fa-clock-o" aria-hidden="true"></i> <time datetime="{{$p->post_time}}">{{date('d/m/Y',strtotime($p->post_time))}}</time>
                        </span>
                        <!-- <span class="meta post-view">
                            <span class="glyphicon glyphicon-eye-open"></span> <span>100 lượt xem</span>
                        </span> -->
                    </p>
                    <p class="post-desc">
                        {{$p->short_desc}}
                    </p>
                    <a href="{{$p->getUrl()}}" class="btn btn-primary">Đọc tiếp</a>

                </div>
            </div>
            @else
                <p class="alert alert-warning">Không có tin bài nào</p>
            @end
            
        </div>
    </div>
</div>
{{$paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))}}