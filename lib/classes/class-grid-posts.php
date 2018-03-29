<?php

class Mai_Grid_Posts extends Mai_Grid {

	function __construct( $args ) {
		parent::__construct( $args );
		// return $this->render();
	}

	function render() {

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
		if ( $this->args['exclude_existing'] && ! empty( $existing_post_ids ) ) {
			if ( isset( $query_args['post__not_in'] ) ) {
				$query_args['post__not_in'] = array_push( $query_args['post__not_in'], $existing_post_ids );
			} else {
				$query_args['post__not_in'] = $existing_post_ids;
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

		// Post ids.
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
		 * @param   array  $args           Parsed arguments to pass to WP_Query.
		 * @param   array  $original_args  Original attributes passed to the shortcode.
		 *
		 * @return  array  The args.
		 */
		$query_args = apply_filters( 'mai_grid_args', $this->args, $this->original_args );

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
					$existing_post_ids[] = get_the_ID();

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

						// Image.
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

						// Content
						if ( in_array( 'content', $this->args['show'] ) ) {
							$entry_content .= wp_strip_all_tags( strip_shortcodes( get_the_content() ) );
						}

						// Limit content. Empty string is sanitized to zero.
						if ( $this->args['content_limit'] > 0 ) {
							// Reset the variable while trimming the content
							$entry_content = wpautop( wp_trim_words( $entry_content, $this->args['content_limit'], '&hellip;' ) );
						}

						// Price.
						if ( in_array( 'price', $this->args['show'] ) ) {
							ob_start();
							woocommerce_template_loop_price();
							$entry_content .= ob_get_clean();
						}

						// Image.
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

						// Add entry content wrap if we have content
						if ( $entry_content ) {
							$html .= sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content', array(), $this->args ), $entry_content );
						}

						// Meta
						if ( in_array( 'meta', $this->args['show'] ) ) {
							$entry_footer = mai_get_the_posts_meta( get_the_ID() );
						}

						// Add filter to the entry footer
						$entry_footer = apply_filters( 'mai_flex_entry_footer', $entry_footer, $this->args, $this->original_args );

						// Entry footer
						if ( $entry_footer ) {
							$html .= sprintf( '<footer %s>%s</footer>', genesis_attr( 'entry-footer', array(), $this->args ), $entry_footer );
						}

						// Image
						if ( ( 'bg' == $this->args['image_location'] ) && $this->args['link'] ) {
							$html .= mai_get_bg_image_link( $url, get_the_title() );
						}

					$html .= $this->get_entry_wrap_close();

				endwhile;

			$html .= $this->get_row_wrap_close();

		}

		// No Posts.
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

}
