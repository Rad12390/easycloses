var Sign = null;

// draw
var canvas, context, tool;

$(document).ready(function () {
    initApiDatas();
    Sign = new SignClass();
    Sign.init();
});

function SignClass() {
    var main = this;
    main.container = $(".page-template");

    // font
    main.font_index = 0;
    main.font_list = [
        'Romantically',
        'Sweet Cookie',
        'AnthoniSignature',
        'Shorelines Script Bold',
    ];

    main.is_draw_sign = false;

    main.init = function () {
        main.initData();
        main.initScreen();
        main.initEvent();
    }

    main.initData = function () {
        main.user = getSigneeInfo();

        main.calcInitials();
        _setSignee(main.user);

        main.initDrawObj();
    }

    main.initScreen = function () {
        main.changeFont();
        
        $("#signee_name").val(main.user.name);
        $("#signee_initials").val(main.user.initials);

        $(".signature-display").text(main.user.name);
        $(".initials-display").text(main.user.initials);

        $("#can-container").css('display', 'none');
    }

    main.initEvent = function () {
        // correct name
        $("#signee_name", main.container).keyup(function () {
            main.user.name = main.checkSign($(this).val());
            $(this).val(main.user.name);
            main.calcInitials();
            _setSignee(main.user);

            $("#signee_initials").val(main.user.initials);

            $(".signature-display").text(main.user.name);
            $(".initials-display").text(main.user.initials);
        });

        // sel font
        $(".sel-sign", main.container).click(function () {
            $(".signature-display-container").css('display', 'block');
            $("#can-container").css('display', 'none');
            main.is_draw_sign = false;
        });

        // draw sign
        $(".draw-sign", main.container).click(function () {
            $(".signature-display-container").css('display', 'none');
            $("#can-container").css('display', 'block');
            main.is_draw_sign = true;
        });

        // left font
        $(".sel-font.left", main.container).click(function () {
            main.font_index--;
            main.changeFont();
        });

        // right font
        $(".sel-font.right", main.container).click(function () {
            main.font_index++;
            main.changeFont();
        });

        // draw
        main.initDrawEvent();

        $('#btn_clear').click(function() {
            main.clearCanvas();
        });

        $('#btn_accept').click(function() {
            if (document.getElementById("chk-admit").checked) {

                // save font of signature
                var sign_doc = _getSignDocData();
                for (var signee of sign_doc.signee_list) {
                    if (main.user.id == signee.id && signee.sign_role_id != ROLE_ID_SENDER) {
                        signee.is_draw_sign = main.is_draw_sign;
                        if (main.is_draw_sign) {
                            signee.font_image = document.getElementById("can").toDataURL();
                        } else {
                            signee.font = main.font_list[main.font_index];
                        }
                        _setSignDocData(sign_doc);
                        break;
                    }
                }

                // save drawing of signature
                var canvas = document.getElementById("can");
                var data = canvas.toDataURL();

                _setSignImg(data);

                $("#input-tab.nav-link").click();
            } else {
                var $modal = $($('#modal-notification').html());
                var $content = '<p>Please agree to the consumer disclousre.</p>';
                Modal.initModal($modal, "Notification", $content, "Ok", function () {
                    $modal.modal('hide');
                    return false;
                });
                $modal.modal('show');
            }
        });

        $('#btn_decline').click(function() {
            var $modal = $($('#modal-small-center').html());
            var $content = '<p>Do you really want to decline?</p>';
            Modal.initModal($modal, "Notification", $content, "Ok", function () {
                $modal.modal('hide');
                window.location.href = $('#route-home').data('route');
                return false;
            });
            $modal.modal('show');
        });
    }

    main.calcInitials = function () {
        if (main.user == undefined || main.user == null || main.user == '') return;
        var s_arr = main.user.name.split(' ');
        main.user.initials = '';
        for (var i = 0; i < s_arr.length; i++) {
            if (s_arr[i].length > 0) {
                main.user.initials += s_arr[i][0] + '.';
            }
        }
    }

    main.changeFont = function () {
        if (main.font_index < 0) main.font_index = main.font_list.length - 1;
        main.font_index %= main.font_list.length;
        $(".signature-display").css('font-family', main.font_list[main.font_index]);
        $(".initials-display").css('font-family', main.font_list[main.font_index]);
    }

    main.checkSign = function (str) {
        // var chkRegex = /^[A-Z][^ ]* [A-Z][^ ]*$/;
        // if (chkRegex.exec(str))
        //     return true;
        // return false;
        var s_arr = str.split(' ');
        str = '';
        for (var i = 0; i < s_arr.length; i++) {
            s_arr[i] = s_arr[i].toLowerCase();
            str += s_arr[i].charAt(0).toUpperCase() + s_arr[i].slice(1);
            if (i < s_arr.length - 1) {
                str += ' ';
            }
        }
        return str;
    }

    // draw - init object
    main.initDrawObj = function () {
        // Find the canvas element.
        canvas = document.getElementById('can');

        // Get the 2D canvas context.
        context = canvas.getContext('2d');

        // Pencil tool instance.
        tool = new PencilClass();
    }

    // draw - init event
    main.initDrawEvent = function () {
        // Attach the mousedown, mousemove and mouseup event listeners.
        canvas.addEventListener('mousedown', main.ev_canvas, false);
        canvas.addEventListener('mousemove', main.ev_canvas, false);
        canvas.addEventListener('mouseup',   main.ev_canvas, false);
    }

    // The general-purpose event handler. This function just determines the mouse 
    // position relative to the canvas element.
    main.ev_canvas = function (ev) {
      if (ev.layerX || ev.layerX == 0) { // Firefox
        ev._x = ev.layerX;
        ev._y = ev.layerY;
      } else if (ev.offsetX || ev.offsetX == 0) { // Opera
        ev._x = ev.offsetX;
        ev._y = ev.offsetY;
      }
  
      // Call the event handler of the tool.
      var func = tool[ev.type];
      if (func) {
        func(ev);
      }
    }

    main.clearCanvas = function () {
        context.clearRect(0, 0, canvas.width, canvas.height);
    }
}

// This painting tool works like a drawing pencil which tracks the mouse 
// movements.
function PencilClass () {
    var tool = this;
    this.started = false;

    // This is called when you start holding down the mouse button.
    // This starts the pencil drawing.
    this.mousedown = function (ev) {
        context.beginPath();
        context.moveTo(ev._x, ev._y);
        tool.started = true;
    };

    // This function is called every time you move the mouse. Obviously, it only 
    // draws if the tool.started state is set to true (when you are holding down 
    // the mouse button).
    this.mousemove = function (ev) {
      if (tool.started) {
        context.lineTo(ev._x, ev._y);
        context.stroke();
      }
    };

    // This is called when you release the mouse button.
    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
      }
    };
}