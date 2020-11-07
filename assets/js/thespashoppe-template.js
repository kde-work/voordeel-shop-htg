(function ($) {
    if (!$) {
        console.error('jQuery and $ are missing');
        return;
    }

    /**
     * Form water testing Template Class.
     */
    function SpaTemplate() {
        this.utc_time_to_local_with_masc();
    }

    /**
     * Echo test block.
     *
     * @param  parent
     * @param  child
     * @param  test_type
     */
    SpaTemplate.prototype.print_test_item = function(parent, child, test_type) {
        let $block = $('.wt-tests'),
            html = '';

        if ($block.length) {
            html = '<div class="wt-tests__item wt-tests__item--'+ parent.id +'">\n' +
                        '<div class="wt-tests__title">'+ parent.name +'</div>\n' +
                        '<div class="wt-tests__body">';
            for (let i = 0; i < child.length; i++) {
                html += '<div class="wt-tests__line wt-tests__line--'+ child[i].id +'">\n' +
                            '<input type="radio" name="wt-tests--'+ parent.id +'" id="wt-tests--'+ child[i].id +'" value="'+ child[i].id +'" data-parent="'+ parent.id +'" data-name="value" class="wt-tests__radio" autocomplete="off">\n' +
                            '<label for="wt-tests--'+ child[i].id +'" class="wt-tests__label"><span style="'+ this.get_gradient(test_type, child[i].color_b, child[i].color_c) +'"></span>'+ child[i].name +'</label>\n' +
                        '</div>';
            }
            html += '</div></div>';
            $block.append(html);
        }
    };

    /**
     * Return gradient/background style.
     *
     * @param  test_type
     * @param  color_b
     * @param  color_c
     */
    SpaTemplate.prototype.get_gradient = function(test_type, color_b, color_c) {
        let color = color_c;

        if (test_type*1 === 1) {
            color = color_b;
        }
        if (!color) {
            return 'display: none;';
        }
        if (color.indexOf(",") === -1) {
            return 'background:' + color + ';';
        } else {
            color = color.split(',');
            return 'background: '+ color[0] +';\n' +
                'background: -moz-linear-gradient(left,  '+ color[0] +' 0%, '+ color[1] +' 100%);\n' +
                'background: -webkit-linear-gradient(left,  '+ color[0] +' 0%,'+ color[1] +' 100%);\n' +
                'background: linear-gradient(to right,  '+ color[0] +' 0%,'+ color[1] +' 100%);\n' +
                'filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\''+ color[0] +'\', endColorstr=\''+ color[1] +'\',GradientType=1 );\n';
        }
    };

    /**
     * Setup UTC time to Local.
     */
    SpaTemplate.prototype.utc_time_to_local_with_masc = function() {
        let $time = $('[data-utc][data-masc]');

        $time.each(function () {
            let $this = $(this),
                this_date = $this.data('utc'),
                this_masc = $this.data('masc'),
                date = new Date(this_date);

            $this.trigger('epme.date-before-with-masc');
            switch (this.tagName.toLowerCase()) {
                case 'input':
                    if (date instanceof Date && !isNaN(date)) {
                        $this.val(date.spa_format(this_masc));
                    }
                    break;
                default:
                    if (date instanceof Date && !isNaN(date)) {
                        $this.text(date.spa_format(this_masc));
                    }
            }
            $this.trigger('epme.date-complete-with-masc');
        });
    };

    window.SpaTemplate = SpaTemplate;
}($ || window.jQuery));