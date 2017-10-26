<?php try{ ?><?php $this->get_header(); ?>
<div class="cube-layout full-with">
    <?php $this->getViewContent(); ?>
</div>
<?php $this->get_footer(); ?><?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>