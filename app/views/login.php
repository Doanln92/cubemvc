@get_header('empty');
<div class="cube-wrapper">
    <style>
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #eee;
        }
    </style>
    <div class="container">

        <form class="form-signin" method="POST" action="{{get_current_url(true)}}">
            <h2 class="form-signin-heading">Login</h2>
            @if($message)
            <p class="alert alert-danger" role="alert">{{$message}}</p>
            @end
            <label for="inputEmail" class="sr-only">Email address</label>
            <input type="text" id="inputEmail" name="username" class="form-control" value="{{$username}}" placeholder="Email address or Username" required autofocus>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
            <div class="checkbox">
                <label>
                        <input type="checkbox" name="remember" value="remember-me" @if($remember) checked @end > Remember me
                      </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <br>
            <a href="{{$next}}">&lt; Back to Website</a> | <a href="{{url('forgot')}}"> Quên mật khẩu</a>
        </form>
    </div>
    <!-- /container -->
</div>
@get_footer('empty');