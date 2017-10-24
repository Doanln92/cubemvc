@include(header);

    @include(sidebar);
    <main class="col-sm-9 ml-sm-auto col-md-10 pt-3" role="main">
        <h1>{{$pagetitle}}</h1>
        @view_content();
    </main> 

@include(footer);