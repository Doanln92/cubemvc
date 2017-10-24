/* Le Ngoc Doan */
var CubeSlider = {
    slider: function slider(slider_name, selector, transition_time, auto_next, time_step) {
        this.name = slider_name;
        this.selector = selector;
        this.transitionTime = transition_time;
        this.autoNext = auto_next;
        this.timeStep = time_step;
        this.node = null;
        this.slides = [];
        this.lastSlideIndex = 0;
        this.total = 0;
        this.current = 0;
        this.isHover = false;
        this.isMove = false;
        this.timeOutID = null;
        this.goTo = function(index) {
            if (this.isMove) return;
            this.isMove = true;
            if (index == this.current) return;
            this.current = index;
            var smargin = "-" + (index * 100) + "%";
            var a = "$(this.node).find('.slides').animate({'margin-left':smargin}, this.transitionTime,function(){" +
                this.name + ".isMove = false;" +
                "});";
            eval(a);
            $(this.node).find('.dot').removeClass('active');
            $($(this.node).find('.dot')[index]).addClass('active');
        };
        this.preview = function() {
            if (this.current == 0) {
                var fs = $(this.node).find('.slides .slide')[0];
                $(this.node).find('.slides').css({
                    'margin-left': '-' + (this.total * 100) + '%'
                });
                $(fs).css({
                    'left': (this.lastSlideIndex + 1) * (100 / (this.total + 1)) + '%'
                });
                eval("$(this.node).find('.slides').animate({'margin-left':(-this.lastSlideIndex*100)+'%'},this.transitionTime,function(){" +
                    "$($(" + this.name + ".node).find('.slides .slide')[0]).css({left:0});" +
                    this.name + ".current=" + this.lastSlideIndex + ";" +
                    "$(" + this.name + ".node).find('.dot').removeClass('active');" +
                    "$($(" + this.name + ".node).find('.dot')[" + this.name + ".current]).addClass('active');" +
                    this.name + ".isMove = false;" +
                    "});");
            } else {
                this.goTo((this.current + this.slides.length - 1) % this.slides.length);
            }
        };

        this.next = function() {
            if (this.current == this.lastSlideIndex) {
                if (this.isMove) return;
                this.isMove = true;
                var fs = $(this.node).find('.slides .slide')[0];
                $(fs).css({
                    'left': (this.lastSlideIndex + 1) * (100 / (this.total + 1)) + '%'
                });
                var a = "$(this.node).find('.slides').animate({'margin-left':(-(this.lastSlideIndex+1)*100)+'%'},this.transitionTime,function(){" +
                    "$($(" + this.name + ".node).find('.slides .slide')[0]).css({left:0});" +
                    "$(" + this.name + ".node).find('.slides').css({'margin-left':0});" +
                    this.name + ".current=0;" +
                    "$(" + this.name + ".node).find('.dot').removeClass('active');" +
                    "$($(" + this.name + ".node).find('.dot')[" + this.name + ".current]).addClass('active');" +
                    this.name + ".isMove = false;" +
                    "});";
                eval(a);
            } else {
                this.goTo((this.current + 1) % this.slides.length);
            }
        };

        this.autoNextSlide = function() {
            if (!this.isHover) {
                this.next();
            }
            this.timeOutID = setTimeout(this.name + ".autoNextSlide();", this.timeStep);
        };
        this.stop = function() {
            clearTimeout(this.timeOutID);
        }
        this.start = function() {
            if (this.selector.constructor == HTMLElement) {
                this.node = this.selector;
            } else {
                this.node = $(this.selector)[0];
            }

            this.slides = $(this.node).find(".slide");
            this.total = this.slides.length;
            this.lastSlideIndex = this.total - 1;
            $(this.node).find(".slides").css({
                width: (this.slides.length + 1) * 100 + "%"
            });
            $(this.node).find(".slide").css({
                width: 100 / (this.slides.length + 1) + "%"
            });
            $(this.node).find(".slides .slide").css({
                position: 'relative'
            });
            var a = "" +
                "$(this.node).find('.slides').hover(function(){" + this.name + ".isHover = true;},function(){" + this.name + ".isHover = false;});" +
                "$(this.node).find('.button-preview').click(function(){" + this.name + ".preview();return false;});" +
                "$(this.node).find('.button-next').click(function(){" + this.name + ".next();return false;});" +
                "$(this.node).find('.dot').click(function(){var num = $(this).attr('data-num')-1;" + this.name + ".goTo(num);return false;});";
            eval(a);
            if (this.autoNext) {
                setTimeout(this.name + ".autoNextSlide();", this.timeStep);
            }
        }
    },
    slideObj: [],
    transition_time: 400,
    auto_next: false,
    time_step: 4000,
    add: function(selector, transition_time, auto_next, time_step) {
        console.log('CubeSlider.add was called');
        var t, a, s;
        if (typeof transition_time == 'undefined') {
            t = this.transition_time;
        } else {
            t = transition_time;
        }
        if (typeof auto_next == 'undefined') {
            a = this.auto_next;
        } else {
            a = auto_next;
        }
        if (typeof time_step == 'undefined') {
            s = this.time_step;
        } else {
            s = time_step;
        }
        var elms = $(selector);
        for (var i = 0; i < elms.length; i++) {
            var stt = true;
            var el = elms[i];
            for (var j = 0; j < this.slideObj.length; j++) {
                if (el == this.slideObj[j]) {
                    stt = false;
                }
            }
            if (stt) {

                var sl = this.slideObj.length;
                this.slideObj[sl] = new this.slider("CubeSlider.slideObj[" + sl + "]", el, t, a, s);
                this.slideObj[sl].start();
                $(el).fadeIn(300);
            }
        }
    }
};

if(typeof window.CubeSliderInit != 'undefined'){
    window.CubeSliderInit();
}