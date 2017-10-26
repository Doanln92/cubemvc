<?php try{ ?><div class="row category-row">
    <?php foreach($cat as $c){ ?>
    <div class="cube-box cat-box with-color green border-content col-12 col-sm-6 col-lg-6">
        <div class="cube-box-header">
            <h3><a href="<?php echoif($c->getUrl()); ?>"><?php echoif($c->name); ?></a></h3>
        </div>
        <div class="cube-box-content">
            <?php $posts = $c->getPosts('*', array('@orderby'=>'id DESC','@limit'=>5)); ?>
            
            <?php if(is_array($posts) && count($posts) > 0) foreach($posts as $i => $post){ ?>
            
                <?php if($i==0){ ?>
                <div class="post-item first-post">
                    <a href="<?php echoif($post->getUrl()); ?>">    
                        <div class="post-thumb-bg" style="background-image: url(<?php echoif($post->getFeatureImage()); ?>)">
                        
                        </div>
                    </a>
                    <div class="post-meta">
                    <h4 class="post-title"><a href="<?php echoif($post->getUrl()); ?>"><?php echoif(Str::short($post->title,64)); ?>...</a></h4>
                    <p><?php echoif(Str::short($post->short_desc,78)); ?>...</p>
                    </div>
                </div>
                <?php } else { ?>
                    <?php if($i==1){ ?><div class="post-list"><?php } ?>
                    <div class="post-item row">
                        <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                            <a href="<?php echoif($post->getUrl()); ?>"><img src="<?php echoif($post->getFeatureImage()); ?>" alt="<?php echoif($post->title); ?>"></a>
                        </div>
                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                        <h4 class="post-title"><a href="<?php echoif($post->getUrl()); ?>"><?php echoif(Str::short($post->title,36)); ?>...</a></h4>
                            <p><?php echoif(Str::short($post->short_desc,80)); ?>...</p>
                        </div>
                    </div>
                    <?php if($i==count($posts)-1){ ?></div><?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <!-- End Cat box -->
</div><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>