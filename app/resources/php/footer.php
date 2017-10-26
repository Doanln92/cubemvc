<?php try{ ?>
        <?php $this->template("footer"); ?>
        <div class="back-to-top">
            <a href="#">
                <div class="arrow-block">
                    <div class="arrow"></div>
                </div>
                <div class="text">Back to Top</div>
                <div class="cleafix"></div>
            </a>
        </div>
    </div>
    <!-- end cube-wrapper -->
    <?php $this->template("bootstrapjs"); ?>
    <script>var home_url='<?php home_url(); ?>';</script>
    <script src="<?php public_url('js/cube.slider.js'); ?>"></script>
    <script src="<?php public_url('js/cube.js'); ?>"></script>

</body>

</html><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>