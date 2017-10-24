        <article class="cube-layout with-sidebar">
            <div class="container">
                <div class="row">
                    <section class="main-content col-12 col-sm-12 col-lg-8">
                        @template(hotnews);
                        <!-- Categories posts -->
                        @forif($home_cats as $cat)
                            @template('cat-'.$cat['type'],array('cat'=>$cat['cat']));
                        @end;
                    </section>
                    <!-- end content -->

                    <!-- sidebar -->
                    @get_sidebar();
                    <!-- end sidebar -->
                </div>
            </div>
        </article>
        <!-- end layout -->