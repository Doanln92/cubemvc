<?php try{ ?><?php
$posts = $cat->getPosts('*','@orderby=id DESC & @limit=5');
?>
<?php if($posts){ ?>
<?php $first = $posts[0]; ?>
<div class="cube-box cat-box cat-box-row row with-color blue border-content">
    <div class="cube-box-header">
        <h3><a href="<?php echoif($cat->getUrl()); ?>"><?php echoif($cat->name); ?></a></h3>
    </div>
    <div class="cube-box-content">
        <div class="row">
            <div class="post-item first-post col-12 col-sm-6 col-lg-6">
                <div class="post-thumb post-thumb">
                    <a href="<?php echoif($first->getUrl()); ?>"><img src="<?php echoif($first->getFeatureImage()); ?>" alt=""></a>
                </div>
                <div class="post-meta">
                    <h4 class="post-title"><a href="#"><?php echoif(Str::short($first->title,70)); ?>...</a></h4>
                    <p><?php echoif(Str::short($first->short_desc,70)); ?>...</p>
                </div>
            </div>

            <div class="post-list col-12 col-sm-6 col-lg-6">
                <?php for($i=1; $i < count($posts); $i++){ ?>
                    <?php $post = $posts[$i]; ?>
                    <div class="post-item row">
                    <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                            <a href="<?php echoif($post->getUrl()); ?>"><img src="<?php echoif($post->getFeatureImage()); ?>" alt="<?php echoif($post->title); ?>"></a>
                        </div>
                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                            <h4 class="post-title"><a href="<?php echoif($post->getUrl()); ?>"><?php echoif(Str::short($post->title,36)); ?>...</a></h4>
                            <p><?php echoif(Str::short($post->short_desc,65)); ?>...</p>
                        </div>
                    </div>
                <?php } ?>
                
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- End Cat box --><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>