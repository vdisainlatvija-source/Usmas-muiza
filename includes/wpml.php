<?php
/**
 * WPML + ACF integration helpers
 *
 * Bridges WPML Advanced Translation Editor (ATE) with ACF flexible content.
 * ACFML "advanced" mode doesn't write translated values to wp_postmeta,
 * so we sync them manually from WPML's icl_translate tables.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Sync ACF flexible content from original post + apply WPML translations.
 *
 * 1. Copies the layout structure from the original language post
 * 2. Pulls translated values from WPML's translation tables (ATE data)
 * 3. Writes translated values to the translated post's meta
 *
 * Uses a sync flag to avoid re-running on every page load.
 * Delete the flag `_acf_wpml_v3_{field_name}` to force re-sync.
 *
 * @param string   $field_name The flexible content field name.
 * @param int|null $post_id    Post ID to check. Defaults to current post.
 * @return int The post ID to use for have_rows().
 */
function usmasmuiza_get_acf_post_id( $field_name = 'sections', $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Check if we're on a translated page
	$default_lang = apply_filters( 'wpml_default_language', null );
	$current_lang = apply_filters( 'wpml_current_language', null );
	$is_translation = $default_lang && $current_lang && $current_lang !== $default_lang;

	// Not a translation — use current post directly
	if ( ! $is_translation ) {
		return $post_id;
	}

	// Already synced — skip (v3 flag = multi-job aggregation fix)
	$sync_flag = '_acf_wpml_v3_' . $field_name;
	if ( get_post_meta( $post_id, $sync_flag, true ) && have_rows( $field_name, $post_id ) ) {
		return $post_id;
	}

	$original_id = apply_filters( 'wpml_object_id', $post_id, 'page', true, $default_lang );

	if ( ! $original_id || $original_id === $post_id ) {
		return $post_id;
	}

	global $wpdb;

	// Step 1: Copy all ACF meta from original post (layout structure + default values)
	$meta_rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT meta_key, meta_value FROM {$wpdb->postmeta}
		 WHERE post_id = %d
		 AND ( meta_key LIKE %s OR meta_key LIKE %s )",
		$original_id,
		$wpdb->esc_like( $field_name ) . '%',
		'\_' . $wpdb->esc_like( $field_name ) . '%'
	) );

	if ( ! $meta_rows ) {
		return $post_id;
	}

	foreach ( $meta_rows as $meta ) {
		update_post_meta( $post_id, $meta->meta_key, maybe_unserialize( $meta->meta_value ) );
	}

	// Step 2: Get translated values from WPML ATE and overwrite
	$translations = usmasmuiza_get_wpml_translations( $post_id, $field_name );

	foreach ( $translations as $meta_key => $translated_value ) {
		update_post_meta( $post_id, $meta_key, $translated_value );
	}

	// Mark as synced
	update_post_meta( $post_id, $sync_flag, time() );

	return $post_id;
}

/**
 * Get translated ACF field values from WPML's ATE translation tables.
 *
 * WPML ATE stores translations in icl_translate as base64+zlib compressed data.
 * Field types follow the pattern: field-{meta_key}-{index}[-{sub_property}]
 *
 * For link fields, sub-properties (title, url, target) are stored separately
 * and need to be reassembled into an array for ACF.
 *
 * @param int    $translated_post_id The translated post ID.
 * @param string $field_name         The flexible content field name to filter by.
 * @return array Associative array of meta_key => translated_value.
 */
