<?php try{ ?>            <div class="container brand-banner">
                <div class="row">
                    <div class="brand col-12 col-sm-5 col-lg-3">
                        <a href="<?php home_url(); ?>">
                            <img src="<?php public_url('images/logo.png'); ?>" alt="TheGioiVuong.com">
                        </a>
                    </div>
                    <div class="col-12 col-sm-7 col-lg-9 banner top-banner">
                        <div class="row">
                            <div class="col-8 col-sm-9">
                                <form method="GET" action="<?php echoif(url('search')); ?>">
                                    <div class="top-search header-search-form mbt-25">
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Tìm kiếm
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" data-type="all">Tất cả</a>
                                                    <!-- <a class="dropdown-item" href="#" data-type="posts">Bài viết</a>
                                                    <a class="dropdown-item" href="#" data-type="products">Sản phẩm</a> -->
                                                    
                                                </div>
                                            </div>
                                            <input type="text" class="form-control search-live live-search_focused" id="search-input" name="s" value="<?php echoif(htmlentities(_get('s'))); ?>" placeholder="Nhập từ khóa..." aria-label="Search for...">
                                            <span class="input-group-btn">
                                                <button class="btn btn-secondary" type="submit"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                        <div id="cube-live-search"></div>
                                    </div>
                                    
                                </form>
      
                                <!-- <div class="form-group has-feedback">
                                  <input type="text" class="form-control input-lg">
                                  
                                </div> -->
                            </div>
                            <div class="col-4 col-sm-3">
                                <div class="mbt-25">
                                    <?php $cart = get_controller('CartController'); ?>
                                    <a href="<?php home_url('cart'); ?>" class="btn btn-warning"><i class="fa fa-shopping-cart"></i> Giỏ hàng (<span class="cart-item-total"><?php echoif($cart->count()); ?></span>)</a>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php return true; }catch(CubeException $e){return $e;}catch(Exception $e){return $e;} ?>