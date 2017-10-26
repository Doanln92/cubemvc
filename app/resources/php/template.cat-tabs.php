<?php try{ ?><?php $catlst = $cat->andWhere('parent_id',$cat->id)->limit(4)->get(); ?>
<div class="cube-box-area bg-black cube-tab">
    <div class="cube-box-header cube-tab-header">
        <h3><a href="<?php echoif($cat->getUrl()); ?>"><?php echoif($cat->name); ?></a></h3>
        <ul class="tab-buttons">
            <?php if(is_array($catlst) && count($catlst) > 0) foreach($catlst as $i => $c){ ?>
            <li class="tab-btn <?php echoif($i?'':'active'); ?>"><a href="#cat-<?php echoif($c->id); ?>"><?php echoif($c->name); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <div class="cube-box-content tabs">
        <?php if(is_array($catlst) && count($catlst) > 0) foreach($catlst as $i => $c){ ?>
        <?php
            $posts = $c->getPosts('*',array(
                '@orderby' => 'id desc',
                '@limit' => 5
            ));
        ?>
        
        <div id="cat-<?php echoif($c->id); ?>" class="tab <?php echoif($i?'':'active'); ?>">
            <div class="cube-box cat-box cat-box-row row">
                <div class="cube-box-content">
                    <?php if(is_array($posts) && count($posts) > 0) foreach($posts as $n => $post){ ?>
                        <?php if($n==0){ ?>
                        <div class="row post-item first-post">
                            <div class="post-thumb post-thumb-large col-12 col-sm-6 col-lg-6">
                                <a href="<?php echoif($post->getUrl()); ?>"><img src="<?php echoif($post->getFeatureImage()); ?>" alt=""></a>
                            </div>
                            <div class="post-meta col-12 col-sm-6 col-lg-6">
                                <h4 class="post-title"><a href="<?php echoif($post->getUrl()); ?>"><?php echoif(Str::short($post->title,64)); ?></a></h4>
                                <p><?php echoif(Str::short($post->short_desc,78)); ?>...</p>
                                <p class="buttons">
                                    <a href="<?php echoif($post->getUrl()); ?>" class="btn btn-success">Xem thêm</a>
                                </p>
                            </div>

                        </div>
                        <?php } else { ?>
                            <?php if($n==1){ ?><div class="post-list bg-transparent row"><?php } ?>
                                <div class="post-item col-12 col-sm-6 col-lg-6">
                                    <div class="row">
                                        <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                                            <a href="<?php echoif($post->getUrl()); ?>"><img src="<?php echoif($post->getFeatureImage()); ?>" alt="<?php echoif($post->title); ?>"></a>
                                        </div>
                                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                                            <h4 class="post-title"><a href="<?php echoif($post->getUrl()); ?>"><?php echoif(Str::short($post->title,36)); ?>...</a></h4>
                                            <p><?php echoif(Str::short($post->short_desc,80)); ?>...</p>
                                        </div>
                                    </div>
                                </div>
                            <?php if($n==count($posts)-1){ ?></div><?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>