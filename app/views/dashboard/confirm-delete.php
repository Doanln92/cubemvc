
@layout(main);
        <h1>Xác nhận yêu cầu</h1>
        
        <p class="alert alert-warning" role="alert">{{$message}}</p>
        <form method="POST">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Xóa</button>
                <a href="javascript:history.back(1);" class="btn btn-light cancel">Hủy</a>
                
                </div>
            </div>
            
        </form>
