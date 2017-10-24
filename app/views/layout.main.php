@get_header();
        <article class="cube-layout with-sidebar">
            <div class="container">
                <div class="row">
                    <section class="main-content col-12 col-sm-12 col-lg-8">
                        @view_content();
                    </section>

                    <!-- side bar -->
                    @get_sidebar();
                </div>
            </div>
        </article>
@get_footer();