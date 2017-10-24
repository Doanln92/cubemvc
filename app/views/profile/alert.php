@layout(main)
@assign('pagetitle','Thông báo');
        
        <p class="alert alert-{{$alert_type}}" role="alert">{{$message}}</p>
 