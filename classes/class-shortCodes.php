<?php
/**
 * Shortcodes Class.
 *
 * @package THESPA_waterTesting\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

/**
 * THESPA_Shortcodes Class.
 */
class THESPA_shortcodes {

	/**
	 * Show button Subscribe button.
	 *
	 * @param  array $attributes
	 * @return string
	 */
	public static function waterTesting( $attributes ) {
		$atts = shortcode_atts( array(
		), $attributes );

		THESPA_Media::resources();
        $obj_data = new THESPA_data();
        $data = $obj_data->get_data();
        $volume = $obj_data->get_list_of( 'volume' );
		$devises = $obj_data->get_list_of( 'devices' );
		$products = $obj_data->get_list_of( 'products' );
		$init = $obj_data->get_save_by_id( $_GET['js_id'] );
		$current_link = get_the_permalink( get_the_ID() );

		if ( empty( $init ) AND !$_COOKIE['TheSpaShopPE_data_map'] ) {
			$init = $obj_data->get_init_blank_param();
        }

		ob_start();
//		echo do_shortcode('[login-with-ajax registration=1]');
//		echo do_shortcode('[lrm_form default_tab="login" logged_in_message="You are currently logged in!"]');
//		echo "<div style='white-space: pre;'>"; print_r($data); echo "</div>"; die;
		?>
		<div class="water-testing">
            <form class="water-testing__form">
                <div class="wt-previous">
                    <?php echo self::getSaves( $obj_data ); ?>
                </div>
                <div class="water-testing__row water-testing__row--title">
                    <h4 class="water-testing__h">Water test</h4>
                </div>
                <div class="water-testing__row">
                    <div class="water-testing__col water-testing__col--100p">
                        <div class="water-testing__title">1. What are you testing?</div>
                    </div>
                    <div class="water-testing__col">
                        <label for="water-testing--devises" class="water-testing__label">Your model</label>
                        <select name="devises" id="water-testing--devises" class="water-testing__select water-testing__select--devises" title="Select your model" autocomplete="off">
			                <?php foreach ( $devises as $item ) {
				                echo "<option value=\"{$item['id']}\">{$item['name']}</option>";
			                }?>
                        </select>
                    </div>
                    <div class="water-testing__col water-testing__col--or">or</div>
                    <div class="water-testing__col">
                        <label for="water-testing--volume" class="water-testing__label">Type of</label>
                        <select name="type" id="water-testing--type" class="water-testing__select water-testing__select--type" title="Type of your tub" autocomplete="off">
			                <?php foreach ( $volume as $item ) {
				                echo "<option value=\"{$item['id']}\">{$item['name']}</option>";
			                }?>
                        </select>
                    </div>
                </div>
                <div class="water-testing__row">
                    <div class="water-testing__col water-testing__col--100p">
                        <div class="water-testing__title">2. Insert the volume</div>
                    </div>
                    <div class="water-testing__col">
                        <label for="water-testing--volume" class="water-testing__label">The volume in Liters</label>
                        <input type="text" name="volume" id="water-testing--volume" disabled title="The volume in Liters" autocomplete="off" class="water-testing__input water-testing__input--validate-number water-testing__input--volume">
                    </div>
                </div>
                <div class="water-testing__row">
                    <div class="water-testing__col water-testing__col--100p">
                        <div class="water-testing__title">3. What test strips are you using?</div>
                    </div>
                    <div class="water-testing__col">
                        <label for="water-testing--chemical" class="water-testing__label">Type of your test</label>
                        <select name="chemical" id="water-testing--chemical" disabled class="water-testing__select water-testing__select--chemical" title="Type of your test" autocomplete="off">
                        </select>
                    </div>
                </div>
                <div class="water-testing__row">
                    <div class="water-testing__col water-testing__col--100p">
                        <div class="water-testing__title">4. Tests</div>
                    </div>
                    <div class="water-testing__col water-testing__col--100p">
                        <div class="water-testing__warning"><div class="water-testing__icon water-testing__icon--warning"></div><div class="water-testing__warning-text">Attention! Colors on your screen may differ from the reference colors. We recommend using colors from the respective test packs.</div></div>
                    </div>
                    <div class="water-testing__col water-testing__col--100p">
                        <div class="wt-tests">
                            <div class="wt-tests__item wt-tests__item--ph wt-tests__item--deactivate">
                                <div class="wt-tests__title">pH</div>
                                <div class="wt-tests__body"><div class="wt-tests__line wt-tests__line--1">
                                        <input type="radio" name="wt-tests--1" id="wt-tests--1" value="1" data-parent="1" data-name="value" class="wt-tests__radio" autocomplete="off">
                                        <label for="wt-tests--1" class="wt-tests__label"><span style="background:#D70825;"></span>8.4</label>
                                    </div><div class="wt-tests__line wt-tests__line--2">
                                        <input type="radio" name="wt-tests--1" id="wt-tests--2" value="2" data-parent="1" data-name="value" class="wt-tests__radio" autocomplete="off">
                                        <label for="wt-tests--2" class="wt-tests__label"><span style="background:#D84A30;"></span>7.8</label>
                                    </div><div class="wt-tests__line wt-tests__line--3">
                                        <input type="radio" name="wt-tests--1" id="wt-tests--3" value="3" data-parent="1" data-name="value" class="wt-tests__radio" autocomplete="off">
                                        <label for="wt-tests--3" class="wt-tests__label"><span style="background:#DB5100;"></span>7.2</label>
                                    </div>
                                </div>
                            </div>
                            <div class="wt-tests__item wt-tests__item--2 wt-tests__item--deactivate">
                                <div class="wt-tests__title">Alkalinity</div>
                                <div class="wt-tests__body"><div class="wt-tests__line wt-tests__line--9">
                                        <input type="radio" name="wt-tests--2" id="wt-tests--9" value="9" data-parent="2" data-name="value" class="wt-tests__radio" autocomplete="off">
                                        <label for="wt-tests--9" class="wt-tests__label"><span style="background:#C69021;"></span>0 ppm</label>
                                    </div><div class="wt-tests__line wt-tests__line--10">
                                        <input type="radio" name="wt-tests--2" id="wt-tests--10" value="10" data-parent="2" data-name="value" class="wt-tests__radio" autocomplete="off">
                                        <label for="wt-tests--10" class="wt-tests__label"><span style="background:#A49634;"></span>40 ppm</label>
                                    </div><div class="wt-tests__line wt-tests__line--11">
                                        <input type="radio" name="wt-tests--2" id="wt-tests--11" value="11" data-parent="2" data-name="value" class="wt-tests__radio" autocomplete="off">
                                        <label for="wt-tests--11" class="wt-tests__label"><span style="background:#858B3D;"></span>80 ppm</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="water-testing__col" style="display:none;">
                        <label for="water-testing--test" class="water-testing__label">Name of your test</label>
                        <select name="test" id="water-testing--test" disabled class="water-testing__select water-testing__select--test" title="Name of your test" autocomplete="off">
                        </select>
                    </div>
                </div>
            </form>
            <div class="water-testing__row water-testing__row--result">
                <div class="water-testing__col water-testing__col--100p">
                    <div class="water-testing__hs"><span>Results</span></div>
                </div>
                <div class="water-testing__col wt-result-boxes">
                    <div class="water-testing__title">Water Balancing Adjustments</div>
                    <div class="water-testing__result-box wt-result-box wt-result-box--t">
                        <div class="water-testing__icon water-testing__icon--bell water-testing__icon--brown"></div>
                        <div class="water-testing__result water-testing__result--regular"><span class="water-testing__test">%TEST%</span> <span class="water-testing__prefix">You need add </span><span class="water-testing__value">%VALUE%</span><span class="water-testing__postfix"> of </span><span class="water-testing__result-product">%PRODUCT%</span><span class="water-testing__dot">.</span></div>
                        <div class="water-testing__result water-testing__result--text"><span class="water-testing__test">%TEST%</span> <span class="water-testing__cont"></span></div>
                    </div>
                </div>
                <div class="water-testing__col water-testing__col--100p wt-info-boxes water-testing__col--info">
                    <div class="water-testing__title">Regular Maintenance</div>
                    <div class="water-testing__result-box wt-info-box wt-info-box--t">
                        <div class="water-testing__icon water-testing__icon--info"></div>
                        <div class="water-testing__result">
                            <div class="wt-info-box__title"></div>
                            <div class="wt-info-box__text"></div>
                            <div class="wt-info-box__result-product"><span class="wt-info-box__prefix">Products: </span><span class="wt-info-box__products"></span>.</div>
                        </div>
                    </div>
                </div>
                <div class="water-testing__col water-testing__col--100p water-testing__col--actions">
                    <div class="water-testing__title">Actions</div>
                    <div class="wt-result-actions">
                        <div class="wt-result-action wt-button wt-result-action--save <?php echo ( !is_user_logged_in() ) ? 'wt-result-action--log-in': ''; ?>">
                            <div class="water-testing__icon water-testing__icon--save water-testing__icon--red"></div>
                            <div class="wt-result-action__text"><?php echo ( !is_user_logged_in() ) ? 'Log in and ': ''; ?>Save</div>
                        </div>
                        <div class="wt-result-action wt-button wt-result-action--print">
                            <div class="water-testing__icon water-testing__icon--print water-testing__icon--brown"></div>
                            <div class="wt-result-action__text">Print</div>
                        </div>
                        <div class="wt-result-action wt-button wt-result-action--to-email">
                            <div class="water-testing__icon water-testing__icon--mail water-testing__icon--brown"></div>
                            <div class="wt-result-action__text">Email Results</div>
                        </div>
                        <div class="wt-result-action wt-button wt-result-action--new" data-href="<?php echo $current_link; ?>">
                            <div class="water-testing__icon water-testing__icon--plus water-testing__icon--brown"></div>
                            <div class="wt-result-action__text">New Test</div>
                        </div>
                    </div>
                    <div class="wt-help">
                        <div class="wt-help__title">Do you need help with test results?</div>
                        <div class="wt-help__text">We can help you.</div>
                        <div class="wt-help__button wt-button wt-button--shadow wt-result-action--get-help"><div class="water-testing__icon water-testing__icon--talk water-testing__icon--brown"></div>Get help</div>
                    </div>
                </div>
                <div class="water-testing__col water-testing__col--100p water-testing__col--products">
                    <div class="water-testing__title">Related Products</div>
                    <div class="wt-products">
                        <div class="wt-product wt-product--t">
                            <a class="wt-product__img" href="/" target="_blank" style="background-image: url()"></a>
                            <div class="wt-product__cont">
                                <a href="/" target="_blank" class="wt-product__title"></a>
                                <div class="wt-product__meta">
                                    <div class="wt-product__cost"></div>
                                    <div class="wt-product__count">
                                        <div class="wt-product__count-btn wt-product__count-btn--minus" data-count="-1">−</div>
                                        <input type="text" name="count" title="Count of product" value="1" autocomplete="off" class="wt-product__input">
                                        <div class="wt-product__count-btn wt-product__count-btn--plus" data-count="1">+</div>
                                    </div>
                                </div>
                                <div class="wt-product__button" data-id="">
                                    <div class="wt-product__button-text wt-product__button-text--default">Add to cart</div>
                                    <div class="wt-product__button-text wt-product__button-text--loading"></div>
                                    <a href="/cart/" target="_blank" class="wt-product__button-text wt-product__button-text--to-cart">Go to Cart →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wt-loading-init"></div>
                </div>
            </div>
            <?php echo self::emailForm( 'to-email', 'Email Results', 'Send Email', false ); ?>
            <?php echo self::emailForm( 'get-help', 'Get Help', 'Send Email', true ); ?>
        </div>
        <script type="application/json" id="water-testing-data"><?php echo json_encode( $data ); ?></script>
        <script type="application/json" id="water-testing-products"><?php echo json_encode( $products ); ?></script>
        <script type="application/json" id="water-testing-devises"><?php echo json_encode( $devises ); ?></script>
        <script type="application/json" id="water-testing-init"><?php echo json_encode( $init ); ?></script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Show saves html.
	 *
     * @param  string $id
     * @param  string $title
     * @param  string $button
     * @param  bool $is_message
	 * @return string
	 */
	public static function emailForm( $id, $title = 'Email Results', $button = 'Send Email', $is_message = false ) {
		ob_start();
		?>
        <div class="wt-modal wt-modal--<?php echo $id; ?>">
            <div class="wt-modal__box">
                <div class="wt-modal__body">
                    <div class="wt-modal__title"><?php echo $title; ?></div>
                    <form class="wt-modal__form" method="post" data-action="<?php echo $id; ?>">
                        <div class="wt-modal__email">
                            <label for="wt-email" class="wt-modal__label wt-modal__label--email">Your email</label>
                            <?php
                            $user = wp_get_current_user();
                            $email = ( $user->exists() AND $user->user_email ) ? $user->user_email : '';
                            ?>
                            <input type="email" class="wt-modal__input wt-modal__input--email" id="wt-email" name="email" autocomplete="on" required value="<?php echo $email; ?>">
                            <?php if ( $is_message ) : ?>
                                <label for="wt-message" class="wt-modal__label wt-modal__label--message">Message</label>
                                <textarea name="message" id="wt-message" cols="30" rows="10" class="wt-modal__input wt-modal__input--message"></textarea>
                            <?php endif; ?>
                        </div>
                        <div class="wt-modal__submit">
                            <button class="wt-button">
                                <span class="water-testing__icon water-testing__icon--mail water-testing__icon--white"></span>
                                <span class="water-testing__text"><?php echo $button; ?></span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="wt-modal__loading"><div></div></div>
                <div class="wt-modal__complete"><div>
                        <div class="wt-modal__complete-text">The request was sent successfully.</div>
                        <button class="wt-button wt-modal__close">
                            <span class="water-testing__text">Close Form</span>
                        </button></div></div>
            </div>
            <div class="wt-modal__bg wt-modal__close" title="Close Form"></div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Show saves html.
	 *
	 * @param  THESPA_data|bool $obj_data
	 * @return string
	 */
	public static function getSaves( $obj_data = false ) {
        if ( $obj_data === false ) {
	        $obj_data = new THESPA_data();
        }
		$saves = $obj_data->get_saves();
		$current_link = get_the_permalink( get_the_ID() );
		$js_id = ( isset( $_GET['js_id'] ) AND $_GET['js_id'] ) ? $_GET['js_id'] : '';
		$js_id = ( isset( $_POST['id'] ) AND $_POST['id'] ) ? $_POST['id'] : $js_id;

		ob_start();
		if ( !empty( $saves ) ) :
		?>
        <div class="water-testing__row water-testing__row--previous">
            <h4 class="water-testing__h water-testing__h--small">Previous tests</h4>
            <div class="wt-previous__items">
                <?php foreach ( $saves as $save ) {
                    $js_data = json_decode( $save['data'] );
                    $name = self::getName( $obj_data, $js_data );
                    ?>
                    <div class="wt-previous__item wt-previous--<?php echo $save['js_id']; ?> <?php echo ( $js_id == $save['js_id'] ) ? 'wt-previous--current' : ''; ?>">
                        <div class="wt-previous__name"><?php echo $name; ?><span data-utc="<?php echo gmdate( 'm/d/Y G:i:s T', strtotime( $save['date'] ) ); ?>" data-masc="beauty"><?php echo $save['date']; ?></span></div>
                        <a class="wt-previous__view" target="_blank" href="<?php echo "{$current_link}?js_id={$save['js_id']}"; ?>">View</a>
                        <div class="wt-previous__remove" data-js-id="<?php echo $save['js_id']; ?>">Remove</div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
		<?php
		endif;
		return ob_get_clean();
	}

	/**
	 * Return first part of name of save.
	 *
	 * @param  THESPA_data $obj_data
	 * @param  object $js_data
	 * @param  string $postfix
	 * @return string
	 */
	public static function getName( $obj_data, $js_data, $postfix = ' — ' ) {
        if ( is_object( $js_data ) ) {
            $devises = $js_data->devises;
            $type = $js_data->type;
            $volume = $js_data->volume;
        } else {
	        $devises = $js_data['devises'];
	        $type = $js_data['type'];
	        $volume = $js_data['volume'];
        }
		if ( $devises ) {
			return "{$obj_data->get_device( $devises )['name']}$postfix";
        }
		if ( $type AND $volume ) {
			return "{$obj_data->get_volume( $type )['name']} - {$volume}L$postfix";
        }

		return '';
	}
}
