<?php try{ ?><?php $this->layout("main"); ?>

<div class="super-box with-color green border-content">
    <div class="super-box-header">
        <h2><a href="<?php echoif(url('listnews',$category->slug)); ?>"><?php echoif($category->name); ?></a></h2>
    </div>
    <div class="super-box-content">
        <div class="post-list">
            <?php if(is_array($posts) && count($posts) > 0) foreach($posts as $p){ ?>

            <div class="post-item row">
                <div class="post-thumb col-12 col-sm-5 col-lg-4">
                    <a href="<?php echoif($p->getUrl()); ?>"><img src="<?php echoif($p->getFeatureImage(320,200)); ?>" alt="<?php echoif($p->title); ?>"></a>
                </div>
                <div class="post-detail col-12 col-sm-7 col-lg-8">
                    <h4 class="post-title"><a href="<?php echoif($p->getUrl()); ?>"><?php echoif($p->title); ?></a></h4>
                    <p class="post-meta">
                        <!-- <span class="meta post-author">
                            <span class="glyphicon glyphicon-user"></span> <span>Author</span>
                        </span> -->
                        <span class="meta post-category">
                            <a href="<?php echoif($p->getCategory()->getUrl()); ?>"><i class="fa fa-folder" aria-hidden="true"></i> <span><?php echoif($p->getCategory()->name); ?></span></a>
                        </span>
                        <span class="meta post-time">
                        <i class="fa fa-clock-o" aria-hidden="true"></i> <time datetime="<?php echoif($p->post_time); ?>"><?php echoif(date('d/m/Y',strtotime($p->post_time))); ?></time>
                        </span>
                        <!-- <span class="meta post-view">
                            <span class="glyphicon glyphicon-eye-open"></span> <span>100 lượt xem</span>
                        </span> -->
                    </p>
                    <p class="post-desc">
                        <?php echoif($p->short_desc); ?>
                    </p>
                    <a href="<?php echoif($p->getUrl()); ?>" class="btn btn-primary">Đọc tiếp</a>

                </div>
            </div>
            <?php } else { ?>
                <p class="alert alert-warning">Không có tin bài nào</p>
            <?php } ?>
            
        </div>
    </div>
</div>
<?php echoif($paging->getPagination(get_current_url(true),'page',array('class'=>'pagination'))); ?><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>