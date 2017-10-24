function cl(ob) {
    console.log(ob);
}
var sf_position = '0';
var sf_templates = "<a href=\"{search_url_escaped}\">View All Results<\/a>";
var sf_input = '#search-input';


function update_shopping_cart_item(total) {
    var t = parseInt(total);
    if (!isNaN(t)) {
        $('.cart-item-total').html(t);
    } else {
        $('.cart-item-total').html('0');
    }
}

function update_money_in_cart(money) {
    var total_money = money.toLocaleString('de-DE', { minimumFractionDigits: 2 });
    // In en-US, logs '100,000.00'
    // In de-DE, logs '100.000,00'
    // In hi-IN, logs '1,00,000.00'
    $('#munny-total').html(total_money);
}
var modal_confirm_callback = cl;

function modal_confirm(message, callback) {
    if (typeof(callback) == 'function') {
        modal_confirm_callback = callback;
    } else {
        modal_confirm_callback = cl;
    }
    $('#modal-confirm .modal-message').html(message);
    $('#modal-confirm').modal('show');
    $('#modal-confirm .btn-confirm-answer').click(function() {
        var $this = $(this);
        if ($this.hasClass('yes')) {
            modal_answer(true);
        } else {
            modal_answer(false);
        }
        $('#modal-confirm').modal('hide');
    });
}

function modal_answer(stt) {
    cl(stt);
    modal_confirm_callback(stt ? true : false);
}

function modal_alert(message) {
    $('#modal-alert .modal-message').html(message);
    $('#modal-alert').modal('show');

}
jQuery(function($) {
    $('#main-nav .btn-toggle').click(function() {
        $('#main-menu').toggleClass('active');
        return false;
    });
    if (typeof window.CubeSlider != 'undefined') {
        CubeSlider.add('#cube-hotnews-slider', 400, true, 4000);
    } else {
        window.CubeSliderInit = function() {
            CubeSlider.add('#cube-hotnews-slider', 400, true, 4000);
        };
    }

    $('.cube-tab-header .tab-buttons li a').click(function() {
        $(this).parent().parent().children().removeClass('active');
        $(this).parent().parent().parent().parent().find('.tabs .tab').removeClass('active');
        $($(this).attr('href')).addClass('active');
        $(this).parent().addClass('active');
        return false;
    });

    var navWrapper = $('.nav-wrapper');
    if (navWrapper.length > 0) {
        var navWrapperTopPosition = $(navWrapper).offset().top;

        $(window).on('scroll', function() {
            if ($(window).scrollTop() > navWrapperTopPosition + 10) {
                if (!$('.nav-wrapper').hasClass('active')) $('.nav-wrapper').addClass('active');
                $('.back-to-top').fadeIn();
            } else {
                $('.nav-wrapper').removeClass('active');
                $('.back-to-top').fadeOut();
            }
        });

    }
    $('.back-to-top a').click(function() {
        $('body,html').animate({ scrollTop: 0 }, 800);
        return false;
    });













    // shop

    $('.btn-add-to-cart').click(function() {
        var id = $(this).attr('data-product');
        $.ajax({
            url: home_url + '/cart/add/' + id,
            type: 'GET',
            cookie: true,
            success: function(res) {
                var count = null;
                try {
                    var rs = JSON.parse(res);
                    count = rs.count;
                    if (!rs.status) {
                        modal_alert("Bạn chỉ được thêm vào giỏ hàng 20 đơn vị sản phẩm trên mỗi mặt hàng");
                    } else {
                        update_shopping_cart_item(count);
                    }

                } catch (e) {

                }

            },
            error: function(e) {
                console.log(e);
            }
        });
    });
    $('.btn-remove-from-cart').click(function() {
        var id = $(this).attr('data-product');
        $.ajax({
            url: home_url + '/cart/remove/' + id,
            type: 'GET',
            cookie: true,
            success: function(res) {
                var count = null;
                try {
                    var rs = JSON.parse(res);
                    count = rs.count;
                    var money = rs.money;
                    if (!rs.status) {
                        modal_alert("Đã có lỗi bất ngờ xảy ra");
                    } else {
                        $('#product-info-' + rs.item).fadeOut(400, function() {
                            $(this).remove();
                        });
                        update_shopping_cart_item(count);
                        update_money_in_cart(money);
                        if (count == 0) {
                            setTimeout(function() {
                                location.reload();
                            }, 300);
                        }

                    }
                } catch (e) {
                    modal_alert("Đã có lỗi bất ngờ xảy ra");
                }
                //update_shopping_cart_item(count);
            },
            error: function(e) {
                console.log(e);
            }
        });
    });

    $('.btn-remove-all-cart').click(function() {
        $.ajax({
            url: home_url + '/cart/remove',
            type: 'GET',
            cookie: true,
            success: function(res) {
                var count = null;
                try {
                    var rs = JSON.parse(res);
                    count = rs.count;
                    var money = rs.money;
                    if (!rs.status) {
                        modal_alert("Đã có lỗi bất ngờ xảy");
                    } else {
                        $('#product-info-' + rs.item).fadeOut(400, function() {
                            $(this).remove();
                        });
                        update_shopping_cart_item(count);
                        update_money_in_cart(money);
                        top.location.reload();

                    }
                } catch (e) {
                    modal_alert("Đã có lỗi bất ngời xảy ra");
                }
                //update_shopping_cart_item(count);
            },
            error: function(e) {
                console.log(e);
            }
        });
    });


    $('.product-quantity').change(function() {
        var id = $(this).attr('data-product');
        var qtt = $(this).val();
        $.ajax({
            url: home_url + '/cart/update/' + id + '/' + qtt,
            type: 'GET',
            cookie: true,
            success: function(res) {
                var count = null;
                try {
                    var rs = JSON.parse(res);
                    count = rs.count;
                    var money = rs.money;
                    if (!rs.status) {
                        modal_alert("Thao tác không hợp lệ");
                    } else {
                        update_shopping_cart_item(count);
                        update_money_in_cart(money);

                    }
                } catch (e) {
                    modal_alert("Đã có lỗi bất ngờ xảy ra");
                }
                //update_shopping_cart_item(count);
            },
            error: function(e) {
                console.log(e);
            }
        });
    });


    $('.btn-order-cart').click(function() {
        modal_confirm("bạn có chắc muốn thanh toán đơn hàng này?", function(stt) {
            if (stt) {
                $('#order-cart').submit();
            }
        });
    });
    $('.btn-cart-detail').click(function() {
        $('.cart-detail.side').slideToggle(400);
        return false;
    });
    //$('#search-input').ajaxyLiveSearch();
    $(sf_input).ajaxyLiveSearch({
        "expand": false,
        "searchUrl": home_url + "search?s=%s",
        "text": "Nhập từ khóa...",
        "delay": 100,
        "iwidth": 180,
        "width": 315,
        "ajaxUrl": home_url + "live-search",
        "rtl": 0
    });
    $(".live-search_ajaxy-selective-input").keyup(function() {
        var width = $(this).val().length * 8;
        if (width < 50) {
            width = 50;
        }
        $(this).width(width);
    });
    $(".live-search_ajaxy-selective-search").click(function() {
        $(this).find(".live-search_ajaxy-selective-input").focus();
    });
    $(".live-search_ajaxy-selective-close").click(function() {
        $(this).parent().remove();
    });
});