<?php try{ ?><div class="modal fade" id="modal-alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Thông báo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-message">Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog" aria-labelledby="ConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ConfirmModalLabel">Xác nhận</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-message">Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm-answer yes">Có</button>
                <button type="button" class="btn btn-secondary btn-confirm-answer no">Không</button>
                
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap core JavaScript
            ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    
        <script>
            window.jQuery || document.write('<script src="<?php public_url("res/js/jquery-3.2.1.min.js"); ?>"><\/script>')
        </script>
        <script src="<?php public_url('res/bootstrap/assets/js/vendor/popper.min.js'); ?>"></script>
        <script src="<?php public_url('res/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="<?php public_url('res/bootstrap/assets/js/ie10-viewport-bug-workaround.js'); ?>"></script>
        <script src="<?php public_url('js/livesearch.js'); ?>"></script>
<?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>