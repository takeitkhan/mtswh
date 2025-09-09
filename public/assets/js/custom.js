$(document).ready(function() {

    // menu item
    $('.menu-item').on('mouseover', function() {
        $(this).addClass('active').removeClass('active');
    });
    // $(".navbar-brand").click(function() {
    //     $("#toggleClass").toggle();
    // });
    $(".navbar-brand").click(function(e) {
        $("#toggleClass").toggle();
        e.stopPropagation();
    });
    $(document).click(function(e) {
        if (!$(e.target).is('#toggleClass, #toggleClass *')) {
            $("#toggleClass").hide();
        }
    });
});

$(function() {
    // date picker
    $("#datepicker").datepicker({
        dateFormat: 'dd-mm-yy',
        theme: 'Bootstrap-4'
    });


    // select 2
    $('.select-box').select2();

});

//Select 2
function select2Refresh() {
    $('select.select-box').select2({});
}


/* Set the width of the side navigation to 250px */
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("mySidenav").style.display = "block";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("mySidenav").style.display = "none";

}

/* Check All Input checknox */
$("#checkAll").click(function() {
    $('input.checkItem:checkbox').not(this).prop('checked', this.checked);
});

window.onload = function() {
    var hidediv = document.getElementById('mySidenav');
    var hidenav = document.getElementById('navbarSupportedContent');
    document.onclick = function(div) {
        if (!div.target.closest('#mySidenav') && hidediv.style.display != "none" && !div.target.closest('.sidebar_open')) {
            hidediv.style.display = "none"
        }
        if (!div.target.closest('#navbarSupportedContent') && hidenav.style.display != "none" && !div.target.closest('.navbar-toggler')) {
            //bootstrap.Collapse.getInstance(hidenav).hide();
        }

    }
}

// toggle full screen

/** Confirm Box */

    (function (root, factory) {
        if (typeof define === 'function' && define.amd) {
            define([], factory);
        } else if (typeof module === 'object' && module.exports) {
            module.exports = factory();
        } else {
            var expData = factory();
            for(var key in expData) {
                if (expData.hasOwnProperty(key)) {
                    root[key] = expData[key];
                }
            }
        }
    }(this, function(){
        function ce(tag, clas, txt) {
            var ele = document.createElement(tag);
            ele.setAttribute('class', clas);
            if(typeof txt === 'undefined' || txt === null){
                return ele;
            }
            var tn = document.createTextNode(txt);
            ele.appendChild(tn);
            return ele;
        }
        var KEY_ESC = 27;
        var KEY_ENTER = 13;

        function buildUI(title, sub, onOk, onCancel, type) {
            if (typeof window === 'undefined') {
                throw 'Cannot use this in node.';
            }
            var prev = document.getElementsByClassName('msc-confirm');
            if(prev.length > 0){
                document.body.removeChild(prev[0]);
            }

            var options = {
                title: 'Confirm',
                subtitle: '',
                onOk: null,
                onCancel: null,
                okText: 'OK',
                cancelText: 'Cancel',
                placeholder: 'Enter value',
                dismissOverlay: false,
                closeOnEscape: true,
                defaultValue: ''
            };

            if(typeof title === 'object') {
                for(var key in title) {
                    options[key] = title[key];
                }
                if(typeof options.onOk !== 'function') {
                    options.onOk = null;
                }
                if(typeof options.onCancel !== 'function') {
                    options.onCancel = null;
                }
            } else {
                options.title = (typeof title === 'string') ? title : options.title;
                options.subtitle = (typeof sub === 'string') ? sub : options.subtitle;
                options.onOk = (typeof onOk === 'function') ? onOk : options.onOk;
                options.onCancel = (typeof onCancel === 'function') ? onCancel : options.onCancel;

                if(typeof sub === 'function') {
                    options.onOk = sub;
                }
            }

            var dialog = ce('div', 'msc-confirm'),
                overlay = ce('div', 'msc-overlay'),
                closeBtn = ce('button', 'msc-close');
            closeBtn.innerHTML = '&times;';
            overlay.appendChild(closeBtn);

            if(options.dismissOverlay) {
                overlay.addEventListener("click", destroy);
            }

            closeBtn.addEventListener('click', destroy);

            var content = ce('div', 'msc-content'),
                cTitle = ce('h3', 'msc-title', options.title),
                body = ce('div', 'msc-body'),
                action = ce('div', 'msc-action'),
                okBtn = ce('button', 'msc-ok', options.okText),
                cancelbtn = ce('button', 'msc-cancel', options.cancelText),
                input = ce('input', 'msc-input');

            body.appendChild(ce('p','msc-sub', options.subtitle));

            action.appendChild(okBtn);
            if(type !== "alert") {
                action.appendChild(cancelbtn);
                cancelbtn.addEventListener('click', cancel);
            }

            okBtn.addEventListener('click', ok);

            content.appendChild(cTitle);
            content.appendChild(body);
            content.appendChild(action);

            dialog.appendChild(overlay);
            dialog.appendChild(content);
            document.body.appendChild(dialog);
            dialog.style.display = 'block';
            content.classList.add('msc-confirm--animate');
            if(type === "prompt") {
                input.setAttribute("type", "text");
                input.setAttribute("placeholder", options.placeholder);
                input.value = options.defaultValue;
                input.addEventListener("keyup", function(e) {
                    if(e.keyCode === KEY_ENTER) {
                        ok();
                    }
                });
                body.appendChild(input);
                input.focus();
            }else if(type==="alert") {
                okBtn.focus();
            }else {
                cancelbtn.focus();
            }

            document.addEventListener('keyup', _hide);

            function destroy() {
                closeBtn.removeEventListener('click', destroy);
                okBtn.removeEventListener('click', ok);
                cancelbtn.removeEventListener('click', cancel);
                if(options.dismissOverlay) {
                    overlay.removeEventListener("click", destroy);
                }
                document.removeEventListener('keyup', _hide);
                document.body.removeChild(dialog);
            }

            function ok() {
                destroy();
                if(options.onOk !== null) {
                    if(type === "prompt") {
                        options.onOk(input.value);
                    }else {
                        options.onOk();
                    }
                }
            }

            function cancel() {
                destroy();
                if(options.onCancel !== null) {
                    options.onCancel();
                }
            }

            function _hide(e) {
                if(options.closeOnEscape && e.keyCode == KEY_ESC) {
                    destroy();
                }
            }
        };
        var exportData = {
            mscConfirm: function(title, sub, onOk, onCancel) {
                buildUI(title, sub, onOk, onCancel, "confirm");
            },
            mscPrompt: function(title, sub, onOk, onCancel) {
                buildUI(title, sub, onOk, onCancel, "prompt");
            },
            mscAlert: function(title, sub, onOk, onCancel) {
                buildUI(title, sub, onOk, onCancel, "alert");
            },
            mscClose: function() {
                var prev = document.getElementsByClassName('msc-confirm');
                if(prev.length > 0){
                    document.body.removeChild(prev[0]);
                }
            }
        };
        return exportData;
    }));

