<?php try{ ?>        <article class="cube-layout with-sidebar">
            <div class="container">
                <div class="row">
                    <section class="main-content col-12 col-sm-12 col-lg-8">
                        <?php $this->template("hotnews"); ?>
                        <!-- Categories posts -->
                        <?php if(is_array($home_cats) && count($home_cats) > 0) foreach($home_cats as $cat){ ?>
                            <?php $this->template('cat-'.$cat['type'],array('cat'=>$cat['cat'])); ?>
                        <?php } ?>
                    </section>
                    <!-- end content -->

                    <!-- sidebar -->
                    <?php $this->get_sidebar(); ?>
                    <!-- end sidebar -->
                </div>
            </div>
        </article>
        <!-- end layout --><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>