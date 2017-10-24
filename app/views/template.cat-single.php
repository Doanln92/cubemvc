<?php
$posts = $cat->getPosts('*','@orderby=id DESC & @limit=5');
?>
@if($posts)
@$first = $posts[0];
<div class="cube-box cat-box cat-box-row row with-color blue border-content">
    <div class="cube-box-header">
        <h3><a href="{{$cat->getUrl()}}">{{$cat->name}}</a></h3>
    </div>
    <div class="cube-box-content">
        <div class="row">
            <div class="post-item first-post col-12 col-sm-6 col-lg-6">
                <div class="post-thumb post-thumb">
                    <a href="{{$first->getUrl()}}"><img src="{{$first->getFeatureImage()}}" alt=""></a>
                </div>
                <div class="post-meta">
                    <h4 class="post-title"><a href="#">{{Str::short($first->title,70)}}...</a></h4>
                    <p>{{Str::short($first->short_desc,70)}}...</p>
                </div>
            </div>

            <div class="post-list col-12 col-sm-6 col-lg-6">
                @for($i=1; $i < count($posts); $i++)
                    @$post = $posts[$i];
                    <div class="post-item row">
                    <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                            <a href="{{$post->getUrl()}}"><img src="{{$post->getFeatureImage()}}" alt="{{$post->title}}"></a>
                        </div>
                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                            <h4 class="post-title"><a href="{{$post->getUrl()}}">{{Str::short($post->title,36)}}...</a></h4>
                            <p>{{Str::short($post->short_desc,65)}}...</p>
                        </div>
                    </div>
                @end
                
            </div>
        </div>
    </div>
</div>
@end
<!-- End Cat box -->