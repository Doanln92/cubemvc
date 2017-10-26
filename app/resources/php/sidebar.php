<?php try{ ?><!-- sidebar -->
                    <aside class="sidebar col-12 col-sm-12 col-lg-4">
                        <div class="cube-box widget ads">
                            <div class="content">
                                <a href="#"><img src="<?php public_url('images/banner-1.gif'); ?>" alt="Quảng cáo"></a>
                            </div>
                        </div>
                        <?php if($posts = Models\Post::where('id>0')->orderBy('view','DESC')->limit(10)->get()){ ?>
                        <div class="cube-box with-color border-content">
                            <div class="cube-box-header">
                                <h3><a href="<?php echoif(url('popular')); ?>">Popular</a></h3>
                            </div>
                            <div class="cube-box-content">
                                <div class="post-list">
                                    <?php foreach($posts as $p){ ?>
                                    <div class="post-item row">
                                        <div class="post-thumb post-thumb-small col-12 col-sm-3 col-lg-3">
                                            <a href="<?php echoif($p->getUrl()); ?>"><img src="<?php echoif($p->getFeatureImage()); ?>" alt="<?php echoif($p->title); ?>"></a>
                                        </div>
                                        <div class="post-meta col-12 col-sm-9 col-lg-9">
                                            <h4 class="post-title"><a href="<?php echoif($p->getUrl()); ?>"><?php echoif($p->title); ?></a></h4>
                                            <p><?php echoif($p->short_desc); ?></p>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    

                                </div>
                            </div>

                        </div>
                        <?php } ?>
                        
                        <div class="cube-box widget ads">
                            <div class="content">
                                <a href="#"><img src="<?php public_url('images/banner-2.jpg'); ?>" alt="Quảng cáo"></a>
                            </div>
                        </div>

                    </aside>
                    <!-- end sidebar --><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>