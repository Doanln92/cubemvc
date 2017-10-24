@get_header(form);
<div class="cube-layout full-with layout-form container">
    <div class="cube-box view-post with-color red border-content">
        <div class="cube-box-header type-2">
            <h3>{{$formtitle}}</h3>
        </div>
        <div class="cube-box-content" style="padding:20px 10px;">
            @view_content();
        </div>
    </div>

</div>
@get_footer(form);