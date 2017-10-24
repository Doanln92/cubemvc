<div class="row category-row">
    @foreach($cat as $c)
    <div class="cube-box cat-box with-color green border-content col-12 col-sm-6 col-lg-6">
        <div class="cube-box-header">
            <h3><a href="{{$c->getUrl()}}">{{$c->name}}</a></h3>
        </div>
        <div class="cube-box-content">
            <?php $posts = $c->getPosts('*', array('@orderby'=>'id DESC','@limit'=>5)); ?>
            
            @forif($posts as $i => $post)
            
                @if($i==0)
                <div class="post-item first-post">
                    <a href="{{$post->getUrl()}}">    
                        <div class="post-thumb-bg" style="background-image: url({{$post->getFeatureImage()}})">
                        
                        </div>
                    </a>
                    <div class="post-meta">
                    <h4 class="post-title"><a href="{{$post->getUrl()}}">{{Str::short($post->title,64)}}...</a></h4>
                    <p>{{Str::short($post->short_desc,78)}}...</p>
                    </div>
                </div>
                @else
                    @if($i==1)<div class="post-list">@end
                    <div class="post-item row">
                        <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                            <a href="{{$post->getUrl()}}"><img src="{{$post->getFeatureImage()}}" alt="{{$post->title}}"></a>
                        </div>
                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                        <h4 class="post-title"><a href="{{$post->getUrl()}}">{{Str::short($post->title,36)}}...</a></h4>
                            <p>{{Str::short($post->short_desc,80)}}...</p>
                        </div>
                    </div>
                    @if($i==count($posts)-1):</div>@end
                @end
            @end
        </div>
    </div>
    @end
    <!-- End Cat box -->
</div>