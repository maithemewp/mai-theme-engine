<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.1.0
 */

/**
 * Main Mai_Grid_Shortcode Class.
 *
 * @since 1.0.0
 */
final class Mai_Grid_Shortcode {

	/**
	 * Singleton
	 * @var   Mai_Grid_Shortcode The one true Mai_Grid_Shortcode
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main Mai_Grid_Shortcode Instance.
	 *
	 * Insures that only one instance of Mai_Grid_Shortcode exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @return  object | Mai_Grid_Shortcode The one true Mai_Grid_Shortcode
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Mai_Grid_Shortcode;
            // Initialize
            self::$instance->init();
		}
		return self::$instance;
	}

	function init() {
		add_shortcode( 'grid', array( $this, 'get_grid' ) );
	}

	function get_grid( $atts, $content = null ) {

		// Save original atts in a variable for filtering later
		$original_atts = $atts;

		// Pull in shortcode attributes and set defaults
		$atts = shortcode_atts( array(
			'align_cols'			=> '',
			'align_text'			=> '',
			'authors'				=> '',
			'categories'			=> '', // Comma separated category IDs
			// 'center'				=> false,
			'columns'				=> '3',
			'content'				=> 'post', // post_type name (comma separated if multiple), or taxonomy name
			'content_limit'			=> '', // Limit number of words
			'content_type'			=> '',
			'display_taxonomies'	=> '',  // Comma separated taxonomies to show terms
			'date_after'			=> '',
			'date_before'			=> '',
			'date_format'			=> '',
			'entry_class'			=> '',
			'exclude'				=> '',
			'exclude_current'		=> false,
			'grid_title'			=> '',
			'grid_title_class'		=> '',
			'grid_title_wrap'		=> 'h2',
			'gutter'				=> '30',
			'hide_empty'			=> true,
			'ids'					=> '',
			'ignore_sticky_posts'	=> false,
			'image_size'			=> 'one-third',
			'image_bg'		=> false,
			'link'					=> true,
			'meta_key'				=> '',
			'meta_value'			=> '',
			// 'middle'				=> false,
			'more_link_text'		=> apply_filters( 'mai_more_link_text', __( 'Read More', 'maitheme' ) ),
			'no_content_message'	=> '',
			'number'				=> '12',
			'offset'				=> 0,
			'order'					=> '',
			'order_by'				=> '',
			'parent'				=> '',
			'row_class'				=> '',
			'show_add_to_cart'		=> false, // Woo only
			'show_author'			=> false,
			'show_content'			=> false,
			'show_date'				=> false,
			'show_excerpt'			=> false,
			'show_image'			=> true,
			'show_more_link'		=> false,
			'show_price'			=> false, // Woo only
			'show_taxonomies'		=> false,
			'show_title'			=> true,
			'status'				=> '', // Comma separated for multiple
			'tags'					=> '', // Comma separated tag IDs
			'tax_include_children'	=> true,
			'tax_operator'			=> 'IN',
			'tax_field'				=> 'term_id',
			'taxonomy'				=> '',
			'terms'					=> '',
			'title_wrap'			=> 'h3',
			'wrapper_class'			=> '',
			'wrapper_id'			=> '',
			'slider'				=> false,
			'arrows'				=> true,  // (slider only) Whether to display arrows
			'center_mode'			=> false, // (slider only) Mobile 'peek'
			'dots'					=> false, // (slider only) Whether to display dots
			'fade'					=> false, // (slider only) Fade instead of left/right scroll (works requires slidestoshow 1)
			'infinite'				=> true,  // (slider only) Loop slider
			'slidestoscroll' 		=> 1, 	  // (slider only) The amount of posts to scroll
		), $atts, 'grid' );

		$atts = apply_filters( 'mai_grid_defaults', $atts );

		$atts = array(
			'align_cols'			=> array_map( 'sanitize_key', ( array_filter( explode( ' ', $atts['align_cols'] ) ) ) ),
			'align_text'			=> array_map( 'sanitize_key', ( array_filter( explode( ' ', $atts['align_text'] ) ) ) ),
			'authors'				=> $atts['authors'], // Validated later
			'categories'			=> array_filter( explode( ',', sanitize_text_field( $atts['categories'] ) ) ),
			// 'center'				=> filter_var( $atts['center'], FILTER_VALIDATE_BOOLEAN ),
			'columns'				=> intval( $atts['columns'] ),
			'content'				=> array_filter( explode( ',', sanitize_text_field( $atts['content'] ) ) ),
			'content_limit'			=> intval( $atts['content_limit'] ),
			'content_type'			=> sanitize_text_field( $atts['content_type'] ),
			'display_taxonomies'	=> array_filter( explode( ',', sanitize_text_field( $atts['display_taxonomies'] ) ) ),
			'date_after'			=> sanitize_text_field( $atts['date_after'] ),
			'date_before'			=> sanitize_text_field( $atts['date_before'] ),
			'date_format'			=> sanitize_text_field( $atts['date_format'] ),
			'entry_class'			=> sanitize_text_field( $atts['entry_class'] ),
			'exclude'				=> array_filter( explode( ',', sanitize_text_field( $atts['exclude'] ) ) ),
			'exclude_current'		=> filter_var( $atts['exclude_current'], FILTER_VALIDATE_BOOLEAN ),
			'grid_title'			=> sanitize_text_field( $atts['grid_title'] ),
			'grid_title_class'		=> sanitize_text_field( $atts['grid_title_class'] ),
			'grid_title_wrap'		=> sanitize_key( $atts['grid_title_wrap'] ),
			'gutter'				=> intval( $atts['gutter'] ),
			'hide_empty'			=> filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
			'ids'					=> array_filter( explode( ',', sanitize_text_field( $atts['ids'] ) ) ),
			'ignore_sticky_posts'	=> filter_var( $atts['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN ),
			'image_size'			=> sanitize_key( $atts['image_size'] ),
			'image_bg'		=> filter_var( $atts['image_bg'], FILTER_VALIDATE_BOOLEAN ),
			'link'					=> filter_var( $atts['link'], FILTER_VALIDATE_BOOLEAN ),
			'meta_key'				=> sanitize_text_field( $atts['meta_key'] ),
			'meta_value'			=> sanitize_text_field( $atts['meta_value'] ),
			// 'middle'				=> filter_var( $atts['middle'], FILTER_VALIDATE_BOOLEAN ),
			'more_link_text'		=> sanitize_text_field( $atts['more_link_text'] ),
			'no_content_message'	=> sanitize_text_field( $atts['no_content_message'] ),
			'number'				=> $atts['number'], // Validated later, after check for 'all'
			'offset'				=> intval( $atts['offset'] ),
			'order'					=> sanitize_key( $atts['order'] ),
			'order_by'				=> sanitize_key( $atts['order_by'] ),
			'parent'				=> $atts['parent'], // Validated later, after check for 'current'
			'row_class'				=> array_map( 'sanitize_html_class', ( array_filter( explode( ' ', $atts['row_class'] ) ) ) ),
			'show_add_to_cart'		=> filter_var( $atts['show_add_to_cart'], FILTER_VALIDATE_BOOLEAN ),
			'show_author'			=> filter_var( $atts['show_author'], FILTER_VALIDATE_BOOLEAN ),
			'show_content'			=> filter_var( $atts['show_content'], FILTER_VALIDATE_BOOLEAN ),
			'show_date'				=> filter_var( $atts['show_date'], FILTER_VALIDATE_BOOLEAN ),
			'show_excerpt'			=> filter_var( $atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN ),
			'show_image'			=> filter_var( $atts['show_image'], FILTER_VALIDATE_BOOLEAN ),
			'show_more_link'		=> filter_var( $atts['show_more_link'], FILTER_VALIDATE_BOOLEAN ),
			'show_price'			=> filter_var( $atts['show_price'], FILTER_VALIDATE_BOOLEAN ),
			'show_taxonomies'		=> filter_var( $atts['show_taxonomies'], FILTER_VALIDATE_BOOLEAN ),
			'show_title'			=> filter_var( $atts['show_title'], FILTER_VALIDATE_BOOLEAN ),
			'status'				=> array_filter( explode( ',', $atts['status'] ) ),
			'tags'					=> array_filter( explode( ',', sanitize_text_field( $atts['tags'] ) ) ),
			'tax_include_children'	=> filter_var( $atts['tax_include_children'], FILTER_VALIDATE_BOOLEAN ),
			'tax_operator'			=> $atts['tax_operator'], // Validated later as one of a few values
			'tax_field'				=> sanitize_key( $atts['tax_field'] ),
			'taxonomy'				=> sanitize_key( $atts['taxonomy'] ),
			'terms'					=> $atts['terms'], // Validated later, after check for 'current'
			'title_wrap'			=> sanitize_key( $atts['title_wrap'] ),
			'wrapper_class'			=> array_map( 'sanitize_html_class', ( array_filter( explode( ' ', $atts['wrapper_class'] ) ) ) ),
			'wrapper_id'			=> sanitize_html_class( $atts['wrapper_id'] ),
			'slider'				=> filter_var( $atts['slider'], FILTER_VALIDATE_BOOLEAN ),
			'arrows'				=> filter_var( $atts['arrows'], FILTER_VALIDATE_BOOLEAN ),
			'center_mode'			=> filter_var( $atts['center_mode'], FILTER_VALIDATE_BOOLEAN ),
			'dots'					=> filter_var( $atts['dots'], FILTER_VALIDATE_BOOLEAN ),
			'fade'					=> filter_var( $atts['fade'], FILTER_VALIDATE_BOOLEAN ),
			'infinite'				=> filter_var( $atts['infinite'], FILTER_VALIDATE_BOOLEAN ),
			'slidestoscroll'		=> intval( $atts['slidestoscroll'] ),
		);

		$html = '';

		// TODO: Test this!!!!!!
		// If content using this as a wrapper for [col] shortcodes
		if ( null != $content ) {
			$html .= $this->get_row_wrap_open( $atts );
			$html .= do_shortcode(trim($content));
			$html .= $this->get_row_wrap_close( $atts );
			return $html;
		}

		// Get the content type
		if ( empty( $atts['content_type'] ) ) {
			$atts['content_type'] = $this->get_content_type( $atts['content'] );
		}

		// Bail if we don't have a valid content type
		if ( empty( $atts['content_type'] ) ) {
			return;
		}

		$flex_grid = array( 'class' => 'flex-grid' );

		if ( ! empty($atts['wrapper_id']) ) {
			$flex_grid['id'] = $atts['wrapper_id'];
		}

		if ( ! empty($atts['wrapper_class']) ) {
			$flex_grid['class'] .= ' ' . implode( ' ', $atts['wrapper_class'] );
		}

	    /**
	     * Main content row wrap.
	     * With flex-row attr so devs can filter elsewhere.
	     */
	    $html .= sprintf( '<div %s>', genesis_attr( 'flex-grid', $flex_grid ) );

