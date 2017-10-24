
@if($hotnews)
@$first=$hotnews[0];
    <div id="hotnews" class="cube-box with-color border-content">
        <div class="cube-box-header">
            <h3><a href="@home_url('hot')">#hot news</a></h3>
        </div>
        <div class="cube-box-content">
            <div class="row">
                <div class="post-item first-post thumb-large col-12 col-sm-7 col-lg-7">

                    <div class="post-thumb-bg" style="background-image: url({{$first->getFeatureImage()}})">
                        <a href="#">
                            <div class="post-meta">
                                <h4 class="post-title">{{$first->title}}</h4>
                                <p class="short-desc">
                                {{$first->short_desc}}...
                                </p>
                            </div>
                        </a>
                    </div>

                </div>
                <!-- end first post -->
                <div class="post-list col-12 col-sm-5 col-lg-5">
                    @for($i=1; $i < count($hotnews); $i++)
                        @$post = $hotnews[$i];
                    <div class="post-item row">
                        <div class="post-thumb post-thumb-small col-3">
                            <a href="{{$post->getUrl()}}"><img src="{{$post->getFeatureImage()}}" alt="{{$post->title}}"></a>
                        </div>
                        <div class="post-meta col-9">
                            <h4 class="post-title"><a href="{{$post->getUrl()}}">{{$post->title}}</a></h4>
                        </div>
                    </div>
                    @end;
                </div>
            </div>
        </div>
    </div>
    <!-- end hot news -->
@end

