var current: int = 1;
var btn1_active: Boolean = true;
var btn2_active: Boolean = false;
var btn3_active: Boolean = false;

function goToSlide(slide: int): void {
    if (slide != current) {
        var ab: int = Math.abs(slide - current);
        if (slide > current) {
            if (ab == 2) {
                mc_slide.gotoAndPlay(120);
            } else if (slide == 2) {
                mc_slide.gotoAndPlay(2);
            } else {
                mc_slide.gotoAndPlay(30);
            }
        } else {
            if (ab == 2) {
                mc_slide.gotoAndPlay(150);
            } else if (slide == 2) {
                mc_slide.gotoAndPlay(60);
            } else {
                mc_slide.gotoAndPlay(90);
            }
        }
        current = slide;
    }
}


function upAll() {
    btn1_up();
    btn2_up();
    btn3_up();
}



btn1.addEventListener(MouseEvent.MOUSE_OVER, btn1_over);
btn1.addEventListener(MouseEvent.MOUSE_OUT, btn1_out);
btn1.addEventListener(MouseEvent.CLICK, btn1_click);

btn2.addEventListener(MouseEvent.MOUSE_OVER, btn2_over);
btn2.addEventListener(MouseEvent.MOUSE_OUT, btn2_out);
btn2.addEventListener(MouseEvent.CLICK, btn2_click);

btn3.addEventListener(MouseEvent.MOUSE_OVER, btn3_over);
btn3.addEventListener(MouseEvent.MOUSE_OUT, btn3_out);
btn3.addEventListener(MouseEvent.CLICK, btn3_click);

function btn1_over(event: MouseEvent): void {
    if (!btn1_active) { mc_btn1.gotoAndPlay(20); }
}

function btn1_out(event: MouseEvent): void {
    if (!btn1_active) { mc_btn1.gotoAndPlay(115); }
}

function btn1_click(event: MouseEvent): void {
    if (!btn1_active) {
        mc_btn1.gotoAndPlay(40);
        upAll();
        goToSlide(1);
        btn1_active = true;
    }
}

function btn1_up(): void {
    if (btn1_active) {
        mc_btn1.gotoAndPlay(56);
        btn1_active = false;
    }
}

function btn2_over(event: MouseEvent): void {
    if (!btn2_active) { mc_btn2.gotoAndPlay(20); }
}

function btn2_out(event: MouseEvent): void {
    if (!btn2_active) { mc_btn2.gotoAndPlay(115); }
}

function btn2_click(event: MouseEvent): void {
    if (!btn2_active) {
        mc_btn2.gotoAndPlay(40);
        upAll();
        goToSlide(2);
        btn2_active = true;
    }
}

function btn2_up(): void {
    if (btn2_active) {
        mc_btn2.gotoAndPlay(56);
        btn2_active = false;
    }
}

function btn3_over(event: MouseEvent): void {
    if (!btn3_active) { mc_btn3.gotoAndPlay(20); }
}

function btn3_out(event: MouseEvent): void {
    if (!btn3_active) { mc_btn3.gotoAndPlay(115); }
}

function btn3_click(event: MouseEvent): void {
    if (!btn3_active) {
        upAll();
        goToSlide(3);
        btn3_active = true;
        mc_btn3.gotoAndPlay(40);
    }
}

function btn3_up(): void {
    if (btn3_active) {
        mc_btn3.gotoAndPlay(56);
        btn1_active = false;
    }
}

mc_btn1.gotoAndPlay(40);