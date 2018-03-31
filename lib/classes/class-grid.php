<?php

/**
 * Build a grid of content.
 *
 * @access  private
 */
class Mai_Grid {

	private $args;

	private $original_args;

	private $content_type;

	private $facetwp = false;

	// Whether facetwp_is_main_query filter has run.
	public static $facetwp_filter = false;

	// All displayed items incase exclude_existing is true in any instance of grid.
	public static $existing_post_ids = array();
	public static $existing_term_ids = array();

	public function __construct( $args = array() ) {

		// Save original args in a variable for filtering later.
		$this->args = $this->original_args = $args;

		// Parse defaults and args.
		$this->args = shortcode_atts( array(
			'align'                => '',  // "top, left" Comma separted. overrides align_cols and align_text for most times one setting makes sense
			'align_cols'           => '',  // "top, left" Comma separted
			'align_text'           => '',  // "center" Comma separted
			'author_after'         => '',
			'author_before'        => '',
			'authors'              => '',  // Comma separated author/user IDs
			'bottom'               => '',  // Bottom margin. 0, 5, 10, 20, 30, 40, 50, 60.
			'categories'           => '',  // Comma separated category IDs
			'columns'              => 3,
			'content'              => 'post',  // post_type name (comma separated if multiple), or taxonomy name
			'content_limit'        => '',  // Limit number of words
			'content_type'         => '',
			'date_after'           => '',
			'date_before'          => '',
			'date_format'          => '',
			'date_query_after'     => '',
			'date_query_before'    => '',
			'entry_class'          => '',
			'exclude'              => '',
			'exclude_categories'   => '',  // Comma separated category IDs
			'exclude_current'      => false,
			'exclude_existing'     => false,
			'facetwp'              => false,
			'grid_title'           => '',
			'grid_title_class'     => '',
			'grid_title_wrap'      => 'h2',
			'gutter'               => '30',
			'hide_empty'           => true,
			'ids'                  => '',
			'ignore_sticky_posts'  => true,  // normal WP_Query is false
			'image_align'          => '',
			'image_location'       => 'before_entry',
			'image_size'           => 'one-third',
			'link'                 => true,
			'meta_key'             => '',
			'meta_value'           => '',
			'more_link_text'       => __( 'Read More', 'mai-theme-engine' ),
			'no_content_message'   => '',
			'number'               => '12',
			'offset'               => '0',
			'order'                => '',
			'order_by'             => '',
			'overlay'              => '',
			'parent'               => '',
			'row_class'            => '',
			'show'                 => 'image, title',  // image, title, add_to_cart, author, content, date, excerpt, image, more_link, price, meta, title
			'status'               => '',  // Comma separated for multiple
			'tags'                 => '',  // Comma separated tag IDs
			'tax_include_children' => true,
			'tax_operator'         => 'IN',
			'tax_field'            => 'term_id',
			'taxonomy'             => '',
			'terms'                => '',  // Comma-separated or 'current'
			'title_wrap'           => 'h3',
			'class'                => '',
			'id'                   => '',
			'slider'               => false,   // (slider only) Make the columns a slider
			'arrows'               => true,    // (slider only) Whether to display arrows
			'autoplay'             => false,   // (slider only) Whether to autoplay the slider
			'center_mode'          => false,   // (slider only) Mobile 'peek'
			'dots'                 => false,   // (slider only) Whether to display dots
			'fade'                 => false,   // (slider only) Fade instead of left/right scroll (works requires slidestoshow 1)
			'infinite'             => true,    // (slider only) Loop slider
			'slidestoscroll'       => $this->get_slidestoscroll_default( $this->args ),  // (slider only) The amount of posts to scroll. Defaults to the amount of columns to show.
			'speed'                => '3000',  // (slider only) Autoplay Speed in milliseconds
		), $this->args, 'grid' );

		// Sanitize args.
		$this->args = array(
			'align'                => mai_sanitize_keys( $this->args['align'] ),
			'align_cols'           => mai_sanitize_keys( $this->args['align_cols'] ),
			'align_text'           => mai_sanitize_keys( $this->args['align_text'] ),
			'author_after'         => sanitize_key( $this->args['author_after'] ),
			'author_before'        => sanitize_key( $this->args['author_before'] ),
			'authors'              => $this->args['authors'], // Validated later
			'bottom'               => ! empty( $this->args['bottom'] ) ? absint( $this->args['bottom'] ) : '',
			'categories'           => array_filter( explode( ',', sanitize_text_field( $this->args['categories'] ) ) ),
			'columns'              => absint( $this->args['columns'] ),
			'content'              => array_filter( explode( ',', sanitize_text_field( $this->args['content'] ) ) ),
			'content_limit'        => absint( $this->args['content_limit'] ),
			'content_type'         => sanitize_text_field( $this->args['content_type'] ),
			'date_after'           => sanitize_text_field( $this->args['date_after'] ),
			'date_before'          => sanitize_text_field( $this->args['date_before'] ),
			'date_format'          => sanitize_text_field( $this->args['date_format'] ),
			'date_query_after'     => sanitize_text_field( $this->args['date_query_after'] ),
			'date_query_before'    => sanitize_text_field( $this->args['date_query_before'] ),
			'entry_class'          => sanitize_text_field( $this->args['entry_class'] ),
			'exclude'              => array_filter( explode( ',', sanitize_text_field( $this->args['exclude'] ) ) ),
			'exclude_categories'   => array_filter( explode( ',', sanitize_text_field( $this->args['exclude_categories'] ) ) ),
			'exclude_current'      => filter_var( $this->args['exclude_current'], FILTER_VALIDATE_BOOLEAN ),
			'exclude_existing'     => filter_var( $this->args['exclude_existing'], FILTER_VALIDATE_BOOLEAN ),
			'facetwp'              => filter_var( $this->args['facetwp'], FILTER_VALIDATE_BOOLEAN ),
			'grid_title'           => sanitize_text_field( $this->args['grid_title'] ),
			'grid_title_class'     => sanitize_text_field( $this->args['grid_title_class'] ),
			'grid_title_wrap'      => sanitize_key( $this->args['grid_title_wrap'] ),
			'gutter'               => mai_is_valid_gutter( absint( $this->args['gutter'] ) ) ? absint( $this->args['gutter'] ) : 30,
			'hide_empty'           => filter_var( $this->args['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
			'ids'                  => array_filter( array_map( 'absint', explode( ',', sanitize_text_field( $this->args['ids'] ) ) ) ),
			'ignore_sticky_posts'  => filter_var( $this->args['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN ),
			'image_align'          => sanitize_key( $this->args['image_align'] ),
			'image_location'       => sanitize_key( $this->args['image_location'] ),
			'image_size'           => sanitize_key( $this->args['image_size'] ),
			'link'                 => filter_var( $this->args['link'], FILTER_VALIDATE_BOOLEAN ),
			'meta_key'             => sanitize_text_field( $this->args['meta_key'] ),
			'meta_value'           => sanitize_text_field( $this->args['meta_value'] ),
			'more_link_text'       => sanitize_text_field( $this->args['more_link_text'] ),
			'no_content_message'   => sanitize_text_field( $this->args['no_content_message'] ),
			'number'               => $this->args['number'], // Validated later, after check for 'all'
			'offset'               => absint( $this->args['offset'] ),
			'order'                => sanitize_key( $this->args['order'] ),
			'order_by'             => sanitize_key( $this->args['order_by'] ),
			'overlay'              => sanitize_key( $this->args['overlay'] ),
			'parent'               => $this->args['parent'], // Validated later, after check for 'current'
			'row_class'            => mai_sanitize_html_classes( $this->args['row_class'] ),
			'show'                 => mai_sanitize_keys( $this->args['show'] ),
			'status'               => array_filter( explode( ',', $this->args['status'] ) ),
			'tags'                 => array_filter( explode( ',', sanitize_text_field( $this->args['tags'] ) ) ),
			'tax_include_children' => filter_var( $this->args['tax_include_children'], FILTER_VALIDATE_BOOLEAN ),
			'tax_operator'         => $this->args['tax_operator'], // Validated later as one of a few values
			'tax_field'            => sanitize_key( $this->args['tax_field'] ),
			'taxonomy'             => sanitize_key( $this->args['taxonomy'] ),
			'terms'                => $this->args['terms'], // Validated later, after check for 'current'
			'title_wrap'           => sanitize_key( $this->args['title_wrap'] ),
			'class'                => mai_sanitize_html_classes( $this->args['class'] ),
			'id'                   => sanitize_html_class( $this->args['id'] ),
			'slider'               => filter_var( $this->args['slider'], FILTER_VALIDATE_BOOLEAN ),
			'arrows'               => filter_var( $this->args['arrows'], FILTER_VALIDATE_BOOLEAN ),
			'autoplay'             => filter_var( $this->args['autoplay'], FILTER_VALIDATE_BOOLEAN ),
			'center_mode'          => filter_var( $this->args['center_mode'], FILTER_VALIDATE_BOOLEAN ),
			'dots'                 => filter_var( $this->args['dots'], FILTER_VALIDATE_BOOLEAN ),
			'fade'                 => filter_var( $this->args['fade'], FILTER_VALIDATE_BOOLEAN ),
			'infinite'             => filter_var( $this->args['infinite'], FILTER_VALIDATE_BOOLEAN ),
			'slidestoscroll'       => absint( $this->args['slidestoscroll'] ),
			'speed'                => absint( $this->args['speed'] ),
		);

		// Get the content type.
		$this->content_type = $this->get_content_type();

	}

	/**
	 * Return the grid HTML.
	 *
	 * @return  string|HTML
	 */
	function render() {

		// Bail if we don't have a valid content type.
		if ( empty( $this->content_type ) ) {
			return;
		}

		switch ( $this->content_type ) {
			case 'post':
				$content = $this->get_posts();
			break;
			case 'term':
				// $content = $this->get_terms( $atts, $original_args );
			break;
			// TODO: $this->get_users( $atts );
			default:
				$content = '';
		}

		// Bail if no content.
		if ( ! $content ) {
			return '';
		}

		// If this is a facetwp grid, filter the main query.
		if ( $this->facetwp ) {
			// If the filter hasn't run yet.
			if ( ! $this::$facetwp_filter ) {
				/**
				 * Set it as the main query.
				 * @link  https://facetwp.com/documentation/facetwp_is_main_query/
				 */
				add_filter( 'facetwp_is_main_query', array( $this, 'facetwp_is_main_query' ), 10, 2 );
				// Set the filter flag so this filter doesn't run more than once.
				$this::$facetwp_filter = true;
			}
		}

		// Set attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['class'], 'flex-grid' ),
			'id'    => ! empty( $this->args['id'] ) ? $this->args['id'] : '',
		);

		return sprintf( '<div %s>%s</div>', genesis_attr( 'flex-grid', $attributes, $this->args ), $content );
	}

	/**
	 * Get the grid posts loop.
	 *
	 * @return  string|HTML
	 */
	function get_posts() {

		// Set up initial query for posts.
		$query_args = array(
			'post_type'           => $this->args['content'],
			'posts_per_page'      => $this->get_number(),
			'ignore_sticky_posts' => $this->args['ignore_sticky_posts'],
		);

		// Authors.
		if ( ! empty( $this->args['authors'] ) ) {
			if ( 'current' === $this->args['authors'] && is_user_logged_in() ) {
				$query_args['author__in'] = get_current_user_id();
			} elseif( 'current' === $this->args['authors'] ) {
				// Force an unused meta key so no results are found.
				$query_args['meta_key'] = 'mai_no_results_abcdefg';
			} else {
				$query_args['author__in'] = explode( ',', sanitize_text_field( $this->args['authors'] ) );
			}
		}

		// Categories.
		if ( ! empty( $this->args['categories'] ) ) {
			$query_args['category__in'] = $this->args['categories'];
		}

		// Date query.
		if ( ! empty( $this->args['date_query_after'] ) || ! empty( $this->args['date_query_before'] ) ) {
			$query_args['date_query'] = array();
			if ( ! empty( $this->args['date_query_after'] ) ) {
				$query_args['date_query']['after'] = $this->args['date_query_after'];
			}
			if ( ! empty( $this->args['date_query_before'] ) ) {
				$query_args['date_query']['before'] = $this->args['date_query_before'];
			}
		}

		// Exclude posts.
		if ( ! empty( $this->args['exclude'] ) ) {
			$query_args['post__not_in'] = $this->args['exclude'];
		}

		// Exclude existing.
		if ( $this->args['exclude_existing'] && ! empty( $this::$existing_post_ids ) ) {
			if ( isset( $query_args['post__not_in'] ) ) {
				$query_args['post__not_in'] = array_push( $query_args['post__not_in'], $this::$existing_post_ids );
			} else {
				$query_args['post__not_in'] = $this::$existing_post_ids;
			}
		}

		// Categories.
		if ( ! empty( $this->args['exclude_categories'] ) ) {
			$query_args['category__not_in'] = $this->args['exclude_categories'];
		}

		// If exclude current.
		if ( is_singular() && $this->args['exclude_current'] ) {
			// If this query_args is already set.
			if ( isset( $query_args['post__not_in'] ) ) {
				$query_args['post__not_in'] = array_push( $query_args['post__not_in'], get_the_ID() );
			} else {
				$query_args['post__not_in'] = array( get_the_ID() );
			}
		}

		// Post IDs.
		if ( ! empty( $this->args['ids'] ) ) {
			$query_args['post__in'] = $this->args['ids'];
		}

		// Order.
		if ( ! empty( $this->args['order'] ) ) {
			$query_args['order'] = $this->args['order'];
		}

		// Orderby.
		if ( ! empty( $this->args['order_by'] ) ) {
			$query_args['orderby'] = $this->args['order_by'];
		}

		// Meta key (for ordering).
		if ( ! empty( $this->args['meta_key'] ) ) {
			$query_args['meta_key'] = $this->args['meta_key'];
		}

		// Meta value (for simple meta queries).
		if ( ! empty( $this->args['meta_value'] ) ) {
			$query_args['meta_value'] = $this->args['meta_value'];
		}

		// Offset. Empty string is sanitized to zero.
		if ( $this->args['offset'] > 0 ) {
			$query_args['offset'] = $this->args['offset'];
		}

		// If post parent attribute, set up parent. Can't check empty() cause may pass 0 or '0';
		if ( '' !== $this->args['parent'] ) {
			if ( is_singular() && 'current' == $this->args['parent'] ) {
				$query_args['post_parent'] = get_the_ID();
			} else {
				$query_args['post_parent'] = intval( $this->args['parent'] );
			}
		}

		// Status.
		if ( ! empty( $this->args['status'] ) ) {
			$query_args['post_status'] = $this->args['status'];
		}

		// Tags.
		if ( ! empty( $this->args['tags'] ) ) {
			$query_args['tag__in'] = $this->args['tags'];
		}

		// Tax query.
		if ( ! empty( $this->args['taxonomy'] ) && ! empty( $this->args['terms'] ) ) {
			if ( 'current' === $this->args['terms'] ) {
				$terms      = array();
				$post_terms = wp_get_post_terms( get_the_ID(), $this->args['taxonomy'] );
				if ( ! is_wp_error( $post_terms ) ) {
					foreach ( $post_terms as $term ) {
						// Get the form by type.
						switch ( $this->args['tax_field'] ) {
							case 'slug':
							$terms[] = $term->slug;
							break;
							case 'term_id':
							$terms[] = $term->term_id;
							break;
						}
					}
				}
			} else {
				// Term string to array.
				$terms = explode( ',', $this->args['terms'] );
			}

			// Force a valid operator.
			if ( ! in_array( $this->args['tax_operator'], array( 'IN', 'NOT IN', 'AND' ) ) ) {
				$this->args['tax_operator'] = 'IN';
			}

			$query_args['tax_query'] = array(
				array(
					'taxonomy'         => $this->args['taxonomy'],
					'field'            => $this->args['tax_field'],
					'terms'            => $terms,
					'operator'         => $this->args['tax_operator'],
					'include_children' => $this->args['tax_include_children'],
				)
			);
		}

		// FacetWP support.
		if ( isset( $this->args['facetwp'] ) && $this->args['facetwp'] ) {
			$this->facetwp = $query_args['facetwp'] = true;
		}

		/**
		 * Filter the arguments passed to WP_Query.
		 *
		 * @param   array  $query_args     Parsed arguments to pass to WP_Query.
		 * @param   array  $args           The current grid args.
		 * @param   array  $original_args  The original grid args.
		 *
		 * @return  array  The args.
		 */
		$query_args = apply_filters( 'mai_grid_args', $query_args, $this->args, $this->original_args );

		// Get our query.
		$query = new WP_Query( $query_args );

		// If posts.
		if ( $query->have_posts() ) {

			// Get it started.
			$html = '';

			$html .= $this->get_grid_title();

			$html .= $this->get_row_wrap_open();

				// Loop through posts.
				while ( $query->have_posts() ) : $query->the_post();

					// Add this post to the existing post IDs.
					$this::$existing_post_ids[] = get_the_ID();

					global $post;

					$image_html = $entry_header = $date = $author = $entry_meta = $entry_content = $entry_footer = $image_id = '';

					// Get image vars.
					$do_image = $has_bg_image = false;

					// If showing image, set some helper variables.
					if ( in_array( 'image', $this->args['show'] ) ) {
						$image_id = $this->get_image_id( get_the_ID() );
						if ( $image_id ) {
							$do_image = true;
							if ( $this->is_bg_image() ) {
								$has_bg_image = true;
							}
						}
					}

					// Opening wrap.
					$html .= $this->get_entry_wrap_open( $post, $has_bg_image );

						// Set url as a variable.
						$url = $this->get_entry_link( $post );

						// Build the image html.
						if ( $do_image && ! $this->is_bg_image() ) {
							$image_html = $this->get_image_html( $image_id, $url, the_title_attribute( 'echo=0' ) );
						}

						// Image.
						if ( 'before_entry' === $this->args['image_location'] ) {
							$html .= $image_html;
						}

						// Date.
						if ( in_array( 'date', $this->args['show'] ) ) {
							/**
							 * If date format is set in shortcode, use that format instead of default Genesis.
							 * Since using G post_date shortcode you can also use 'relative' for '3 days ago'.
							 */
							$date_before    = $this->args['date_before'] ? ' before="' . str_replace( ' ', '&nbsp;', $this->args['date_before'] ) . '"' : '';
							$date_after     = $this->args['date_after'] ? ' after="' . str_replace( ' ', '&nbsp;', $this->args['date_after'] ) . '"' : '';
							$date_format    = $this->args['date_format'] ? ' format="' . $this->args['date_format'] . '"' : '';
							$date_shortcode = sprintf( '[post_date%s%s%s]', $date_before, $date_after, $date_format );
							// Use Genesis output for post date.
							$date = do_shortcode( $date_shortcode );
						}

						// Author.
						if ( in_array( 'author', $this->args['show'] ) ) {
							/**
							 * If author has no link this shortcode defaults to genesis_post_author_shortcode() [post_author].
							 */
							$author_before = $this->args['author_before'] ? ' before="' . str_replace( ' ', '&nbsp;', $this->args['author_before'] ) . '"' : '';
							$author_after  = $this->args['author_after'] ? ' after="' . str_replace( ' ', '&nbsp;', $this->args['author_after'] ) . '"' : '';
							// Can't have a nested link if we have a background image.
							if ( $has_bg_image ) {
								$author_shortcode_name = 'post_author';
							} else {
								$author_shortcode_name = 'post_author_link';
							}
							$author_shortcode = sprintf( '[%s%s%s]', $author_shortcode_name, $author_before, $author_after );
							// Use Genesis output for author, including link.
							$author = do_shortcode( $author_shortcode );
						}

						// Build entry meta.
						if ( $date || $author ) {
							$entry_meta .= sprintf( '<p %s>%s%s</p>', genesis_attr( 'entry-meta-before-content', array(), $this->args ), $date, $author );
						}

						// Build entry header.
						if ( $this->is_entry_header_image() || in_array( 'title', $this->args['show'] ) || $entry_meta ) {

							// Image.
							if ( 'before_title' === $this->args['image_location'] ) {
								$entry_header .= $image_html;
							}

							// Title.
							if ( in_array( 'title', $this->args['show'] ) ) {
								if ( $this->args['link'] ) {
									$title = sprintf( '<a href="%s" title="%s">%s</a>', $url, esc_attr( get_the_title() ), get_the_title() );
								} else {
									$title = get_the_title();
								}
								$entry_header .= sprintf( '<%s %s>%s</%s>', $this->args['title_wrap'], genesis_attr( 'entry-title', array(), $this->args ), $title, $this->args['title_wrap'] );
							}

							// Image.
							if ( 'after_title' === $this->args['image_location'] ) {
								$entry_header .= $image_html;
							}

							// Entry Meta.
							if ( $entry_meta ) {
								$entry_header .= $entry_meta;
							}

						}

						// Add filter to the entry header.
						$entry_header = apply_filters( 'mai_flex_entry_header', $entry_header, $this->args, $this->original_args );

						// Add entry header wrap if we have content.
						if ( $entry_header ) {
							$html .= sprintf( '<header %s>%s</header>', genesis_attr( 'entry-header', array(), $this->args ), $entry_header );
						}

						// Excerpt.
						if ( in_array( 'excerpt', $this->args['show'] ) ) {
							// Strip tags and shortcodes cause things go nuts, especially if showing image as background
							$entry_content .= wpautop( wp_strip_all_tags( strip_shortcodes( get_the_excerpt() ) ) );
						}

						// Content.
						if ( in_array( 'content', $this->args['show'] ) ) {
							$entry_content .= wp_strip_all_tags( strip_shortcodes( get_the_content() ) );
						}

						// Limit content. Empty string is sanitized to zero.
						if ( $this->args['content_limit'] > 0 ) {
							// Reset the variable while trimming the content.
							$entry_content = wpautop( wp_trim_words( $entry_content, $this->args['content_limit'], '&hellip;' ) );
						}

						// Price.
						if ( in_array( 'price', $this->args['show'] ) ) {
							ob_start();
							woocommerce_template_loop_price();
							$entry_content .= ob_get_clean();
						}

						// Image. This runs at the end because the image was getting stripped content_limit was too low.
						if ( 'before_content' === $this->args['image_location'] ) {
							$entry_content = $image_html . $entry_content;
						}

						// Add filter to the entry content, before more link.
						$entry_content = apply_filters( 'mai_flex_entry_content', $entry_content, $this->args, $this->original_args );

						// More link.
						if ( $this->args['link'] && in_array( 'more_link', $this->args['show'] ) ) {
							$entry_content .= mai_get_read_more_link( $post, $this->args['more_link_text'], 'post' );
						}

						// Add to cart link.
						if ( $this->args['link'] && in_array( 'add_to_cart', $this->args['show'] ) ) {
							$entry_content .= $this->get_add_to_cart_link();
						}

						// Add entry content wrap if we have content.
						if ( $entry_content ) {
							$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content', array(), $this->args ), $entry_content );
						}

						// Meta.
						if ( in_array( 'meta', $this->args['show'] ) ) {
							$entry_footer = mai_get_the_posts_meta( get_the_ID() );
						}

						// Add filter to the entry footer.
						$entry_footer = apply_filters( 'mai_flex_entry_footer', $entry_footer, $this->args, $this->original_args );

						// Entry footer.
						if ( $entry_footer ) {
							$html .= sprintf( '<footer %s>%s</footer>', genesis_attr( 'entry-footer', array(), $this->args ), $entry_footer );
						}

						// Image.
						if ( ( 'bg' == $this->args['image_location'] ) && $this->args['link'] ) {
							$html .= mai_get_bg_image_link( $url, get_the_title() );
						}

					$html .= $this->get_entry_wrap_close();

				endwhile;

				// Clear duplicate IDs.
				$this::$existing_post_ids = array_unique( $this::$existing_post_ids );

			$html .= $this->get_row_wrap_close();

		}

		// No posts.
		else {
			/**
			 * Filter content to display if no posts match the current query.
			 *
			 * @param string $no_posts_message Content to display, returned via {@see wpautop()}.
			 */
			$html = apply_filters( 'mai_grid_no_results', wpautop( $this->args['no_content_message'] ) );
		}

		wp_reset_postdata();
		return $html;
	}

	/**
	 * Get the grid terms loop.
	 *
	 * @return  string|HTML
	 */
	function get_terms() {

		// Set up initial query for terms.
		$query_args = array(
			'hide_empty' => $this->args['hide_empty'],
			'number'     => $this->get_number(),
			'taxonomy'   => $this->args['content'],
		);

		// Exclude.
		if ( ! empty( $this->args['exclude'] ) ) {
			$query_args['exclude_tree'] = $this->args['exclude'];
		}

		// Terms IDs,
		if ( ! empty( $this->args['ids'] ) ) {
			$query_args['include'] = $this->args['ids'];
		}

		// Order.
		if ( ! empty( $this->args['order'] ) ) {
			$query_args['order'] = $this->args['order'];
		}

		// Orderby.
		if ( ! empty( $this->args['order_by'] ) ) {
			$query_args['orderby'] = $this->args['order_by'];
		}

		// Meta key (for ordering).
		if ( ! empty( $this->args['meta_key'] ) ) {
			$query_args['meta_key'] = $this->args['meta_key'];
		}

		// Meta value (for simple meta queries).
		if ( ! empty( $this->args['meta_value'] ) ) {
			$query_args['meta_value'] = $this->args['meta_value'];
		}

		// Offset. Empty string is sanitized to zero.
		if ( $this->args['offset'] > 0 ) {
			$query_args['offset'] = $this->args['offset'];
		}

		// If post parent attribute, set up parent. Can't check empty() cause may pass 0 or '0';
		if ( '' !== $this->args['parent'] ) {
			if ( ( is_category() || is_tag() || is_tax() ) && 'current' === $this->args['parent'] ) {
				$query_args['parent'] = get_queried_object_id();
			} else {
				$query_args['parent'] = intval( $this->args['parent'] );
			}
		}

		/**
		 * Filter the arguments passed to WP_Query.
		 *
		 * @param   array  $query_args     Parsed arguments to pass to WP_Query.
		 * @param   array  $args           The current grid args.
		 * @param   array  $original_args  The original grid args.
		 *
		 * @return  array  The args.
		 */
		$query_args = apply_filters( 'mai_grid_args', $query_args, $this->args, $this->original_args );

		// Get our query.
		$terms = get_terms( $query_args );

		// If terms and not an error.
		if ( $terms && ! is_wp_error( $terms ) ) {

			// Get it started.
			$html = '';

			$html .= $this->get_grid_title();

			$html .= $this->get_row_wrap_open();

				// Loop through terms.
				foreach ( $terms as $term ) {

					// Add this term to the existing term IDs.
					$this::$existing_term_ids[] = $term->term_id;

					$image_html = $entry_header = $entry_content = $entry_footer = $image_id = '';

					// Get image vars.
					$do_image = $has_bg_image = false;

					// If showing image, set some helper variables.
					if ( in_array( 'image', $this->args['show'] ) ) {
						$image_id = $this->get_image_id( $term->term_id );
						if ( $image_id ) {
							$do_image = true;
							if ( $this->is_bg_image() ) {
								$has_bg_image = true;
							}
						}
					}

					// Opening wrap.
					$html .= $this->get_entry_wrap_open( $term, $has_bg_image );

						// Set url as a variable.
						$url = $this->get_entry_link( $term );

						// Build the image html.
						if ( $do_image && ! $this->is_bg_image() ) {
							$image_html = $this->get_image_html( $image_id, $url, esc_attr( $term->name ) );
						}

						// Image.
						if ( 'before_entry' === $this->args['image_location'] ) {
							$html .= $image_html;
						}

						// Build entry header.
						if ( $this->is_entry_header_image() || in_array( 'title', $this->args['show'] ) ) {

							// Image.
							if ( 'before_title' === $this->args['image_location'] ) {
								$entry_header .= $image_html;
							}

							// Title.
							if ( in_array( 'title', $this->args['show'] ) ) {
								if ( $this->args['link'] ) {
									$title = sprintf( '<a href="%s" title="%s">%s</a>', $url, esc_attr( $term->name ), $term->name );
								} else {
									$title = $term->name;
								}
								$entry_header .= sprintf( '<%s %s>%s</%s>', $this->args['title_wrap'], genesis_attr( 'entry-title', array(), $this->args ), $title, $this->args['title_wrap'] );
							}

							// Image.
							if ( 'after_title' === $this->args['image_location'] ) {
								$entry_header .= $image_html;
							}

						}

						// Add filter to the entry header.
						$entry_header = apply_filters( 'mai_flex_entry_header', $entry_header, $this->args, $this->original_args );

						// Add entry header wrap if we have content.
						if ( $entry_header ) {
							$html .= sprintf( '<header %s>%s</header>', genesis_attr( 'entry-header', array(), $this->args ), $entry_header );
						}

						// Excerpt/Content.
						if ( in_array( 'excerpt', $this->args['show'] ) || in_array( 'content', $this->args['show'] ) ) {
							$entry_content .= wpautop( wp_strip_all_tags( strip_shortcodes( term_description( $term->term_id, $term->taxonomy ) ) ) );
						}

						// Limit content. Empty string is sanitized to zero.
						if ( $this->args['content_limit'] > 0 ) {
							// Reset the variable while trimming the content.
							$entry_content = wpautop( wp_trim_words( $entry_content, $this->args['content_limit'], '&hellip;' ) );
						}

						// Image. This runs at the end because the image was getting stripped content_limit was too low.
						if ( 'before_content' === $this->args['image_location'] ) {
							$entry_content = $image_html . $entry_content;
						}

						// Add filter to the entry content, before more link.
						$entry_content = apply_filters( 'mai_flex_entry_content', $entry_content, $this->args, $this->original_args );

						// More link
						if ( $this->args['link'] && in_array( 'more_link', $this->args['show'] ) ) {
							$entry_content .= mai_get_read_more_link( $term, $this->args['more_link_text'], 'term' );
						}

						// Add entry content wrap if we have content
						if ( $entry_content ) {
							$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content', array(), $this->args ), $entry_content );
						}

						// Add filter to the entry footer.
						$entry_footer = apply_filters( 'mai_flex_entry_footer', $entry_footer, $this->args, $this->original_args );

						// Entry footer.
						if ( $entry_footer ) {
							$html .= sprintf( '<footer %s>%s</footer>', genesis_attr( 'entry-footer', array(), $this->args ), $entry_footer );
						}

						// Image.
						if ( ( 'bg' == $this->args['image_location'] ) && $this->args['link'] ) {
							$html .= mai_get_bg_image_link( $url, $term->name );
						}

					$html .= $this->get_entry_wrap_close();

				}

				// Clear duplicate IDs.
				$this::$existing_term_ids = array_unique( $this::$existing_term_ids );

			$html .= $this->get_row_wrap_close();

		}

		// No terms.
		else {
			/**
			 * Filter content to display if no posts match the current query.
			 *
			 * @param string $no_posts_message Content to display, returned via {@see wpautop()}.
			 */
			$html = apply_filters( 'mai_grid_no_results', wpautop( $this->args['no_content_message'] ) );
		}

		return $html;
	}

	/**
	 * Get the grid title.
	 *
	 * @return  string|HTML
	 */
	function get_grid_title() {
		// Bail if no title
		if ( empty( $this->args['grid_title'] ) ) {
			return;
		}
		$classes = 'heading';
		$classes = mai_add_classes( $this->args['grid_title_class'], $classes );
		return sprintf( '<%s class="%s">%s</%s>', $this->args['grid_title_wrap'], trim($classes), $this->args['grid_title'], $this->args['grid_title_wrap'] );
	}

	/**
	 * Get the content type.
	 *
	 * @return  string|false
	 */
	function get_content_type() {
		// If we already have a value.
		if ( ! empty( $this->args['content_type'] ) ) {
			return $this->args['content_type'];
		}
		// If types are all post types. get_post_type() gets all built in and custom post types.
		if ( array_intersect( $this->args['content'], get_post_types() ) == $this->args['content'] ) {
			return 'post'; // Means any post_type
		}
		// Get public taxonomies.
		$taxos = get_taxonomies( array(
			'public' => true,
		), 'names' );
		// If types are all taxonomies.
		if ( array_intersect( $this->args['content'], $taxos ) == $this->args['content'] ) {
			return 'term';
		}
		// Nada.
		return false;
	}

	/**
	 * Get the grid row opening HTML.
	 *
	 * @return  string|HTML
	 */
	function get_row_wrap_open() {

		// Row attributes.
		$attributes = array(
			'class' => mai_add_classes( $this->args['row_class'], 'row' ),
		);

		// FacetWP support.
		if ( $this->args['facetwp'] ) {
			$attributes['class'] = mai_add_classes( 'facetwp-template', $attributes['class'] );
		}

		// If slider.
		if ( $this->args['slider'] ) {

			// Enqueue Slick Carousel.
			wp_enqueue_script( 'mai-slick' );
			wp_enqueue_script( 'mai-slick-init' );

			// Slider wrapper class.
			$attributes['class'] = mai_add_classes( 'mai-slider', $attributes['class'] );

			// Slider HTML data attributes.
			$attributes = $this->add_slider_data_attributes( $attributes );

		}
		// Not on slider.
		else {

			// Add gutter.
			$attributes['class'] = mai_add_classes( sprintf( 'gutter-%s', $this->args['gutter'] ), $attributes['class'] );

			// Add row align classes.
			$attributes['class'] = mai_add_align_classes_row( $attributes['class'], $this->args );

		}

		// WooCommerce.
		if ( class_exists( 'WooCommerce' ) && in_array( 'product', $this->args['content'] ) ) {
			$attributes['class'] .= ' woocommerce';
		}

		// Bring it home.
		return sprintf( '<div %s>', genesis_attr( 'flex-row', $attributes, $this->args ) );
	}

	/**
	 * Get the grid row closing HTML.
	 *
	 * @return  string|HTML
	 */
	function get_row_wrap_close() {
		return '</div>';
	}

	/**
	 * Get the grid entry opening HTML.
	 *
	 * @return  string|HTML
	 */
	function get_entry_wrap_open( $object, $has_bg_image ) {

		// Set the entry classes.
		$attributes = array(
			'class' => mai_add_classes( $this->get_entry_classes() ),
		);

		// Add the align classes.
		$attributes['class'] = mai_add_classes( $this->get_entry_align_classes(), $attributes['class'] );

		$light_content = false;
		$valid_overlay = mai_is_valid_overlay( $this->args['overlay'] );

		if ( $this->is_bg_image() ) {

			// Get the object ID.
			$object_id = $this->get_object_id( $object );

			if ( $object_id ) {

				// Add background image with aspect ratio attributes.
				$attributes = mai_add_background_image_attributes( $attributes, $this->get_image_id( $object_id ), $this->args['image_size'] );

				if ( $has_bg_image ) {

					$light_content = true;

					// Set dark overlay if we don't have one.
					$this->args['overlay'] = ! $valid_overlay ? 'dark' : $this->args['overlay'];
				}
			}

			if ( $this->has_bg_link() ) {

				// Add has-bg-link class.
				$attributes['class'] .= ' has-bg-link';
			}
		}

		if ( $valid_overlay ) {

			// If we have a dark overlay, content is light.
			if ( 'dark' === $this->args['overlay'] ) {
				$light_content = true;
			}

			// Add overlay classes.
			$attributes['class'] = mai_add_overlay_classes( $attributes['class'], $this->args['overlay'] );
		}

		// Shade class
		$attributes['class'] .= $light_content ? ' light-content' : '';

		/**
		 * Main entry col wrap.
		 * If we use genesis_attr( 'entry' ) then it resets the classes.
		 */
		return sprintf( '<div %s>', genesis_attr( 'flex-entry', $attributes, $this->args ) );
	}

	/**
	 * Get the grid entry closing HTML.
	 *
	 * @return  string|HTML
	 */
	function get_entry_wrap_close() {
		return '</div>';
	}

	/**
	 * Add slider data attributes to the attributes array.
	 *
	 * @param   array  $attributes  The existing attributes array.
	 *
	 * @return  array  The modified $attributes.
	 */
	function add_slider_data_attributes( $attributes ) {
		$attributes['data-arrows']         = $this->args['arrows'] ? 'true' : 'false';
		$attributes['data-autoplay']       = $this->args['autoplay'] ? 'true' : 'false';
		$attributes['data-center']         = in_array( 'center', $this->args['align'] ) ? 'true' : 'false';
		$attributes['data-centermode']     = $this->args['center_mode'] ? 'true' : 'false';
		$attributes['data-dots']           = $this->args['dots'] ? 'true' : 'false';
		$attributes['data-fade']           = $this->args['fade'] ? 'true' : 'false';
		$attributes['data-infinite']       = $this->args['infinite'] ? 'true' : 'false';
		$attributes['data-middle']         = in_array( 'middle', $this->args['align'] ) ? 'true' : 'false';
		$attributes['data-slidestoscroll'] = $this->args['slidestoscroll'];
		$attributes['data-slidestoshow']   = $this->args['columns'];
		$attributes['data-speed']          = $this->args['speed'];
		$attributes['data-gutter']         = $this->args['gutter'];
		return $attributes;
	}

	/**
	 * Get default slidestoscroll.
	 * First check slidestoscroll, then columns, then fallback to default.
	 *
	 * Can't use $this->args cause not set yet. This is for defaults.
	 *
	 * @param   array  $args  The initial grid args.
	 *
	 * @return  string|int  The amount of slides to scroll. Sanitized later.
	 */
	function get_slidestoscroll_default( $args ) {
		$slidestoscroll = 3;
		if ( isset( $args['slidestoscroll'] ) ) {
			$slidestoscroll = $args['slidestoscroll'];
		} elseif ( isset( $args['columns'] ) ) {
			$slidestoscroll = $args['columns'];
		}
		return $slidestoscroll;
	}

	/**
	 * Get the flex entry classes.
	 *
	 * @return  string  The HTML ready classes.
	 */
	function get_entry_classes() {

		// We need classes to be an array so we can use them in get_post_class().
		$classes = array( 'flex-entry', 'entry' );

		// If image is not aligned.
		if ( $this->args['image_align'] ) {
			$classes[] = 'image-' . $this->args['image_align'];
		}

		// Add bottom margin classes.
		if ( ! empty( $this->args['bottom'] ) ) {
			$bottom = mai_get_bottom_class( $this->args['bottom'] );
			if ( $bottom ) {
				$classes[] = $bottom;
			}
		}

		// Add any custom classes.
		if ( $this->args['entry_class'] ) {
			$classes = array_merge( $classes, explode( ' ', $this->args['entry_class'] ) );
		}

		// If not a slider.
		if ( ! $this->args['slider'] ) {
			// Add Flexington columns.
			$classes = array_merge( $classes, explode( ' ', mai_get_flex_entry_classes_by_columns( $this->args['columns'] ) ) );
		} else {
			// Add slide class.
			$classes[] = 'mai-slide';
		}

		// If dealing with a post object.
		if ( 'post' === $this->content_type ) {

			/**
			 * Merge our new classes with the default WP generated classes.
			 * Also removes potential duplicate flex-entry since we need it even if slider.
			 */
			$classes = get_post_class( $classes, get_the_ID() );
		}

		// Remove duplicates and sanitize.
		$classes = array_map( 'sanitize_html_class', array_unique( $classes ) );

		// Turn array into a string of space separated classes
		return implode( ' ', $classes );
	}

	/**
	 * Get the flex entry align classes.
	 * We can't use get_align_classes() method since this may add 'column'
	 * which reverses the left/top center/middle right/bottom directional classes.
	 *
	 * @return  string  The HTML ready classes.
	 */
	function get_entry_align_classes() {

		$classes = '';

		/**
		 * "align" takes precendence over "align_cols" and "align_text".
		 * "align" forces the text to align along with the cols.
		 */
		if ( ! empty( $this->args['align'] ) ) {

			// If image is bg or not aligned.
			if ( 'bg' === $this->args['image_location'] || empty( $this->args['image_align'] ) ) {
				$classes = 'column';
				$classes = mai_add_align_classes_column( $classes, $this->args['align'] );
			} else {
				$classes = mai_add_align_classes_row( $classes, $this->args['align'] );
			}

		} else {

			// Align text.
			if ( ! empty( $this->args['align_text'] ) ) {

				// Column. Save as variable first cause php 5.4 broke, and not sure I care to support that but WTH.
				$vertical_align = array_intersect( array( 'top', 'middle', 'bottom' ), $this->args['align_text'] );
				if ( ! empty( $vertical_align ) ) {
					$classes = 'column';
					$classes = mai_add_align_text_classes_column( $classes, $this->args['align_text'] );
				}
				$classes = mai_add_align_text_classes( $classes, $this->args['align_text'] );

			}

		}

		return $classes;
	}

	/**
	 * Get the ID from an object.
	 *
	 * @param   object     The object to get the id from.
	 *
	 * @return  int|false  The object ID, or false if not a valid content type.
	 */
	function get_object_id( $object ) {
		switch ( $this->content_type ) {
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
				$id = false;
		}
		return $id;
	}

	/**
	 * Get a link for a given entry.
	 *
	 * @param   object|int  $object_or_id  The object or object ID, either from $post, $term, or $user.
	 *
	 * @return  int|string  The image ID or empty string.
	 */
	function get_entry_link( $object_or_id ) {
		switch ( $this->content_type ) {
			case 'post':
				$link = get_permalink( $object_or_id );
			break;
			case 'term':
				$link = get_term_link( $object_or_id );
			break;
			default:
				$link = '';
		}
		return $link;
	}

	/**
	 * Get the add to cart link with screen reader text.
	 *
	 * @return  string|HTML
	 */
	function get_add_to_cart_link() {
		$link = '';
		if ( class_exists( 'WooCommerce' ) ) {
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$link = ob_get_clean();
		}
		return $link ? sprintf( '<p class="more-link-wrap">%s</p>', $link ) : '';
	}

	/**
	 * Get a type of content main image ID.
	 * Needs to be used in the loop, so it can get the correct content type ID.
	 *
	 * @param   int   $object_id   The object ID, either $post_id, $term_id, or $user_id. Can't be object if term, so safer to always use ID.
	 *
	 * @return  int  The image ID.
	 */
	function get_image_id( $object_id ) {
		switch ( $this->content_type ) {
			case 'post':
				$image_id = get_post_thumbnail_id( $object_id );
			break;
			case 'term':
				$key      = ( class_exists( 'WooCommerce' ) && ( 'product_cat' == $this->args['taxonomy'] ) ) ? 'thumbnail_id' : 'banner_id';
				$image_id = get_term_meta( $object_id, $key, true );
			break;
			case 'user':
				$image_id = get_user_meta( $object_id, 'banner_id', true ); // Not used yet.
			break;
			default:
				$image_id = '';
		}
		return $image_id;
	}

	/**
	 * Whether the main entry element should be a link or not.
	 *
	 * @return  bool
	 */
	function has_bg_link() {
		if ( $this->is_bg_image() && $this->args['link'] ) {
			return true;
		}
		return false;
	}

	/**
	 * If the grid is set to show image as a background.
	 *
	 * @return  bool
	 */
	function is_bg_image() {
		if ( 'bg' === $this->args['image_location'] ) {
			return true;
		}
		return false;
	}

	/**
	 * If image location is in the header.
	 *
	 * @return  bool
	 */
	function is_entry_header_image() {
		switch ( $this->args['image_location'] ) {
			case 'before_title':
			case 'after_title':
				$return = true;
			break;
			default:
				$return = false;
		}
		return $return;
	}

	/**
	 * Build the image HTML with location/align classes.
	 *
	 * @param   int     $image_id   The image ID.
	 * @param   string  $url        The url to link to, if 'link' param is true.
	 * @param   string  $att_title  The title to be used as the wrapping element attribute.
	 *
	 * @return  string}HTML  The image HTML.
	 */
	function get_image_html( $image_id, $url, $att_title ) {
		$image      = wp_get_attachment_image( $image_id, $this->args['image_size'], false, array( 'class' => 'wp-post-image' ) );
		$attributes = array();
		// Add the default class and add location as a class to the image link.
		$attributes['class'] = 'entry-image-link';
		if ( $this->args['image_location'] ) {
			$attributes['class'] .= sprintf( ' entry-image-%s', str_replace( '_', '-', $this->args['image_location'] ) );
		}
		if ( $this->args['image_align'] ) {
			switch ( $this->args['image_align'] ) {
				case 'left':
					$attributes['class'] .= ' alignleft';
				break;
				case 'center':
					$attributes['class'] .= ' aligncenter';
				break;
				case 'right':
					$attributes['class'] .= ' alignright';
				break;
			}
		} else {
			$attributes['class'] .= ' alignnone';
		}
		$attributes['title'] = $att_title;
		if ( $this->args['link'] ) {
			$attributes['href'] = $url;
			$image_wrap = 'a';
		} else {
			$image_wrap = 'span';
		}
		return sprintf( '<%s %s>%s</%s>', $image_wrap, genesis_attr( 'flex-entry-image-link', $attributes, $this->args ), $image, $image_wrap );
	}

	/**
	 * Get the number of items to show.
	 * If all, return the appropriate value depending on content type.
	 *
	 * @return  int  The number of items.
	 */
	function get_number() {
		if ( 'all' === $this->args['number'] ) {
			switch ( $this->content_type ) {
				case 'post':
					$number = -1; // wp_query uses -1 for all.
				break;
				case 'term':
					$number = 0;  // get_terms() uses 0 for all.
				break;
				default:
					$number = 100; // Just to be safe, cause we may add user later.
			}
		} else {
			$number = $this->args['number'];
		}
		return intval( $number );
	}

	/**
	 * Allow FacetWP to work with custom templates and WP_Query.
	 * by checking for a new 'facetwp' => true, parameter in the query.
	 *
	 * @uses    FacetWP
	 *
	 * @param   bool    $is_main_query  boolean  Whether FacetWP should use the current query
	 * @param   object  $query          The WP_Query object
	 *
	 * @return  bool
	 */
	function facetwp_is_main_query( $is_main_query, $query ) {
		if ( $this->facetwp && isset( $query->query_vars['facetwp'] ) ) {
			$is_main_query = true;
		}
		return $is_main_query;
	}

}
