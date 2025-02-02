<?php

/**
 * Key-value field class.
 */
abstract class RWMB_Key_Value_Field extends RWMB_Text_Field
{
	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function html( $meta, $field )
	{
		// Key
		$key                       = isset( $meta[0] ) ? $meta[0] : '';
		$attributes                = self::get_attributes( $field, $key );
		$attributes['placeholder'] = esc_attr__( 'Key', 'neighborhood' );
		$html                      = sprintf( '<input %s>', self::render_attributes( $attributes ) );

		// Value
		$val                       = isset( $meta[1] ) ? $meta[1] : '';
		$attributes                = self::get_attributes( $field, $val );
		$attributes['placeholder'] = esc_attr__( 'Value', 'neighborhood' );
		$html .= sprintf( '<input %s>', self::render_attributes( $attributes ) );

		return $html;
	}

	/**
	 * Show begin HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function begin_html( $meta, $field )
	{
		$desc = $field['desc'] ? "<p id='{$field['id']}_description' class='description'>{$field['desc']}</p>" : '';

		if ( empty( $field['name'] ) )
			return '<div class="rwmb-input">' . $desc;

		return sprintf(
			'<div class="rwmb-label">
				<label for="%s">%s</label>
			</div>
			<div class="rwmb-input">
			%s',
			$field['id'],
			$field['name'],
			$desc
		);
	}

	/**
	 * Show end HTML markup for fields
	 * Do not show field description. Field description is shown before list of fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function end_html( $meta, $field )
	{
		$button = $field['clone'] ? self::add_clone_button( $field ) : '';
		$html   = "$button</div>";
		return $html;
	}

	/**
	 * Escape meta for field output
	 *
	 * @param mixed $meta
	 * @return mixed
	 */
	static function esc_meta( $meta )
	{
		foreach ( (array) $meta as $k => $pairs )
		{
			$meta[$k] = array_map( 'esc_attr', (array) $pairs );
		}
		return $meta;
	}

	/**
	 * Sanitize field value.
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return string
	 */
	static function value( $new, $old, $post_id, $field )
	{
		foreach ( $new as &$arr )
		{
			if ( empty( $arr[0] ) && empty( $arr[1] ) )
				$arr = false;
		}
		$new = array_filter( $new );
		return $new;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field )
	{
		$field                       = parent::normalize( $field );
		$field['clone']              = true;
		$field['multiple']           = true;
		$field['attributes']['type'] = 'text';
		return $field;
	}

	/**
	 * Format value for the helper functions.
	 * @param array        $field Field parameter
	 * @param string|array $value The field meta value
	 * @return string
	 */
	public static function format_value( $field, $value )
	{
		$output = '<ul>';
		foreach ( $value as $subvalue )
		{
			$output .= sprintf( '<li><label>%s</label>: %s</li>', $subvalue[0], $subvalue[1] );
		}
		$output .= '</ul>';
		return $output;
	}
}
