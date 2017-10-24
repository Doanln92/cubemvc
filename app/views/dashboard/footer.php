        </div>
    </div>
        
    <!-- Bootstrap core JavaScript
            ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>

    <script>
        window.jQuery || document.write('<script src="{{public_url("js/jquery-3.2.1.min.js")}}"><\/script>');

        var dashboard_url = "{{url('dashboard')}}";
    </script>
    <script src="@public_url('res/bootstrap/assets/js/vendor/popper.min.js')"></script>
    <script src="@public_url('res/bootstrap/js/bootstrap.min.js')"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="@public_url('res/bootstrap/assets/js/ie10-viewport-bug-workaround.js')"></script>
    <script src="@public_url('/res/js/moment-with-locales.min.js')"></script>
    <script src="@public_url('/res/datetimepicker/js/datetimepicker.min.js')"></script>
    <script type="text/javascript" src="@public_url('/res/tinymce/tinymce.min.js')"></script>
    <script src="@public_url('js/cube.admin.js')"></script>

</body>

</html>