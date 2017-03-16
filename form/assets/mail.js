(function (global, factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {/* Use AMD */
        define(SHForm());
    } else if (typeof module !== 'undefined' && module.exports) {/* Use CommonJS */
        module.exports = factory();
    } else {/* Use Browser */
        global.SHForm = factory();
    }
})(typeof window !== 'undefined' ? window : this, function () {
    var SHForm = function (_form) {
        var formCallback = function ($form, $option) {
            if ($form == undefined || $form.length <= 0) return false;

            var option = {
                'successCallback': function () {

                },
                'errorCallback': function () {

                }
            };

            option = $.extend({}, option, $option)

            $form.submit(function(e) {
                var action = $form.attr('action'),
                    url = (action != undefined && action.length > 0) ? action : '/mail.php',
                    infoBlock = $form.find('.info-form');

                if (infoBlock.length > 0) {
                    infoBlock.hide();
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    error: function () {
                        alert('Form error');
                    },
                    success: function(data) {
                        var infoReed = function (array) {
                                infoBlock.html('');

                                $.each(array, function (key, val) {
                                    var block = $('<p/>');

                                    block.text(val);

                                    infoBlock.append(block);
                                });

                                infoBlock.show();
                            },
                            infoResponse = false;

                        if ($(data.error).length > 0 || $(data.success).length > 0) {
                            infoResponse = true;

                            if (infoResponse && (infoBlock == undefined || infoBlock.length <= 0)) {
                                infoBlock = $('<div class="info-form"/>');

                                $form.prepend(infoBlock);
                            }

                            if ($(data.error).length > 0) {
                                infoReed(data.error);
                                $form.addClass('error-form').removeClass('success-form');
                                option.errorCallback($form);
                            }

                            if ($(data.success).length > 0) {
                                infoReed(data.success);
                                $form.addClass('success-form').removeClass('error-form');
                                option.successCallback($form);
                            }
                        }
                    }
                });

                e.preventDefault();
            });

            return true;
        };

        $(_form).each(function () {
            var form = $(this);


            formCallback(form, {
                'successCallback': function (form) {
                    form.find('.info-form').removeClass('alert-danger').addClass('alert-success');
                },
                'errorCallback': function (form) {
                    form.find('.info-form').removeClass('alert-success').addClass('alert-danger');
                }
            });
        });
    };

    return SHForm;
});

$(document).ready(function () {
    SHForm('form.sendForm');
})