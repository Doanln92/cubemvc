@$catlst = $cat->andWhere('parent_id',$cat->id)->limit(4)->get();
<div class="cube-box-area bg-black cube-tab">
    <div class="cube-box-header cube-tab-header">
        <h3><a href="{{$cat->getUrl()}}">{{$cat->name}}</a></h3>
        <ul class="tab-buttons">
            @forif($catlst,$i,$c)
            <li class="tab-btn {{$i?'':'active'}}"><a href="#cat-{{$c->id}}">{{$c->name}}</a></li>
            @end;
        </ul>
    </div>
    <div class="cube-box-content tabs">
        @forif($catlst,$i,$c)
        <?php
            $posts = $c->getPosts('*',array(
                '@orderby' => 'id desc',
                '@limit' => 5
            ));
        ?>
        
        <div id="cat-{{$c->id}}" class="tab {{$i?'':'active'}}">
            <div class="cube-box cat-box cat-box-row row">
                <div class="cube-box-content">
                    @forif($posts, $n, $post)
                        @if($n==0)
                        <div class="row post-item first-post">
                            <div class="post-thumb post-thumb-large col-12 col-sm-6 col-lg-6">
                                <a href="{{$post->getUrl()}}"><img src="{{$post->getFeatureImage()}}" alt=""></a>
                            </div>
                            <div class="post-meta col-12 col-sm-6 col-lg-6">
                                <h4 class="post-title"><a href="{{$post->getUrl()}}">{{Str::short($post->title,64)}}</a></h4>
                                <p>{{Str::short($post->short_desc,78)}}...</p>
                                <p class="buttons">
                                    <a href="{{$post->getUrl()}}" class="btn btn-success">Xem thêm</a>
                                </p>
                            </div>

                        </div>
                        @else
                            @if($n==1)<div class="post-list bg-transparent row">@end;
                                <div class="post-item col-12 col-sm-6 col-lg-6">
                                    <div class="row">
                                        <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                                            <a href="{{$post->getUrl()}}"><img src="{{$post->getFeatureImage()}}" alt="{{$post->title}}"></a>
                                        </div>
                                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                                            <h4 class="post-title"><a href="{{$post->getUrl()}}">{{Str::short($post->title,36)}}...</a></h4>
                                            <p>{{Str::short($post->short_desc,80)}}...</p>
                                        </div>
                                    </div>
                                </div>
                            @if($n==count($posts)-1)</div>@end;
                        @end;
                    @end;
                </div>
            </div>
        </div>
        @end;
    </div>
</div>