<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
<button id="button_1" value="val_1" name="but1">Click me</button>
<p>
		<?php esc_html_e( 'Cuicpro Block - hello from a dynamic block!', 'cuicpro-block' ); ?>
	</p>
</div>

