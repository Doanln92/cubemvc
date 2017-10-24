@include(header);

    @include(sidebar);
    <main class="col-sm-9 ml-sm-auto col-md-10 pt-3" role="main">
    	<h2 style="margin-bottom: 20px;">@e(isset($formtitle)?$formtitle:null)</h2>
        @view_content();
    </main> 

@include(footer);