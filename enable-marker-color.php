<?php
/**
 * Plugin Name: Enable Marker Color
 * Description: Add settings to the List block to change marker color.
 * Version: 1.0.0
 * Author: Toro_Unit,HAMWORKS
 * Author URI: https://torounit.com
 * License: GPL2 or Later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: enable-marker-color
 * Requires at least: 6.4
 * Requires PHP: 8.0
 *
 * @package Enable_Marker_Color
 */

namespace Enable_Marker_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin main class.
 */
class Plugin {


	const ASSET_HANDLE = 'enable-marker-color';

	const TEXT_DOMAIN = 'enable-marker-color';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_assets' ), 100 );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register assets.
	 *
	 * @return void
	 */
	public function register_assets(): void {

		wp_set_script_translations(
			self::ASSET_HANDLE,
			self::TEXT_DOMAIN,
		);

		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_style( self::ASSET_HANDLE, false );
		wp_add_inline_style( self::ASSET_HANDLE, $this->get_inline_styles() );

		if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/index.asset.php' ) ) {
			$asset = include plugin_dir_path( __FILE__ ) . 'build/index.asset.php';
			wp_register_script(
				self::ASSET_HANDLE,
				plugin_dir_url( __FILE__ ) . 'build/index.js',
				$asset['dependencies'],
				$asset['version'],
				array(
					'in_footer' => true,
				)
			);
		}

		wp_set_script_translations( self::ASSET_HANDLE, self::TEXT_DOMAIN );
	}

	/**
	 * Get CSS.
	 *
	 * @return string
	 */
	private function get_inline_styles(): string {
		$colors = $this->get_colors();
		$css    = '';
		foreach ( $colors as $color ) {
			$css .= <<<CSS
				.has-{$color['slug']}-marker-color li::marker {
					color: {$color['color']};
				}
			CSS;
		}

		return $css;
	}

	/**
	 * Get colors.
	 *
	 * @return array{name: string, slug: string, color: string}
	 */
	public function get_colors(): array {

		$tree    = \WP_Theme_JSON_Resolver::get_merged_data();
		$palette = $tree->get_settings()['color']['palette'];

		if ( $tree->get_settings()['color']['defaultPalette'] ) {
			$colors = array_merge( $palette['default'], $palette['theme'] );
		} else {
			$colors = $palette['theme'];
		}

		if ( ! empty( $palette['custom'] ) ) {
			$colors = array_merge( $colors, $palette['custom'] );
		}

		return $colors;
	}


	/**
	 * Enqueue assets.
	 */
	public function enqueue_block_assets(): void {
		wp_enqueue_style( self::ASSET_HANDLE );
	}

	/**
	 * Enqueue block editor assets.
	 */
	public function enqueue_block_editor_assets(): void {
		wp_enqueue_style( self::ASSET_HANDLE );
		wp_enqueue_script( self::ASSET_HANDLE );
	}
}

new Plugin();