function usmasmuiza_get_wpml_translations( $translated_post_id, $field_name = 'sections' ) {
	global $wpdb;

	$prefix = $wpdb->prefix;

	// Check if WPML tables exist
	$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$prefix}icl_translate'" );
	if ( ! $table_exists ) {
		return [];
	}

	// Find ALL completed translation jobs for this post (newest first)
	$job_ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT j.job_id
		 FROM {$prefix}icl_translate_job j
		 INNER JOIN {$prefix}icl_translation_status ts ON j.rid = ts.rid
		 INNER JOIN {$prefix}icl_translations t ON ts.translation_id = t.translation_id
		 WHERE t.element_id = %d
		 AND t.element_type LIKE 'post_%%'
		 AND j.translated = 1
		 ORDER BY j.job_id DESC",
		$translated_post_id
	) );

	if ( ! $job_ids ) {
		return [];
	}

	// Get translated fields from ALL jobs, latest first.
	// Use a subquery to pick the most recent value per field_type.
	$placeholders  = implode( ',', array_fill( 0, count( $job_ids ), '%d' ) );
	$like_pattern  = 'field-' . $wpdb->esc_like( $field_name ) . '%';
	$query_args    = array_merge( $job_ids, [ $like_pattern ] );

	$fields = $wpdb->get_results( $wpdb->prepare(
		"SELECT t1.field_type, t1.field_data_translated
		 FROM {$prefix}icl_translate t1
		 INNER JOIN (
		     SELECT field_type, MAX(job_id) as max_job_id
		     FROM {$prefix}icl_translate
		     WHERE job_id IN ({$placeholders})
		     AND field_finished = 1
		     AND field_data_translated IS NOT NULL
		     AND field_data_translated != ''
		     AND field_type LIKE %s
		     GROUP BY field_type
		 ) t2 ON t1.field_type = t2.field_type AND t1.job_id = t2.max_job_id",
		...$query_args
	) );

	if ( ! $fields ) {
		return [];
	}

	$translations = [];
	$link_parts = [];

	foreach ( $fields as $field ) {
		$ft = $field->field_type;

		// Skip metadata fields (type/name descriptors for link sub-properties)
		if ( preg_match( '/-(type|name)$/', $ft ) ) {
			continue;
		}

		// Decode: base64 then zlib decompress
		$raw = base64_decode( $field->field_data_translated );
		$value = @gzuncompress( $raw );
		if ( $value === false ) {
			$value = $raw; // Fallback if not compressed
		}

		// Simple field: field-{meta_key}-{index}
		if ( preg_match( '/^field-([a-zA-Z0-9_]+)-(\d+)$/', $ft, $m ) ) {
			$translations[ $m[1] ] = $value;
		}
		// Link sub-property: field-{meta_key}-{index}-{property}
		elseif ( preg_match( '/^field-([a-zA-Z0-9_]+)-(\d+)-([a-z_]+)$/', $ft, $m ) ) {
			$meta_key = $m[1];
			$prop = $m[3];

			if ( ! isset( $link_parts[ $meta_key ] ) ) {
				$link_parts[ $meta_key ] = [];
			}
			$link_parts[ $meta_key ][ $prop ] = $value;
		}
	}

	// Reassemble link fields: merge translated sub-properties into existing arrays
	foreach ( $link_parts as $meta_key => $props ) {
		$existing = get_post_meta( $translated_post_id, $meta_key, true );

		if ( is_serialized( $existing ) ) {
			$existing = maybe_unserialize( $existing );
		}

		if ( is_array( $existing ) ) {
			foreach ( $props as $prop => $val ) {
				$existing[ $prop ] = $val;
			}
			$translations[ $meta_key ] = $existing;
		} else {
			$translations[ $meta_key ] = $props;
		}
	}

	return $translations;
}

/**
 * Clear ACF WPML sync flags when a translation is updated.
 * Hooked to WPML's translation save action.
 */
function usmasmuiza_clear_acf_wpml_sync( $new_post_id ) {
	global $wpdb;

	$wpdb->query( $wpdb->prepare(
		"DELETE FROM {$wpdb->postmeta}
		 WHERE post_id = %d
		 AND meta_key LIKE '_acf_wpml_v%%'",
		$new_post_id
	) );
}
add_action( 'wpml_pro_translation_completed', 'usmasmuiza_clear_acf_wpml_sync', 10, 1 );
