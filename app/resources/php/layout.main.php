<?php try{ ?><?php $this->get_header(); ?>
        <article class="cube-layout with-sidebar">
            <div class="container">
                <div class="row">
                    <section class="main-content col-12 col-sm-12 col-lg-8">
                        <?php $this->getViewContent(); ?>
                    </section>

                    <!-- side bar -->
                    <?php $this->get_sidebar(); ?>
                </div>
            </div>
        </article>
<?php $this->get_footer(); ?><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>