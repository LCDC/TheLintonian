<?php
function lcdc_linton_cinema_widget() {
	register_widget( 'LCDC_LintonCinema' );
}
add_action( 'widgets_init', 'lcdc_linton_cinema_widget' );

class LCDC_LintonCinema extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'lc_widget',
			'description' => 'Linton Cinema Upcoming Movies',
		);
		parent::__construct( $widget_ops['classname'], $widget_ops['description'], $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$movies_url = 'https://ticketing.us.veezi.com/sessions/?siteToken=ZqcFJzyp7kChroZUH4Q8eQ==';
		$upcoming_regex = '<script type="application/ld\\+json">\\s+(\\[{"@type":"VisualArtsEvent".*])\\s+</script>';

		$html = wpcom_vip_file_get_contents( $movies_url, 5, 3 * HOUR_IN_SECONDS, array(
			'obey_cache_control_header' => false,
		) );

		preg_match( '/<script type="application\/ld\\+json">\\s+({.*})\\s+<\/script>/Uis', $html, $cinema_matches );
		preg_match( '/<script type="application\/ld\\+json">\\s+(\\[{"@type":"VisualArtsEvent".*])\\s+<\/script>/Uis', $html, $upcoming_matches );

		if ( isset( $cinema_matches[1] ) ) {
			$cinema = json_decode( $cinema_matches[1] );
		}

		// Bad data, bail.
		if ( ! isset( $cinema->legalName ) || 'Linton Cinema' !== $cinema->legalName ) { // @codingStandardsIgnoreLine
			return false;
		}

		if ( isset( $upcoming_matches[1] ) ) {
			$movie_list = json_decode( $upcoming_matches[1] );
		}

		// Bad data, bail.
		if ( ! is_array( $movie_list ) ) {
			return false;
		}

		$movies = array();
		foreach ( $movie_list as $upcoming ) {
			$movies[ $upcoming->name ]['name'] = $upcoming->name;
			$movies[ $upcoming->name ]['duration'] = $upcoming->duration;
			$movies[ $upcoming->name ]['url'] = $upcoming->url;
			$movies[ $upcoming->name ]['showtimes'][] = array(
				'time' => $upcoming->startDate, // @codingStandardsIgnoreLine
				'url' => $upcoming->url,
			);
		}

		// Regex to grab poster from URL: <img class="poster" src="(.*)" alt="Trolls" />
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		// TODO: Make a GUI for these "manual" fixes.
		foreach ( $movies as $name => $movie ) {
			switch ( $name ) {
				case 'Miracle on 34th Street (1947)':
					$name = 'Miracle on 34th Street';
					$year = '1947';
					break;
				case 'Kung Fu Panda 3':
					$year = '2016';
					break;
				case 'Angry Birds':
					$name = 'The Angry Birds Movie';
					$year = '2016';
					break;
				case 'The Secret Life of Pets':
					$year = '2016';
					break;
				case 'Ice Age: Collision Course':
					$year = '2016';
					break;
				case 'Storks':
					$year = '2016';
					break;
				case 'Nine Lives':
					$year = '2016';
					break;
				case 'Trolls':
					$year = '2016';
					break;
				case 'Sing':
					$year = '2016';
					break;
				case 'Monster Trucks':
					$year = '2016';
					break;
				default:
					$year = current_time( 'Y' );
			}// End switch().

			// TODO: Move API key to widget option.
			$omdb_search_url = add_query_arg( array(
				't' => rawurlencode( $name ),
				'y' => rawurlencode( $year ),
				'plot' => 'short',
				'r' => 'json',
				'apikey' => rawurlencode( get_option( 'omdbapi_key' ) ),
			), 'http://www.omdbapi.com/' );
			$omdb_result = json_decode( wpcom_vip_file_get_contents( $omdb_search_url, 5, 48 * HOUR_IN_SECONDS, array(
				'obey_cache_control_header' => false,
			) ) );

			if ( ! empty( $movie['showtimes'] ) ) {
				?>
				<div class="row" style="margin-bottom: 15px;">
					<div style="padding-left: 0px;" class="col-xs-4">
						<?php if ( isset( $omdb_result->Poster ) ) : // @codingStandardsIgnoreLine ?>
						<a href="<?php echo esc_url( $movie['showtimes'][0]['url'] ); ?>" rel="nofollow noopener">
							<img src=" <?php echo esc_url( $omdb_result->Poster );  // @codingStandardsIgnoreLine ?>" />
						</a>
						<?php endif; ?>
					</div>
					<div class="col-xs-8" style="padding: 0px;">
						<h5 class="title" style="color: white;"><?php echo esc_html( $name ); ?></h5>
						<ul style="list-style: none; padding-left: 10px;">
						<?php
						for ( $i = 0; $i < 3; $i++ ) {
							if ( isset( $movie['showtimes'][ $i ] ) ) {
								$movie_url = $movie['showtimes'][ $i ]['url'];
								$movie_time = DateTime::createFromFormat( DateTime::ISO8601, $movie['showtimes'][ $i ]['time'] );
								if ( date( 'W', current_time( 'timestamp' ) ) === $movie_time->format( 'W' ) ) {
									$date_str = 'D, g:ia';
								} else {
									$date_str = 'M jS, g:ia';
								}
						?>
							<li>
								<a href="<?php echo esc_url( $movie_url ); ?>" rel="nofollow noopener">
									<?php echo esc_html( $movie_time->format( $date_str ) ); ?>
								</a>
							</li>
						<?php
							}
						}
						?>
						</ul>
					</div>
				</div>
				<?php
			}// End if().
		}// End foreach().
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Upcoming Movies at Linton Cinemas', 'thelintonian' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'thelintonian' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}

if ( ! function_exists( 'wpcom_vip_file_get_contents' ) ) {
	function wpcom_vip_file_get_contents( $url, $timeout = 3, $cache_time = 900, $extra_args = array() ) {
		global $blog_id;

		$extra_args_defaults = array(
			'obey_cache_control_header' => true, // Uses the "cache-control" "max-age" value if greater than $cache_time
			'http_api_args' => array(), // See http://codex.wordpress.org/Function_API/wp_remote_get
		);

		$extra_args = wp_parse_args( $extra_args, $extra_args_defaults );

		$cache_key       = md5( serialize( array_merge( $extra_args, array( 'url' => $url ) ) ) ); // @codingStandardsIgnoreLine
		$backup_key      = $cache_key . '_backup';
		$disable_get_key = $cache_key . '_disable';
		$cache_group     = 'wpcom_vip_file_get_contents';

		// Let's see if we have an existing cache already
		// Empty strings are okay, false means no cache
		$cache = wp_cache_get( $cache_key, $cache_group );
		if ( false !== $cache ) {
			return $cache;
		}

		// The timeout can be 1 to 10 seconds, we strongly recommend no more than 3 seconds
		$timeout = min( 10, max( 1, (int) $timeout ) );

		$server_up = true;
		$response = false;
		$content = false;

		// Check to see if previous attempts have failed
		if ( false !== wp_cache_get( $disable_get_key, $cache_group ) ) {
			$server_up = false;
		} else {
			// Otherwise make the remote request
			$http_api_args = (array) $extra_args['http_api_args'];
			$http_api_args['timeout'] = $timeout;
			$response = wp_remote_get( $url, $http_api_args ); // @codingStandardsIgnoreLine
		}

		// Was the request successful?
		if ( $server_up && ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$content = wp_remote_retrieve_body( $response );

			$cache_header = wp_remote_retrieve_header( $response, 'cache-control' );
			if ( is_array( $cache_header ) ) {
				$cache_header = array_shift( $cache_header );
			}

			// Obey the cache time header unless an arg is passed saying not to
			if ( $extra_args['obey_cache_control_header'] && $cache_header ) {
				$cache_header = trim( $cache_header );
				// When multiple cache-control directives are returned, they are comma separated
				foreach ( explode( ',', $cache_header ) as $cache_control ) {
					// In this scenario, only look for the max-age directive
					if ( 'max-age' === substr( trim( $cache_control ), 0, 7 ) ) {
						// Note the array_pad() call prevents 'undefined offset' notices when explode() returns less than 2 results
						list( $cache_header_type, $cache_header_time ) = array_pad( explode( '=', trim( $cache_control ), 2 ), 2, null );
					}
				}
				// If the max-age directive was found and had a value set that is greater than our cache time
				if ( isset( $cache_header_type ) && isset( $cache_header_time ) && $cache_header_time > $cache_time ) {
					$cache_time = (int) $cache_header_time; // Casting to an int will strip "must-revalidate", etc.
				}
			}

			// The cache time shouldn't be less than a minute
			// Please try and keep this as high as possible though
			// It'll make your site faster if you do
			$cache_time = (int) $cache_time;
			if ( $cache_time < 60 ) {
				$cache_time = 60;
			}

			// Cache the result
			wp_cache_add( $cache_key, $content, $cache_group, $cache_time );

			// Additionally cache the result with no expiry as a backup content source
			wp_cache_add( $backup_key, $content, $cache_group );

			// So we can hook in other places and do stuff
			do_action( 'wpcom_vip_remote_request_success', $url, $response );
		} elseif ( false !== wp_cache_get( $backup_key, $cache_group ) ) {
			$content = wp_cache_get( $backup_key, $cache_group );
			// Okay, it wasn't successful. Perhaps we have a backup result from earlier.
			// If a remote request failed, log why it did
			if ( ! defined( 'WPCOM_VIP_DISABLE_REMOTE_REQUEST_ERROR_REPORTING' ) || ! WPCOM_VIP_DISABLE_REMOTE_REQUEST_ERROR_REPORTING ) {
				if ( $response && ! is_wp_error( $response ) ) {
					error_log( "wpcom_vip_file_get_contents: Blog ID {$blog_id}: Failure for $url and the result was: " . $response['response']['code'] . ' ' . $response['response']['message'] ); // @codingStandardsIgnoreLine
				} elseif ( $response ) { // is WP_Error object
					error_log( "wpcom_vip_file_get_contents: Blog ID {$blog_id}: Failure for $url and the result was: " . $response->get_error_message() ); // @codingStandardsIgnoreLine
				}
			}
		} elseif ( $response ) {
			// We were unable to fetch any content, so don't try again for another 60 seconds
			wp_cache_add( $disable_get_key, 1, $cache_group, 60 );

			// If a remote request failed, log why it did
			if ( ! defined( 'WPCOM_VIP_DISABLE_REMOTE_REQUEST_ERROR_REPORTING' ) || ! WPCOM_VIP_DISABLE_REMOTE_REQUEST_ERROR_REPORTING ) {
				if ( $response && ! is_wp_error( $response ) ) {
					error_log( "wpcom_vip_file_get_contents: Blog ID {$blog_id}: Failure for $url and the result was: " . $response['response']['code'] . ' ' . $response['response']['message'] ); // @codingStandardsIgnoreLine
				} elseif ( $response ) { // is WP_Error object
					error_log( "wpcom_vip_file_get_contents: Blog ID {$blog_id}: Failure for $url and the result was: " . $response->get_error_message() ); // @codingStandardsIgnoreLine
				}
			}
			// So we can hook in other places and do stuff
			do_action( 'wpcom_vip_remote_request_error', $url, $response );
		}// End if().

		return $content;
	}
}// End if().