function confirmAlert(title, subtitle = '', formId = 'form'){
	if(title){
      	title = title;
    }else {
    	title = 'Are you sure'
    }
    return mscConfirm({
          title: title,
          subtitle: subtitle,
          onOk: function() {
            $(formId).submit();
          },
          onCancel: null,
          okText: 'OK',
          cancelText: 'Cancel',
          placeholder: 'Enter value',
          dismissOverlay: false,
          closeOnEscape: true,
          defaultValue: ''
      })

}


function confirmAlertCustom(title, subtitle = '', link ='#', okBtnText = 'Ok'){
	if(title){
      	title = title;
    }else {
    	title = 'Are you sure'
    }
    return mscConfirm({
          title: title,
          subtitle: subtitle,
          onOk: function() {
            window.location = link;
          },
          onCancel: null,
          okText: okBtnText,
          cancelText: 'Cancel',
          placeholder: 'Enter value',
          dismissOverlay: false,
          closeOnEscape: true,
          defaultValue: ''
      })

}

function confirmAlertBeforeSubmit(formId = null){
    let title = 'Are you sure to Delete';
    //console.log(formId);
    return mscConfirm({
          title: '',
          subtitle: null,
          onOk: function(){
              //$('#'+formId+' form').submit();
              $('form#'+formId).submit();
          },
          onCancel: null,
          okText: 'Confirm',
          cancelText: 'Cancel',
          placeholder: 'Enter value',
          dismissOverlay: false,
          closeOnEscape: true,
          defaultValue: ''
      })
}

function DeleteconfirmAlertCustom(formId = null){
	let title = 'Are you sure to Delete';
    //console.log(formId);
    return mscConfirm({
          title: title,
          subtitle: null,
          onOk: function(){
              //$('#'+formId+' form').submit();
              $('form#'+formId).submit();
          },
          onCancel: null,
          okText: 'Confirm',
          cancelText: 'Cancel',
          placeholder: 'Enter value',
          dismissOverlay: false,
          closeOnEscape: true,
          defaultValue: ''
      })

}


/** Custom Alert Box */

var ALERT_TITLE = "Warning!";
var ALERT_BUTTON_TEXT = "Ok";

if(document.getElementById) {
	window.alert = function(txt) {
		createCustomAlert(txt);
	}
}

function createCustomAlert(txt) {
	d = document;

	if(d.getElementById("modalContainer")) return;

	mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
	mObj.id = "modalContainer";
	mObj.style.height = d.documentElement.scrollHeight + "px";

	alertObj = mObj.appendChild(d.createElement("div"));
	alertObj.id = "alertBox";
	if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
	alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";
	alertObj.style.visiblity="visible";

	h1 = alertObj.appendChild(d.createElement("h1"));
	h1.appendChild(d.createTextNode(ALERT_TITLE));

	msg = alertObj.appendChild(d.createElement("p"));
	//msg.appendChild(d.createTextNode(txt));
	msg.innerHTML = txt;

	btn = alertObj.appendChild(d.createElement("a"));
	btn.id = "closeBtn";
	btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
	btn.href = "#";
	btn.focus();
	btn.onclick = function() { removeCustomAlert();return false; }

	alertObj.style.display = "block";

}

function removeCustomAlert() {
	document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
}

function ful(){
    alert('Alert this pages');
}

/** Header Notification */
$(document).ready(function () {
    $("#notificationLink").click(function () {
        $("#notificationContainer").fadeToggle(300);
        //$("#notification_count").fadeOut("slow");
        return false;
    });

    //Document Click hiding the popup
    $(document).click(function () {
        $("#notificationContainer").hide();
    });

    //Popup on click
    $("#notificationLink").click(function () {
        return false;
    });

});


/**
 * Browser Full screen
 */

var elem = document.documentElement;
$('#btnFullScreen').click(function(){
    let thisData = $("#btnFullScreen").attr('data-full_screen');
    if(thisData == 'exit'){
        $(this).attr('data-full_screen', 'fullscreen')
        openFullscreen();
    }
    if(thisData == 'fullscreen'){
        $(this).attr('data-full_screen', 'exit')
        closeFullscreen()
    }
})

function openFullscreen() {
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) { /* Safari */
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { /* IE11 */
        elem.msRequestFullscreen();
    }
}

function closeFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) { /* Safari */
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE11 */
        document.msExitFullscreen();
    }
}


