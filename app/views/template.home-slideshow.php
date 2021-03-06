<?php


if(count($random_posts)>=5){
    $slide_post = array();
    $i=0;
    foreach($random_posts as $index =>$post){
        $slide_post[$i][]=$post;
        if($index%5==4) $i++;
    }
?>
        <article class="cube-slider hotnews grid-slide" id="cube-hotnews-slider" style="display:none">
            <div class="container">
                <div class="slides">
                    @foreach($slide_post as $slide):
                        @if(count($slide)==5)
                    <div class="slide">
                        <div class="row">
                            {{$first = $slide[0]}}
                            <div class="post-item big-cell col-12 col-sm-12 col-lg-6" style="background-image:url({{$first->getFeatureImage()}})">
                                <a href="{{$first->getUrl()}}" class="post-link">
                                    <div class="post-meta">
                                        <h3 class="post-title">
                                            {{$first->title}}
                                        </h3>
                                        <p>{{$first->short_desc}}</p>
                                    </div>
                                </a>
                                <a href="{{$first->getCategory()->getUrl()}}" class="category-link">{{$first->cat_name}}</a>
                            </div>
                            <div class="col-12 col-sm-12 col-lg-6">
                            @for($i=1;$i<5;$i++)
                                {{$post=$slide[$i]}}
                                @if($i==1)<div class="row">@endif;
                                    <div class="post-item small-cell col-6" style="background-image:url({{$post->getFeatureImage()}})">
                                        <a href="{{$post->getUrl()}}" class="post-link">
                                            <div class="post-meta">
                                                <h3 class="post-title">
                                                {{Str::short($post->title)}}...
                                                </h3>
                                                <p>{{Str::short($post->short_desc,64)}}</p>
                                            </div>
                                        </a>
                                        <a href="{{$post->getCategory()->getUrl()}}" class="category-link">{{$post->cat_name}}</a>
                                    </div>
                                    
                                @if($i==4)</div>@endif;
                            @endfor;
                                
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                        @endif;
                    @endforeach;
                    <!-- end slide -->

                    <div class="buttons">
                        <a href="#" class="btn-left button-preview"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a>
                        <a href="#" class="btn-right button-next"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                        <!-- <div class="dots">
                        <div class="dot active" data-num="1"></div>
                        <div class="dot" data-num="2"></div>
                        <div class="dot" data-num="3"></div>
                        <div class="dot" data-num="4"></div>
                        <div class="dot" data-num="5"></div>
                    </div> -->
                    </div>
                </div>
            </div>
        </article>
        <!-- end slider -->
<?php } ?>