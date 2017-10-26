<?php try{ ?>
<?php if($hotnews){ ?>
<?php $first=$hotnews[0]; ?>
    <div id="hotnews" class="cube-box with-color border-content">
        <div class="cube-box-header">
            <h3><a href="<?php home_url('hot'); ?>">#hot news</a></h3>
        </div>
        <div class="cube-box-content">
            <div class="row">
                <div class="post-item first-post thumb-large col-12 col-sm-7 col-lg-7">

                    <div class="post-thumb-bg" style="background-image: url(<?php echoif($first->getFeatureImage()); ?>)">
                        <a href="#">
                            <div class="post-meta">
                                <h4 class="post-title"><?php echoif($first->title); ?></h4>
                                <p class="short-desc">
                                <?php echoif($first->short_desc); ?>...
                                </p>
                            </div>
                        </a>
                    </div>

                </div>
                <!-- end first post -->
                <div class="post-list col-12 col-sm-5 col-lg-5">
                    <?php for($i=1; $i < count($hotnews); $i++){ ?>
                        <?php $post = $hotnews[$i]; ?>
                    <div class="post-item row">
                        <div class="post-thumb post-thumb-small col-3">
                            <a href="<?php echoif($post->getUrl()); ?>"><img src="<?php echoif($post->getFeatureImage()); ?>" alt="<?php echoif($post->title); ?>"></a>
                        </div>
                        <div class="post-meta col-9">
                            <h4 class="post-title"><a href="<?php echoif($post->getUrl()); ?>"><?php echoif($post->title); ?></a></h4>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- end hot news -->
<?php } ?>

<?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>