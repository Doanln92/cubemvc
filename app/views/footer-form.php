
        @template(footer)
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
    @template(bootstrapjs)
    <script src="@public_url('/res/js/moment-with-locales.min.js')"></script>
    <script src="@public_url('/res/datetimepicker/js/datetimepicker.min.js')"></script>
    <script>
      var home_url='@home_url()';
      var manager_url='@home_url('manager')';
    </script>
    <script src="@public_url('js/cube.slider.js')"></script>
    <script src="@public_url('js/cube.js')"></script>

    <script src="@public_url('js/cube.form.js')"></script>

</body>

</html>