	        switch ( $atts['content_type'] ) {
	            case 'post':
	                $html .= $this->get_posts( $atts, $original_atts );
	                break;
	            case 'term':
	                $html .= $this->get_terms( $atts, $original_atts );
	                break;
	            // TODO: $this->get_users( $atts );
	            default:
	                $html .= '';
	                break;
	        }

        $html .= '</div>';

		return $html;

	}


	function get_content_type( $content_types ) {

		/**
		 * If types are all post types.
		 * get_post_type() on its own gets all built in and custom post types.
		 */
		if ( array_intersect( $content_types, get_post_types() ) == $content_types ) {
			return 'post'; // Means any post_type
		} else {

			$taxos = get_taxonomies( array(
			   'public' => true,
			), 'names' );

			if ( array_intersect( $content_types, $taxos ) == $content_types ) {
				return 'term';
			}

		}

		return false;

	}


	function get_posts( $atts, $original_atts ) {

		$number = $this->get_number( $atts );

		// Set up initial query for posts
		$args = array(
			'post_type'		 => $atts['content'],
			'posts_per_page' => $number,
		);

		// Authors
		if ( ! empty($atts['authors']) ) {
			if ( 'current' == $atts['authors'] && is_user_logged_in() ) {
				$args['author__in'] = get_current_user_id();
			} elseif( 'current' == $atts['authors'] ) {
				// Force an unused meta key so no results are found
				$args['meta_key'] = 'mai_no_results_abcdefg';
			} else {
				$args['author__in'] = explode( ',', sanitize_text_field( $atts['author'] ) );
			}
		}

		// Categories
		if ( ! empty($atts['categories']) ) {
			$args['category__in'] = $atts['categories'];
		}

		// Exclude
		if ( ! empty($atts['exclude']) ) {
			$args['post__not_in'] = $atts['exclude'];
		}

		// If Exclude Current
		if ( is_singular() && $atts['exclude_current'] ) {
			// If this args is already set (probably from 'exclude')
			if ( isset( $args['post__not_in'] ) ) {
				$args['post__not_in'] = array_push( $args['post__not_in'], get_the_ID() );
			} else {
				$args['post__not_in'] = array( get_the_ID() );
			}
		}

		// Post IDs
		if ( ! empty($atts['ids']) ) {
			$args['post__in'] = $atts['ids'];
		}

		// Ignore Sticky Posts
		if ( $atts['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = true;
		}

		// Order
		if ( ! empty($atts['order']) ) {
			$args['order'] = $atts['order'];
		}

		// Orderby
		if ( ! empty($atts['order_by']) ) {
			$args['orderby'] = $atts['order_by'];
		}

		// Meta key (for ordering)
		if ( ! empty( $atts['meta_key'] ) ) {
			$args['meta_key'] = $atts['meta_key'];
		}

		// Meta value (for simple meta queries)
		if ( ! empty( $atts['meta_value'] ) ) {
			$args['meta_value'] = $atts['meta_value'];
		}

		// Offset. Empty string is sanitized to zero.
		if ( $atts['offset'] > 0 ) {
			$args['offset'] = $atts['offset'];
		}

		// If post parent attribute, set up parent
		if ( ! empty($atts['parent']) ) {
			if ( is_singular() && 'current' == $atts['parent'] ) {
				$args['post_parent'] = get_the_ID();
			} else {
				$args['post_parent'] = intval( $atts['parent'] );
			}
		}

		// Status
		if ( ! empty($atts['status']) ) {
			$args['post_status'] = $atts['status'];
		}

		// Tags
		if ( ! empty($atts['tags']) ) {
			$args['tag__in'] = $atts['tags'];
		}

		// Tax query
		if ( ! empty($atts['taxonomy']) && ! empty($atts['terms']) ) {
			if ( 'current' == $atts['terms'] ) {
				$terms		= array();
				$post_terms	= wp_get_post_terms( get_the_ID(), $atts['taxonomy'] );
				if ( is_wp_error( $post_terms ) ) {
					foreach ( $post_terms as $term ) {
						$terms[] = $term->slug;
					}
				}
			} else {
				// Term string to array
				$terms = explode( ',', $atts['terms'] );
			}

			// Validate operator
			if ( ! in_array( $atts['tax_operator'], array( 'IN', 'NOT IN', 'AND' ) ) ) {
				$atts['tax_operator'] = 'IN';
			}

			$args['tax_query'] = array(
				array(
					'taxonomy'         => $atts['taxonomy'],
					'field'            => $atts['tax_field'],
					'terms'            => $terms,
					'operator'         => $atts['tax_operator'],
					'include_children' => $atts['tax_include_children'],
				)
			);
		}

		/**
		 * Filter the arguments passed to WP_Query.
		 *
		 * @param array $args          Parsed arguments to pass to WP_Query.
		 * @param array $original_atts Original attributes passed to the shortcode.
		 */
		$args = apply_filters( 'grid_shortcode_args', $args, $original_atts );

		// Get our query
		$query = new WP_Query( $args );

		// If no posts
		if ( ! $query->have_posts() ) {
			/**
			 * Filter content to display if no posts match the current query.
			 *
			 * @param string $no_posts_message Content to display, returned via {@see wpautop()}.
			 */
			return apply_filters( 'grid_shortcode_no_results', wpautop( $atts['no_content_message'] ) );
		}

		// Get it started
		$html = '';

		$html .= $this->get_grid_title( $atts );

		$html .= $this->get_row_wrap_open( $atts );

			// Loop through posts
			while ( $query->have_posts() ) : $query->the_post();

				global $post;

				$entry_header = $date = $author = $entry_meta = $entry_content = $entry_footer = $image_id = '';

				// Get image vars
				$do_image = $has_background_image = false;
				if ( $atts['show_image'] ) {
					$image_id = $this->get_image_id( $atts, $post->ID );
					if ( $image_id ) {
						$do_image = true;
						if ( $atts['image_bg'] ) {
							$has_background_image = true;
						}
					}
				}

				// Opening wrap
				$html .= $this->get_entry_wrap_open( $atts, $post, $has_background_image );

					// Set url as a variable
					$url = $this->get_entry_link( $atts, $post );


					// Image
					if ( $do_image && ! $atts['image_bg'] ) {
						if ( $image_id ) {
							$image = wp_get_attachment_image( $image_id, $atts['image_size'], false, array( 'class' => 'wp-post-image' ) );
							if ( $atts['link'] ) {
								$html .= sprintf( '<a href="%s" class="entry-image-link" title="%s">%s</a>', $url, the_title_attribute( 'echo=0' ), $image );
							} else {
								$html .= $image;
							}
						}
					}

					// Date
					if ( $atts['show_date'] ) {
						/**
						 * If date formate is set in shortcode, use that format instead of default Genesis.
						 * Since using G post_date shortcode you can also use 'relative' for '3 days ago'.
						 */
						$date_before	= $atts['date_before'] ? ' before="' . $atts['date_before'] . '"' : '';
						$date_after		= $atts['date_after'] ? ' after="' . $atts['date_after'] . '"' : '';
						$date_format	= $atts['date_format'] ? ' format="' . $atts['date_format'] . '"' : '';
						$date_shortcode	= sprintf( '[post_date%s%s%s]', $date_before, $date_after, $date_format );
						// Use Genesis output for post date
						$date = do_shortcode( $date_shortcode );
					}

					// Author
					if ( $atts['show_author'] ) {
						/**
						 * If author has no link this shortcode defaults to genesis_post_author_shortcode() [post_author]
						 */
						$author_before	  = $atts['author_before'] ? ' before="' . $atts['author_before'] . '"' : '';
						$author_after	  = $atts['author_after'] ? ' after="' . $atts['author_after'] . '"' : '';
						// Can't have a nested link if we have a background image
						if ( $has_background_image ) {
							$author_shortcode_name = 'post_author';
						} else {
							$author_shortcode_name = 'post_author_link';
						}
						$author_shortcode = sprintf( '[%s%s%s]', $author_shortcode_name, $author_before, $author_after );
						// Use Genesis output for author, including link
						$author = do_shortcode( $author_shortcode );
					}

					// Build entry meta
					if ( $date || $author ) {
						$entry_meta .= sprintf( '<p %s>%s%s</p>', genesis_attr( 'entry-meta-before-content' ), $date, $author );
					}

					// Build entry header
					if ( $atts['show_title'] || $entry_meta ) {

						$html .= sprintf( '<header %s>', genesis_attr( 'entry-header' ) );

							// Title
							if ( $atts['show_title'] ) {
								if ( $atts['link'] && ! $has_background_image ) {
									$title = sprintf( '<a href="%s" title="%s">%s</a>', $url, esc_attr( get_the_title() ), get_the_title() );
								} else {
									$title = get_the_title();
								}
								$html .= sprintf( '<%s %s>%s</%s>', $atts['title_wrap'], genesis_attr( 'entry-title' ), $title, $atts['title_wrap'] );
							}

							// Entry Meta
							if ( $entry_meta ) {
								$html .= $entry_meta;
							}

						$html .= '</header>';

					}

					// Excerpt
					if ( $atts['show_excerpt'] ) {
						$entry_content .= wpautop( strip_shortcodes( get_the_excerpt() ) );
					}

					// Content
					if ( $atts['show_content'] ) {
						$entry_content .= apply_filters( 'the_content', get_the_content() );
					}

					// Limit content. Empty string is sanitized to zero.
					if ( $atts['content_limit'] > 0 ) {
						// Reset the variable while trimming the content
						$entry_content = wpautop( wp_trim_words( $entry_content, $atts['content_limit'], '&hellip;' ) );
					}

					if ( $atts['show_price'] ) {
						ob_start();
						woocommerce_template_loop_price();
						$entry_content .= ob_get_clean();
					}

					// More link
					if ( $atts['link'] && $atts['show_more_link'] ) {
						$entry_content .= $this->get_more_link( $atts, $url, $has_background_image );
					}

					// Add to cart link
					// TODO: Test!!!!!!
					if ( $atts['show_add_to_cart'] ) {
						if ( class_exists( 'WooCommerce' ) ) {
							ob_start();
							woocommerce_template_loop_add_to_cart();
							$add_to_cart = ob_get_clean();
							$entry_content .= sprintf( '<p class="more-link-wrap">%s</p>', $add_to_cart );
						}
					}

					// Add entry content wrap if we have content
					if ( $entry_content ) {
						$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content' ), $entry_content );
					}

					// Taxonomies
					if ( $atts['display_taxonomies'] ) {

						$taxos = array_map( 'trim', explode( ',', $atts['display_taxonomies'] ) );

						foreach ( $taxos as $taxo ) {

							// Skip if post type isn't in the taxo
							if ( ! is_object_in_taxonomy( get_post_type(), $taxo ) ) {
								continue;
							}
							$terms = get_the_terms( get_the_ID(), $taxo );
							foreach ( $terms as $term ) {
								$entry_footer .= '[post_terms taxonomy="' . $tax . '" before="' . get_taxonomy($taxo)->labels->singular_name . ': "]';
							}

						}

					}

					// Entry footer
					if ( $entry_footer ) {
						$html .= sprintf( '<footer %s>%s</footer>', genesis_attr( 'entry-footer' ), $entry_footer );
					}

				$html .= $this->get_entry_wrap_close( $atts, $has_background_image );

			endwhile;
			wp_reset_postdata();

		$html .= $this->get_row_wrap_close( $atts );

		return $html;

	}


	function get_terms( $atts ) {

		$number = $this->get_number( $atts );

		// Set up initial query for terms
		$args = array(
			'hide_empty' => $atts['hide_empty'],
			'number'	 => $number,
			'taxonomy'	 => $atts['content'],
		);

		// Exclude
		if ( ! empty($atts['exclude']) ) {
			$args['exclude_tree'] = $atts['exclude'];
		}

		// Terms IDs
		if ( ! empty($atts['ids']) ) {
			$args['include'] = $atts['ids'];
		}

		// Order
		if ( ! empty($atts['order']) ) {
			$args['order'] = $atts['order'];
		}

		// Orderby
		if ( ! empty($atts['order_by']) ) {
			$args['orderby'] = $atts['order_by'];
		}

		// Meta key (for ordering)
		if ( ! empty( $atts['meta_key'] ) ) {
			$args['meta_key'] = $atts['meta_key'];
		}

		// Meta value (for simple meta queries)
		if ( ! empty( $atts['meta_value'] ) ) {
			$args['meta_value'] = $atts['meta_value'];
		}

		// Offset. Empty string is sanitized to zero.
		if ( $atts['offset'] > 0 ) {
			$args['offset'] = $atts['offset'];
		}

		// If post parent attribute, set up parent
		if ( ! empty($atts['parent']) ) {
			if ( ( is_category() || is_tag() || is_tax() ) && 'current' == $atts['parent'] ) {
				$args['parent'] = get_queried_object_id();
			} else {
				$args['parent'] = intval( $atts['parent'] );
			}
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			/**
			 * Filter content to display if no posts match the current query.
			 *
			 * @param string $no_posts_message Content to display, returned via {@see wpautop()}.
			 */
			return apply_filters( 'grid_shortcode_no_results', wpautop( $atts['no_content_message'] ) );
		}

		$html = '';

		$html .= $this->get_grid_title( $atts );

		$html .= $this->get_row_wrap_open( $atts );

			foreach ( $terms as $term ) {

				$entry_header = $date = $author = $entry_meta = $entry_content = $image_id = '';

				// Get image vars
				$do_image = $has_background_image = false;
				if ( $atts['show_image'] ) {
					$image_id = $this->get_image_id( $atts, $term->term_id );
					if ( $image_id ) {
						$do_image = true;
						if ( $atts['image_bg'] ) {
							$has_background_image = true;
						}
					}
				}

				// Opening wrap
				$html .= $this->get_entry_wrap_open( $atts, $term, $has_background_image );

					// Set url as a variable
					$url = $this->get_entry_link( $atts, $term );

					// Image
					if ( $do_image && ! $atts['image_bg'] ) {
						if ( $image_id ) {
							$image = wp_get_attachment_image( $image_id, $atts['image_size'], false, array( 'class' => 'wp-post-image' ) );
							if ( $atts['link'] ) {
								$html .= sprintf( '<a href="%s" class="entry-image-link" title="%s">%s</a>', $url, esc_attr( $term->name ), $image );
							} else {
								$html .= $image;
							}
						}
					}

					// Title
					if ( $atts['show_title'] ) {

						// Build entry header
						$html .= sprintf( '<header %s>', genesis_attr( 'entry-header' ) );

							if ( $atts['link'] && ! $has_background_image ) {
								$title = sprintf( '<a href="%s" title="%s">%s</a>', $url, esc_attr( $term->name ), $term->name );
							} else {
								$title = $term->name;
							}
							$html .= sprintf( '<%s %s>%s</%s>', $atts['title_wrap'], genesis_attr( 'entry-title' ), $title, $atts['title_wrap'] );

						$html .= '</header>';

					}

					// Excerpt/Content
					if ( $atts['show_excerpt'] || $atts['show_content'] ) {
						$entry_content .= term_description( $term->term_id, $term->taxonomy );
					}

					// Limit content. Empty string is sanitized to zero.
					if ( $atts['content_limit'] > 0 ) {
						// Reset the variable while trimming the content
						$entry_content = wpautop( wp_trim_words( $entry_content, $atts['content_limit'], '&hellip;' ) );
					}

					// More link
					if ( $atts['link'] && $atts['show_more_link'] ) {
						$entry_content .= $this->get_more_link( $atts, $url, $has_background_image );
					}

					// Add entry content wrap if we have content
					if ( $entry_content ) {
						$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content' ), $entry_content );
					}

				$html .= $this->get_entry_wrap_close( $atts, $has_background_image );

			}

		$html .= $this->get_row_wrap_close( $atts );

		return $html;

	}

	function get_grid_title( $atts ) {

		// Bail if no title
		if ( empty( $atts['grid_title'] ) ) {
			return;
		}
		$classes = 'heading ' . $atts['grid_title_class'];
		return sprintf( '<%s class="%s">%s</%s>', $atts['grid_title_wrap'], trim($classes), $atts['grid_title'], $atts['grid_title_wrap'] );
	}


	function get_row_wrap_open( $atts ) {

		$flex_row = array();

		// Main row class
	    $flex_row['class'] = 'row';

	    $is_valid_gutter = false;
	    // Gutter
	    if ( $atts['gutter'] ) {
	    	// If gutter is a valid Flexington size
			if ( in_array( $atts['gutter'], array( 5, 10, 20, 30, 40, 50 ) ) ) {
				$is_valid_gutter = true;
			}
	    }

	    // Row classes
	    if ( ! empty( $atts['row_class'] ) ) {
	    	$flex_row['class'] .= ' ' . implode( ' ', $atts['row_class'] );
	    }

	    // If posts are a slider
		if ( $atts['slider'] ) {

			// Enqueue Slick Carousel
			wp_enqueue_script( 'mai-slick' );
			wp_enqueue_script( 'mai-slick-init' );

			// Slider wrapper class
			$flex_row['class'] .= ' mai-slider';

			// TODO: center is no more!

			// Slider HTML data attributes
			$flex_row['data-arrows']		 = $atts['arrows'] ? 'true' : 'false';
			$flex_row['data-center']		 = $atts['center'] ? 'true' : 'false';
			$flex_row['data-centermode']	 = $atts['center_mode'] ? 'true' : 'false';
			$flex_row['data-dots']			 = $atts['dots'] ? 'true' : 'false';
			$flex_row['data-fade']			 = $atts['fade'] ? 'true' : 'false';
			$flex_row['data-infinite']		 = $atts['infinite'] ? 'true' : 'false';
			$flex_row['data-middle']		 = $atts['middle'] ? 'true' : 'false';
			$flex_row['data-slidestoscroll'] = $atts['slidestoscroll'];
			$flex_row['data-slidestoshow']	 = $atts['columns'];
			$flex_row['data-gutter']		 = $is_valid_gutter ? $atts['gutter'] : 0;

		}
		// Flex row classes are not on slider
		else {

			// Add gutter
	    	if ( $is_valid_gutter ) {
				$flex_row['class'] .= sprintf( ' gutter-%s', $atts['gutter'] );
		    }

		    // Align columns
		    if ( ! empty( $atts['align_cols'] ) ) {

		    	// Left
			    if ( isset( $atts['align_cols']['left'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['start-xs'];
			    }

			    // Center
			    if ( isset( $atts['align_cols']['center'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['center-xs'];
			    }

			    // Right
			    if ( isset( $atts['align_cols']['right'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['end-xs'];
			    }

			    // Top
			    if ( isset( $atts['align_cols']['top'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['top-xs'];
			    }

			    // Middle
			    if ( isset( $atts['align_cols']['middle'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['middle-xs'];
			    }

			    // Bottom
			    if ( isset( $atts['align_cols']['bottom'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['bottom-xs'];
			    }

		    }

		    // Align text
		    if ( ! empty( $atts['align_text'] ) ) {

		    	// Left
			    if ( isset( $atts['align_text']['left'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['start-xs'];
			    }

			    // Center
			    if ( isset( $atts['align_text']['center'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['center-xs'];
			    }

			    // Right
			    if ( isset( $atts['align_text']['right'] ) ) {
			    	$flex_row['class'] .= ' ' . $atts['align_cols']['end-xs'];
			    }

		    }

			// Center horizontally
			// if ( $atts['center'] ) {
			// 	$flex_row['class'] .= ' text-xs-center';
			// }

			// Center vertically
			// if ( $atts['middle'] ) {
			// 	$flex_row['class'] .= ' middle-xs';
			// }

		}

		// WooCommerce
		if ( class_exists( 'WooCommerce' ) && in_array( 'product', $atts['content'] ) ) {
			$flex_row['class'] .= ' woocommerce';
		}

	    /**
	     * Main content row wrap.
	     * With flex-row attr so devs can filter elsewhere.
	     */
	    return sprintf( '<div %s>', genesis_attr( 'flex-row', $flex_row ) );

	}

	function get_row_wrap_close( $atts ) {
		return '</div>';
	}

	function get_entry_wrap_open( $atts, $object, $has_background_image ) {

		$flex_entry = array();

		// Add href if linking element
		if ( $this->is_linking_element( $atts, $has_background_image ) ) {
			$flex_entry['href'] = $this->get_entry_link( $atts, $object );
		}

		// Set the entry classes
		$flex_entry['class'] = $this->get_entry_classes( $atts );

		if ( $atts['image_bg'] ) {
			// Get the object ID
			$object_id = $this->get_object_id( $atts, $object );
			if ( $object_id ) {
				$flex_entry = $this->add_image_bg( $flex_entry, $atts, $object_id );
			}
		}

		/**
		 * Main entry col wrap.
		 * If we use genesis_attr( 'entry' ) then it resets the classes.
		 */
		return sprintf( '<%s %s>', $this->get_entry_wrap_element( $atts, $has_background_image ), genesis_attr( 'flex-entry', $flex_entry ) );
	}

	function get_entry_wrap_close( $atts, $has_background_image ) {
		return sprintf( '</%s>', $this->get_entry_wrap_element( $atts, $has_background_image ) );
	}

	function get_entry_wrap_element( $atts, $has_background_image ) {
		return $this->is_linking_element( $atts, $has_background_image ) ? 'a' : 'div';
	}

	/**
	 * Whether the main entry element should be a link or not.
	 *
	 * @param   array  $atts  The shortcode atts
	 *
	 * @return  bool
	 */
	function is_linking_element( $atts, $has_background_image ) {
		if ( $atts['image_bg'] && $atts['link'] && $has_background_image ) {
			return true;
		}
		return false;
	}

	function get_entry_link( $atts, $object_or_id ) {
	    switch ( $atts['content_type'] ) {
	        case 'post':
	            $link = get_permalink( $object_or_id );
	            break;
	        case 'term':
	            $link = get_term_link( $object_or_id );
	            break;
	        default:
	            $link = '';
	            break;
	    }
	    return $link;
	}

	function get_entry_classes( $atts ) {
		// We need classes to be an array so we can use them in get_post_class()
		$classes = array( 'flex-entry', 'entry' );

		// Add any custom classes
		if ( $atts['entry_class'] ) {
			$classes = array_merge( $classes, explode( ' ', $atts['entry_class'] ) );
		}

		// Add Flexington columns if not a slider
		if ( ! $atts['slider'] ) {
			$classes = array_merge( $classes, explode( ' ', mai_get_flex_entry_classes_by_columns( $atts['columns'] ) ) );
		}

		// If dealing with a post object
		if ( 'post' == $atts['content_type'] ) {

			/**
			 * Remove the normal flex entry classes filter to make sure we start with a clean slate.
			 * This was an issue when adding [grid] shortcode in product_cat descriptions.
			 */
			remove_filter( 'post_class', 'mai_add_flex_entry_post_classes' );

		    /**
		     * Merge our new classes with the default WP generated classes.
		     * Also removes potential duplicate flex-entry since we need it even if slider.
		     */
			$classes = array_map( 'sanitize_html_class', get_post_class( array_unique( $classes ), get_the_ID() ) );

			// Add back the post class filter for any queried posts
			add_filter( 'post_class', 'mai_add_flex_entry_post_classes' );

		}
		// non-posts don't have post_class, so add boxed content class manually
		// elseif ( mai_is_boxed_content_enabled() ) {
		// 	$classes[] = 'boxed';
		// }

		// Turn array into a string of space separated classes
		return implode( ' ', $classes );
	}

	function get_object_id( $atts, $object ) {
	    switch ( $atts['content_type'] ) {
	        case 'post':
	            $id = $object->ID;
	            break;
	        case 'term':
	            $id = $object->term_id;
	            break;
	        case 'user':
	            $id = $object->ID;
	            break;
	        default:
	            $id = '';
	            break;
	    }
	    return $id;
	}

	/**
	 * Maybe add the featured image as inline style.
	 *
	 * @param   array  $attributes  The genesis_attr attributes
	 * @param   array  $atts        The shortcode atts
	 *
	 * @return  array              [description]
	 */
	function add_image_bg( $attributes, $atts, $object_id ) {
		// Get the image ID
		$image_id = $this->get_image_id( $atts, $object_id );
	    if ( ! $image_id ) {
	    	return $attributes;
	    }
	    $image = wp_get_attachment_image_src( $image_id, $atts['image_size'], true );
		$attributes['class']			.= ' image-bg image-bg-ar overlay light-content';
		$attributes['style']			= 'background-image: url(' . $image[0] . ');';
		$attributes['data-img-width']	= $image[1];
		$attributes['data-img-height']	= $image[2];
	    return $attributes;
	}

	/**
	 * Get a type of content main image ID.
	 * Needs to be used in the loop, so it can get the correct content type ID.
	 *
	 * @param  string  $type        The type of content, either 'post', 'term', or 'user'
	 * @param  int     $object_id   The object ID, either $post_id, $term_id, or $user_id
	 *
	 * @return int     The image ID
	 */
	function get_image_id( $atts, $object_id ) {
	    switch ( $atts['content_type'] ) {
	        case 'post':
	            $image_id = get_post_thumbnail_id( $object_id );
	            break;
	        case 'term':
	            $key = 'banner_id';
	            // If the term is a WooCommerce Product Category, change the key
	            if ( class_exists( 'WooCommerce' ) && ( 'product_cat' == $atts['taxonomy'] ) ) {
                    $key = 'thumbnail_id';
	            }
	            $image_id = get_term_meta( $object_id, $key, true );
	            break;
	        case 'user':
	            $image_id = get_user_meta( $object_id, 'banner_id', true );
	            break;
	        default:
	            $image_id = '';
	            break;
	    }
	    return $image_id;
	}

	function get_more_link( $atts, $url, $has_background_image ) {
		if ( $atts['image_bg'] && $has_background_image ) {
			$link = sprintf( '<span class="more-link">%s</span>', $atts['more_link_text'] );
		} else {
			$link = sprintf( '<a class="more-link" href="%s">%s</a>', $url, $atts['more_link_text'] );
		}
	    return sprintf( '<p class="more-link-wrap">%s</p>', $link );
	}

	/**
	 * Get the number of items to show.
	 * If all, return the appropriate value depending on content type.
	 *
	 * @param  array  $atts        The shortcode atts
	 *
	 * @return int 	  The number of items
	 */
	function get_number( $atts ) {
		if ( 'all' === $atts['number'] ) {
		    switch ( $atts['content_type'] ) {
		        case 'post':
		            $number = -1; // wp_query uses -1 for all
		            break;
		        case 'term':
		            $number = 0;  // get_terms() uses 0 for all
		            break;
		        default:
		            $number = 100; // Just to be safe, cause we may add user later
		            break;
		    }
		} else {
			$number = $atts['number'];
		}
		return intval( $number );
	}

}

/**
 * The main function for that returns Mai_Grid_Shortcode
 *
 * The main function responsible for returning the one true Mai_Grid_Shortcode
 * Instance to functions everywhere.
 *
 * @since 1.0.0
 *
 * @return object|Mai_Grid_Shortcode The one true Mai_Grid_Shortcode Instance.
 */
function Mai_Grid_Shortcode() {
	return Mai_Grid_Shortcode::instance();
}

// Get Mai_Grid_Shortcode Running.
Mai_Grid_Shortcode();
