(function ($) {
    if (!$) {
        console.error('jQuery and $ are missing');
        return;
    }

    /**
     * Form water testing Actions Class.
     */
    function SpaActions() {
        this.$body = $('body');

        this.events_init();
    }

    /**
     * Events init.
     */
    SpaActions.prototype.events_init = function() {
        let _this = this;

        // Print
        this.$body.on('click', '.wt-result-action--print', function(e) {
            window.print();
        });

        // New test
        this.$body.on('click', '.wt-result-action--new', function(e) {
            Cookies.remove('TheSpaShopPE.data_map', { path: '/' });
            document.location.href = $(this).data('href');
        });

        // Save with login
        this.$body.on('click', '.wt-result-action--save.wt-result-action--log-in', function(e) {
            // history.pushState(null, null, '.?js_id=' + thespashoppe.get_id());
        });

        // Save
        this.$body.on('click', '.wt-result-action--save:not(.wt-result-action--log-in)', function(e) {
            history.pushState(null, null, '.?js_id=' + thespashoppe.get_id());
            Cookies.remove('TheSpaShopPE.save', { path: '/' });
            _this.save_form();
        });

        // Log in
        this.$body.on('click', '.wt-result-action--log-in', function(e) {

            // Set cookie to save after login
            Cookies.set('TheSpaShopPE.save', '1', { expires: 7, path: '/' });
        });

        // Remove
        this.$body.on('click', '.wt-previous__remove', function(e) {
            let $this = $(this);

            if (confirm('Are you sure you want to delete it?')) {
                _this.remove_test($this);
            }
        });

        // Email Results button
        this.$body.on('click', '.wt-result-action--to-email', function(e) {
            theSpaModals.open_modal('to-email');
        });

        // Get Help button
        this.$body.on('click', '.wt-result-action--get-help', function(e) {
            theSpaModals.open_modal('get-help');
        });

        // Submit Form
        this.$body.on('submit', '.wt-modal__form', function(e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            _this.submit_form($(this));

            return false;
        });
    };

    /**
     * Submit Form.
     *
     * @param  $this
     */
    SpaActions.prototype.submit_form = function($this) {
        let _this = this;
        let $email = $('[name="email"]', $this);
        let $message = $('[name="message"]', $this);
        let data = {
            'action' : 'thespa_' + $this.data('action'),
            'data' : thespashoppe.get_data(),
            'email' : $email.val(),
            'results' : $('.wt-result-boxes').html(),
            'regular_results' : $('.wt-info-boxes').html(),
            'nonce' : theSpaShoppeSettings.nonce,
        };

        if ($message.length) {
            data.message = $message.val();
        }

        $.ajax({
            type: 'POST',
            url: theSpaShoppeSettings.url,
            dataType: 'json',
            async: true,
            data: data,
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                theSpaModals.loader_start();
            },
            success: function (data) {
                try {
                    console.log(data);
                    if(data.message !== void 0) {
                        theSpaModals.complete(data.message);
                    }

                    if(data.save !== void 0) {
                        _this.on_save_result(data.save);
                        history.pushState(null, null, '.?js_id=' + thespashoppe.get_id());
                    }

                } catch (err) {
                    thespashoppe.error(err);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                theSpaModals.loader_stop();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.error('THESPA_Requests-@11: '+xhr.status);
                console.error('THESPA_Requests-@12: '+thrownError);
                thespashoppe.error(thrownError + ', status: ' + xhr.status);
            }
        });
    };

    /**
     * Remove test.
     */
    SpaActions.prototype.remove_test = function($this) {
        let this_id = $this.data('js-id');

        $.ajax({
            type: 'POST',
            url: theSpaShoppeSettings.url,
            dataType: 'json',
            async: true,
            data: {
                'action' : 'thespa_remove_test',
                'js_id' : this_id,
                'nonce' : theSpaShoppeSettings.nonce,
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                thespashoppe.loader_start();
            },
            success: function (data) {
                try {
                    console.log(data);
                    if (data.success !== void 0 && data.success === 'remove 1') {
                        let $box = $('.wt-previous--'+this_id);

                        $box.hide();
                    } else {
                        thespashoppe.error('data empty');
                    }

                } catch (err) {
                    thespashoppe.error(err);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                thespashoppe.loader_stop();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.error('THESPA_Requests-@11: '+xhr.status);
                console.error('THESPA_Requests-@12: '+thrownError);
                thespashoppe.error(thrownError + ', status: ' + xhr.status);
            }
        });
    };

    /**
     * Save form.
     */
    SpaActions.prototype.save_form = function() {
        let _this = this;

        $.ajax({
            type: 'POST',
            url: theSpaShoppeSettings.url,
            dataType: 'json',
            async: true,
            data: {
                'action' : 'thespa_save',
                'data' : thespashoppe.get_data(),
                'id' : thespashoppe.get_id(),
                'nonce' : theSpaShoppeSettings.nonce,
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                thespashoppe.loader_start();
            },
            success: function (data) {
                console.log(data);
                _this.on_save_result(data);
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                thespashoppe.loader_stop();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.error('THESPA_Requests-@11: '+xhr.status);
                console.error('THESPA_Requests-@12: '+thrownError);
                thespashoppe.error(thrownError + ', status: ' + xhr.status);
            }
        });
    };

    /**
     * On save result.
     *
     * @param  data
     */
    SpaActions.prototype.on_save_result = function(data) {
        try {
            if (data.html !== void 0 && data.html) {
                let $wt_previous = $('.wt-previous');
                let template = new SpaTemplate();

                $wt_previous.html(data.html);
                template.utc_time_to_local_with_masc();
                thespashoppe.onbeforeunload(JSON.stringify(thespashoppe.data_map));
            } else {
                thespashoppe.error('data empty');
            }

        } catch (err) {
            thespashoppe.error(err);
        }
    };

    // Init.
    $(function () {
        window.SpaActions = SpaActions;
        window.thespaactions = new SpaActions();
    });
}($ || window.jQuery));