(function ($) {
    if (!$) {
        console.error('jQuery and $ are missing');
        return;
    }

    /**
     * Form water testing.
     */
    function TheSpaShopPE() {
        let _this = this;
        this.$body = $('body');
        this.template = new SpaTemplate();
        this.data_form = TheSpaShopPE.prototype.initial_data($('#water-testing-data'));
        this.products = TheSpaShopPE.prototype.initial_data($('#water-testing-products'));
        this.init = TheSpaShopPE.prototype.initial_data($('#water-testing-init'));
        this.current_products = [];
        this.current_extra_products = [];
        this.filter = [];
        this.devises = TheSpaShopPE.prototype.initial_data($('#water-testing-devises'));
        this.data_map = {'id':null, 'devises':null, 'type':null, 'volume':null, 'chemical':null, 'test':{}};
        this.is_change_data_map = true;

        console.log(this.data_form, this.products); // TODO REMOVE THIS

        this.loader_start();

        // Initial form
        this.initial_form();
        this.events_init();
        this.load_init_data();
        this.load();
        this.set_id();

        setTimeout(function () {
            _this.auto_save();
        }, 50);

        this.loader_stop();
    }

    /**
     * Setup form.
     */
    TheSpaShopPE.prototype.initial_form = function() {
        let $selects = $('.water-testing__select');

        if ($selects.length) {
            $selects.each(function () {
                let $this = $(this);
                let opt = {
                    placeholder: $this.attr('title'),
                    allowClear: true,
                };

                $this.select2(opt);
                $this.val(null).trigger('change');
            });
        }
    };

    /**
     * Close window alert.
     *
     * @param  init
     * @return void
     */
    TheSpaShopPE.prototype.onbeforeunload = function(init) {
        let _this = this;
        this.preinit = init;

        window.onbeforeunload = function () {
            if (_this.is_change()) {
                return "The changed data is not saved. Close the page?";
            }
            return null;
        };
    };

    /**
     * Any changes with server init?
     *
     * @return boolean
     */
    TheSpaShopPE.prototype.is_change = function() {
        return (this.preinit !== void 0 && this.preinit !== JSON.stringify(this.data_map));
    };

    /**
     * Setup form data.
     *
     * @param  $elem
     * @return json
     */
    TheSpaShopPE.prototype.initial_data = function($elem) {
        return this.to_json($elem.text());
    };

    /**
     * Setup form data.
     *
     * @param  data
     * @return json
     */
    TheSpaShopPE.prototype.to_json = function(data) {
        if (data !== void 0 && data.length) {
            try {
                return JSON.parse(data);
            } catch (err) {
                TheSpaShopPE.prototype.error(err);
            }
        }
    };

    /**
     * Events init.
     */
    TheSpaShopPE.prototype.events_init = function() {
        let _this = this;

        // Change Select and Input Text
        this.$body.on('change', '.water-testing__select, .water-testing__input', function (e) {
            let $this = $(this),
                this_name = $this.attr('name');
            _this.change_item($this, this_name);
        });

        // Change Radio
        this.$body.on('change', '.wt-tests__radio', function (e) {
            let $this = $(this),
                this_name = $this.data('name'),
                $par = $this.closest('.wt-tests__body').find('wt-radio-selected');

            $par.find('wt-radio-selected').removeClass('wt-radio-selected');
            $this.parent('div').addClass('wt-radio-selected');
            _this.change_item($this, this_name);
        });

        // Change input
        this.$body.on('keydown', '.water-testing__input--validate-number', {obj: _this}, TheSpaShopPE.prototype.validate_number);
    };

    /**
     * Select item.
     *
     * @param  $elem
     * @param  select_type
     * @return void
     */
    TheSpaShopPE.prototype.change_item = function($elem, select_type) {
        if (select_type !== 'value') {
            this.hide_result_box();
            this.remove_result_boxes();
        }
        switch (select_type) {
            case 'devises' :
                let devise = this.get_list_by_id($elem.val(), this.devises, true);

                if (this.is_change_data_map === true) {
                    this.data_map.devises = $elem.val();
                }

                // if device is selected
                if (devise !== null) {
                    this.change_input('volume', '', true);
                    this.change_select('type', devise.id_volume, true);
                    this.change_input('volume', devise.volume, true);
                } else {
                    this.change_select('type', null, false);
                    this.change_input('volume', '', true);
                }
                break;
            case 'type' :
                if (this.is_change_data_map === true) {
                    this.data_map.type = $elem.val();
                    this.data_map.volume = this.data_map.chemical = null;
                    this.data_map.test = {};
                }

                if (this.data_map.type !== null) {
                    this.change_input('volume', null, false);
                } else {
                    this.change_input('volume', '', true);
                }
                break;
            case 'volume' :
                if (this.data_map.volume !== null && this.data_map.volume !== '' && this.data_map.value && $elem.val() !== '') {
                    if (this.is_change_data_map === true) {
                        this.data_map.volume = $elem.val();
                    }
                    this.show_results();
                } else {
                    if (this.is_change_data_map === true) {
                        this.data_map.volume = $elem.val();
                        this.data_map.chemical = null;
                        this.data_map.test = {};
                    }
                    this.create_select('chemical', this.data_map.volume);
                }
                break;
            case 'chemical' :
                if (this.is_change_data_map === true) {
                    this.data_map.chemical = $elem.val();
                    this.data_map.test = {};                                         
                }
                console.log('this.data_map.chemical', this.data_map.chemical);
                this.create_radio('test', this.data_map.chemical);
                break;
            case 'value' :
                if (this.is_change_data_map === true) {
                    this.data_map.test[$elem.data('parent')] = $elem.val();
                }
                this.show_results();
                break;
        }

        // Save session to the cookie
        Cookies.set('TheSpaShopPE.data_map', JSON.stringify(this.data_map), { expires: 7, path: '/' });
    };

    /**
     * Load init.
     *
     * @return $
     */
    TheSpaShopPE.prototype.load_init_data = function() {
        let data_map = this.to_json(Cookies.get('TheSpaShopPE.data_map'));
        let is_change = false;

        // Load server save init
        if (this.init.type !== void 0 && this.init.type) {
            console.log('server init', this.init);
            this.data_map = this.init;
            this.onbeforeunload(JSON.stringify(this.init));
        } else {
            // Load cookie init
            if (data_map !== void 0 && data_map.type !== void 0) {
                console.log('cookie init', data_map);
                this.data_map = data_map;
            }
        }
    };

    /**
     * Make save if necessary.
     *
     * @return void
     */
    TheSpaShopPE.prototype.auto_save = function() {
        let save = Cookies.get('TheSpaShopPE.save');

        console.log('save', save, Cookies.get('TheSpaShopPE.data_map'));
        if (save !== void 0 && save*1 === 1) {
            $('.wt-result-action--save:not(.wt-result-action--log-in)').trigger('click');
        }
    };

    /**
     * Set id if not exist.
     *
     * @return void
     */
    TheSpaShopPE.prototype.set_id = function() {
        if (this.data_map.id === void 0 || this.data_map.id === null) {
            this.data_map.id = uuidv4();
        }
    };

    /**
     * Load save.
     *
     * @return $
     */
    TheSpaShopPE.prototype.load = function() {
        if (this.data_map.type !== void 0 && this.data_map.type !== null) {
            this.is_change_data_map = false;

            if (this.data_map.devises !== void 0 && this.data_map.devises !== null && this.data_map.devises) {
                this.change_select('devises', this.data_map.devises, false);
            } else {
                if (this.data_map.type !== void 0 && this.data_map.type !== null) {
                    this.change_select('type', this.data_map.type, false);
                    if (this.data_map.volume !== void 0 && this.data_map.volume !== null) {
                        this.change_input('volume', this.data_map.volume, false);
                    }
                }
            }
            if (this.data_map.chemical !== void 0 && this.data_map.chemical !== null) {
                this.change_select('chemical', this.data_map.chemical, false);
            }

            for (let key in this.data_map.test) {
                if (this.data_map.test.hasOwnProperty(key)) {
                    let $result_box = $('#wt-tests--' + this.data_map.test[key]);

                    $result_box.prop('checked', true).trigger('change');
                }
            }

            this.is_change_data_map = true;
        }
    };

    /**
     * Add new result.
     *
     * @return $
     */
    TheSpaShopPE.prototype.add_result_box = function(id, block_class, box_class) {
        let $box = $('.' + block_class);
        let $result_template = $('.' + box_class + '--t');

        $box.append($result_template.clone());
        $result_template.first().removeClass(box_class + '--t').addClass(box_class + '--' + id);

        return $('.' + box_class + '--' + id);
    };

    /**
     * Show result box.
     *
     * @return void
     */
    TheSpaShopPE.prototype.show_result_box = function() {
        $('.water-testing__row--result').show();
    };

    /**
     * Hide result box.
     *
     * @return void
     */
    TheSpaShopPE.prototype.hide_result_box = function() {
        $('.water-testing__row--result').hide();
    };

    /**
     * Remove results.
     *
     * @return void
     */
    TheSpaShopPE.prototype.remove_result_boxes = function() {
        this.current_products = [];
        this.current_extra_products = [];
        $('.wt-result-box:not(.wt-result-box--t),.wt-info-box:not(.wt-info-box--t)').remove();
    };

    /**
     * Show result.
     *
     * @return void
     */
    TheSpaShopPE.prototype.show_results = function() {
        this.loader_start();
        this.remove_result_boxes();
        if (Object.keys(this.data_map.test).length === 0) {
            this.hide_result_box();
        } else {
            let arr;

            this.show_result_box();
            for (let key in this.data_map.test) {
                if (this.data_map.test.hasOwnProperty(key)) {
                    // console.log(key, this.data_map.test[key]);
                    arr = this.get_array('result', {'p' : key, 'c': this.data_map.test[key]});
                    // console.log('arr', arr);

                    for (let i = 0; i < arr.length; i++) {
                        let $result_box = this.add_result_box(this.data_map.test[key] + '-' + arr[i].id, 'wt-result-boxes', 'wt-result-box');

                        this.fill_result(arr[i], $result_box, key);
                    }
                }
            }

            // Show products in the Related section
            this.show_related(this.current_products, this.current_extra_products);

            // Show 'This is useful'
            this.show_useful();
        }
        this.loader_stop();
    };

    /**
     * Show 'This is useful'.
     *
     * @return void
     */
    TheSpaShopPE.prototype.show_useful = function() {
        let arr = this.get_list_by_id(this.data_map.type, this.data_form, true);

        for (let i = 0; i < arr.global_result.length; i++) {
            // Filter to Bromine and Chlorine
            if (this.current_chemical_type() === 1 && arr.global_result[i].is_b*1 === 0) {
                continue;
            }
            if (this.current_chemical_type() === 2 && arr.global_result[i].is_c*1 === 0) {
                continue;
            }

            let $result_box = this.add_result_box(this.data_map.volume + '-' + arr.global_result[i].id, 'wt-info-boxes', 'wt-info-box');
            let $title = $('.wt-info-box__title', $result_box);
            let $text = $('.wt-info-box__text', $result_box);
            let products = this.get_products(arr.global_result[i].products);
            let products__html = '';
            let $result_product = $('.wt-info-box__products', $result_box);

            $title.html(arr.global_result[i].name);
            $text.html(this.filter_text(arr.global_result[i].text));

            // Show product links
            for (let i = 0; i < products.length; i++) {
                products__html += "<a href='"+ products[i].url +"' target='blank'>"+ this.filter_text(products[i].name) +"</a>, ";
            }
            $result_product.html(products__html.substr(0, products__html.length - 2));
        }
    };

    /**
     * Change result block.
     *
     * @param  data
     * @param  $results
     * @param  test_id
     * @return void
     */
    TheSpaShopPE.prototype.fill_result = function(data, $results, test_id) {
        let $result_regular = $('.water-testing__result--regular', $results);
        let $result_text = $('.water-testing__result--text', $results);
        let products;
        let extra_products;

        $results.show();
        $result_regular.hide();
        $result_text.hide();

        products = this.get_products(data.products);
        if (this.current_chemical_type() === 1) {
            extra_products = this.get_products(data.extra_products_b);
        } else {
            extra_products = this.get_products(data.extra_products_c);
        }

        // If result is 'text' - independent output.
        if (data.type === 'text') {
            $result_text.show();
            $result_text.find('.water-testing__cont').html(this.filter_text(data.add_text) + '.');
            $result_text.find('.water-testing__test').html(this.test_name_by_id(test_id));
        } else {
            let $value = $('.water-testing__value', $result_regular);
            let $product = $('.water-testing__result-product', $result_regular);
            let $test = $('.water-testing__test', $result_regular);
            let products__html = '';

            this.filter = [];
            this.filter['volume'] = Math.floor(this.data_map.volume * data.per_liter);

            // Show result in the box
            $result_regular.show();
            if (data.type === 'text_value') {
                $('.water-testing__prefix', $result_regular).hide();
                $value.html(this.filter_text(data.add_text)).css({'font-weight': '500'});
            } else {
                $value.html(this.filter['volume'] + ' ' + this.filter_text(data.add_text));
            }

            // Show Test name
            $test.html(this.test_name_by_id(test_id));

            // Show product links in the result box
            for (let i = 0; i < products.length; i++) {
                products__html += "<a href='"+ products[i].url +"' target='blank'>"+ this.filter_text(products[i].name) +"</a>, ";
            }
            $product.html(products__html.substr(0, products__html.length - 2));
        }

        // Push products in the global arrays
        this.current_products = this.push_products(products, this.current_products);
        this.current_extra_products = this.push_products(extra_products, this.current_extra_products);
    };

    /**
     * Push unique products in the array.
     *
     * @param  text
     * @return string
     */
    TheSpaShopPE.prototype.filter_text = function(text) {
        switch (true) {
            case (text.indexOf('%VALUE%') !== -1) :
                return text.replace('%VALUE%', this.filter['volume']);
            default:
                return text;
        }
    };

    /**
     * Return current chemical type.
     *
     * @return int
     */
    TheSpaShopPE.prototype.current_chemical_type = function() {
        return this.get_list_by_id(this.data_map.chemical, this.get_array('chemical', null), true).type * 1;
    };

    /**
     * Return test name by id.
     *
     * @param  id
     * @return string
     */
    TheSpaShopPE.prototype.test_name_by_id = function(id) {
        let arr = this.get_array('test', {'p' : id});
        arr = this.get_list_by_id(id, arr, true);
        return arr.name;
    };

    /**
     * Push unique products in the array.
     *
     * @param  products
     * @param  arr
     * @return array
     */
    TheSpaShopPE.prototype.push_products = function(products, arr) {
        for (let i = 0; i < products.length; i++) {
            let is_find = false;
            for (let j = 0; j < arr.length; j++) {
                if (products[i] === arr[j]) {
                    is_find = true;
                    break;
                }
            }
            if (!is_find) {
                arr.unshift(products[i]);
            }
        }
        return arr;
    };

    /**
     * Show products in the Related section.
     *
     * @param  products
     * @param  extra_products
     * @return void
     */
    TheSpaShopPE.prototype.show_related = function(products, extra_products) {
        let $products = $('.wt-products');
        let $product_old = $('.wt-product:not(.wt-product--t)');

        if (extra_products.length !== 0) {
            products = products.concat(extra_products);
        }
        if (JSON.stringify(window.products_cache) !== JSON.stringify(products)) {
            $product_old.remove();
            for (let i = 0; i < products.length; i++) {
                let $product_template = $('.wt-product--t');
                let $product_new;
                $products.append($product_template.clone());
                $product_template.first().removeClass('wt-product--t').addClass('wt-product--' + products[i].id);

                $product_new = $('.wt-product--' + products[i].id);
                $product_new.find('.wt-product__img').css({
                    'background-image' : 'url('+ products[i].img +')',
                });
                $product_new.find('.wt-product__title').text(products[i].name);
                $product_new.find('a:not(.wt-product__button-text--to-cart)').attr('href', products[i].url);
                $product_new.find('.wt-product__cost').html(products[i].cost);
                $product_new.find('.wt-product__button').data('id', products[i].id);
            }
            window.products_cache = products;
        }
    };

    /**
     * Get product html by id(s).
     *
     * @param  ids
     * @return array
     */
    TheSpaShopPE.prototype.get_products = function(ids) {
        let products = [];
        ids = ids.split(',');
        for (let j = 0; j < ids.length; j++) {
            for (let i = 0; i < this.products.length; i++) {
                if (this.products[i].id*1 === ids[j]*1) {
                    products[j] = this.products[i];
                    break;
                }
            }
        }
        return products;
    };

    /**
     * Create radio.
     *
     * @param  type
     * @param  val
     * @return void
     */
    TheSpaShopPE.prototype.create_radio = function(type, val) {
        let $box = $('.wt-tests');
        let list_par = [];

        $box.empty();
        if (val !== null && val !== '') {
            list_par = this.get_array(type);
            // console.log('list_par', list_par);
            for (let i = 0; i < list_par.length; i++) {
                let list_child = this.get_array('value', {'p' : list_par[i].id});

                // console.log('list_child', list_child);
                this.template.print_test_item(list_par[i], list_child, this.current_chemical_type());
            }
        }
    };

    /**
     * Create select box.
     *
     * @param  type
     * @param  val
     * @return void
     */
    TheSpaShopPE.prototype.create_select = function(type, val) {
        let $elem = $('.water-testing__select[name="'+ type +'"]');
        let list = [];

        $elem.empty();
        if (val !== null && val !== '') {
            list = this.get_array(type);
            list.forEach(function (elem, index, array) {
                $elem.append('<option value="'+ elem.id +'">'+ elem.name +'</option>');
            });
            if (list.length === 1 && list[0].name === '—') {
                $elem.prop("disabled", true);
            } else {
                $elem.prop("disabled", false);
            }
        } else {
            $elem.prop("disabled", true);
            $elem.trigger('change');
            return;
        }

        // if singe option then select this
        if (list.length !== 1 || this.is_change_data_map !== true) {
            $elem.val(null);
        }

        $elem.trigger('change');
    };

    /**
     * Get data array by type and value.
     *
     * @param  type
     * @param  id
     * @return array
     */
    TheSpaShopPE.prototype.get_array = function(type, id) {
        let arr = this.get_list_by_id(this.data_map.type, this.data_form);
        if ('chemical' !== type) {
            // console.log('!== type', this.data_map.chemical, arr);
            arr = this.get_list_by_id(this.data_map.chemical, arr);
            if ('test' !== type) {
                arr = this.get_list_by_id(id.p, arr);
                if ('value' !== type) {
                    arr = this.get_list_by_id(id.c, arr);
                }
            }
        }
        // console.log('type', arr, this.data_map);
        return arr;
    };

    /**
     * Get list by id.
     *
     * @param  val
     * @param  arr
     * @param  is_full
     * @return array
     */
    TheSpaShopPE.prototype.get_list_by_id = function(val, arr, is_full) {
        for (let i = 0; i < arr.length; i++) {
            if (arr[i].id * 1 === val * 1) {
                if (is_full) return arr[i];
                return arr[i].data;
            }
        }
        return null;
    };

    /**
     * Change value of select box.
     *
     * @param  type
     * @param  val
     * @param  disabled
     * @return void
     */
    TheSpaShopPE.prototype.change_select = function(type, val, disabled) {
        let $elem = $('.water-testing__select[name="'+ type +'"]');

        $elem.val(val).prop("disabled", disabled).trigger('change');
    };

    /**
     * Show input.
     *
     * @param  type
     * @param  val
     * @param  disabled
     * @return void
     */
    TheSpaShopPE.prototype.change_input = function(type, val, disabled) {
        let $elem = $('.water-testing__input[name="'+ type +'"]');

        if (val !== null) {
            $elem.val(val);
        }
        $elem.prop("disabled", disabled).trigger('change');
    };

    /**
     * Loader start.
     */
    TheSpaShopPE.prototype.loader_start = function() {
        this.$body.addClass('spa-loader');
    };

    /**
     * Loader stop.
     */
    TheSpaShopPE.prototype.loader_stop = function() {
        this.$body.removeClass('spa-loader');
    };

    /**
     * Validate number for input.
     *
     * @param  event
     * @return void
     */
    TheSpaShopPE.prototype.validate_number = function(event) {
        // dot, backspace, delete, tab и escape
        if (event.keyCode == 190 || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
            // Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // home, end, влево, вправо
            (event.keyCode >= 35 && event.keyCode <= 39)) {
            return;
        } else {
            // 0-9
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault();
            }
        }
    };

    /**
     * Error handler.
     *
     * @param  err
     * @return void
     */
    TheSpaShopPE.prototype.error = function(err) {
        console.error(err);
    };

    /**
     * Return data_map.
     *
     * @return object
     */
    TheSpaShopPE.prototype.get_data = function() {
        return this.data_map;
    };

    /**
     * Return current id.
     *
     * @return object
     */
    TheSpaShopPE.prototype.get_id = function() {
        return this.data_map.id;
    };

    // Init.
    $(function () {
        window.thespashoppe = new TheSpaShopPE();
    });

}($ || window.jQuery));