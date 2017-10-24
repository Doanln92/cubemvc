@layout('main');
{{$cate = $post->getCategory()}}
<div class="cube-box view-post with-color red border-content">
<div class="cube-box-header type-2">
    <h3><a href="{{$cate->getUrl()}}">{{$cate->name}}</a></h3>
</div>
<div class="cube-box-content">
    <div class="post-header">
        <h2 class="post-title">{{$post->title}}</h2>
        <p class="post-meta">
            <span class="meta post-author">
                <a href="@home_url('profile/'.$post->getOwner()->username)"><i class="fa fa-user" aria-hidden="true"></i> <span>{{$post->getOwner()->name}}</span></a>
            </span>
            <span class="meta post-category">
                <a href="{{$cate->getUrl()}}"><i class="fa fa-folder" aria-hidden="true"></i> <span>{{$cate->name}}</span></a>
            </span>
            <span class="meta post-time">
            <i class="fa fa-clock-o" aria-hidden="true"></i> <time datetime="{{$post->post_time}}">{{$post->getTime()}}</time>
            </span>
            <span class="meta post-view">
            <i class="fa fa-eye" aria-hidden="true"></i> <span>{{$post->view}} lượt xem</span>
            </span>
        </p>
    </div>
    <div class="post-content">
    {{$post->content}}
    </div>
</div>
</div>
@if($relative)
<div class="cube-box post-list type-2 with-color purple border-content">
<div class="cube-box-header type-2">
    <h3>Bài viết liên quan</h3>
</div>
<div class="cube-box-content">
    <div class="row list-content">
        @foreach($relative as $p)
        <div class="post-item col-6 col-sm-4">
            <div class="post-thumb">
                <a href="{{$p->getUrl()}}"><img src="{{$p->getFeatureImage()}}" alt=""></a>
            </div>
            <h5 class="post-title"><a href="{{$p->getUrl()}}">{{$p->title}}</a></h5>
        </div>
        @end;
    </div>
</div>
</div>
@end;