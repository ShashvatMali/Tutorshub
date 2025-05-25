<?php
/**
 * @Create profile from admin create user
 * @type create
 */
if (!function_exists('tuturn_create_wp_user_profile')) {

    function tuturn_create_wp_user_profile($user, $post_type='tuturn-instructor') {
		global $tuturn_settings;
		
		if(!empty($user->ID)){
			$user_meta	= get_userdata($user->ID);
			$user_id	= $user->ID;
		}else{
			$user_id	= $user;
			$user_meta	= get_userdata($user_id);
		}

		if( !empty( $user_id )  ) {
			$title		= $user_meta->first_name.' '.$user_meta->last_name;
			$roles		= !empty($user_meta->roles) ? $user_meta->roles : '';
			$linked_profile   	= tuturn_get_linked_profile_id($user_id);
			if(!empty( $linked_profile )){
				if ( 'publish' == get_post_status ( $linked_profile ) ) {
					return true;
				}
			}

			if( !empty($roles) && in_array('subscriber',$roles)){

				if( !empty($post_type) && ( $post_type === 'tuturn-student' || $post_type	=== 'tuturn-instructor' ) ){
					$post_data	= array(
						'post_title'	=> wp_strip_all_tags($title),
						'post_author'	=> $user_id,
						'post_status'   => 'publish',
						'post_type'		=> $post_type,
					);
					$post_id	= wp_insert_post( $post_data );

					if( !empty( $post_id ) ) {
						update_post_meta($post_id, '_linked_profile',intval($user_id));
						//Update user linked profile
						if( !empty($post_type) && ( $post_type === 'tuturn-student' ) ){
							update_user_meta( $user_id, '_user_type', 'student' );
						} else {						
							update_user_meta( $user_id, '_user_type', 'instructor' );
						}
						update_post_meta( $post_id, '_is_verified', 'yes' );
						update_user_meta($user_id, '_linked_profile', intval($post_id));
						tuturn_update_profile_options($user_id, $post_id, $post_type);
							
					}
				}
			}
		}
	}
}

/**
 * @Create profile from admin create user
 * @type create
 */
if (!function_exists('tuturn_update_profile_options')) {
	function tuturn_update_profile_options($user_id='', $profile_id='', $user_type='tuturn-instructor') {
		if(empty($user_id)){
			return;
		}

		$user_meta	= get_userdata($user_id);

		if(empty($profile_id)){
			$profile_id	= get_user_meta($user_id, '_linked_profile', true);
		}

		$_address	= get_post_meta($profile_id, '_address', true);
		if(empty($_address)){

			$address_array	= array(
				array(
					'_address'	=> '4300 Black Ave, Pleasanton, California United States',
					'_country_region'	=> 'us',
					'_zipcode'	=> '94566',
					'_latitude'	=> '37.671924',
					'_longitude'	=> '-121.873301',
				),
			
				array(
					'_address'	=> '4300 Black Ave, Pleasanton, California, United States',
					'_country_region'	=> 'us',
					'_zipcode'	=> '44124',
					'_latitude'	=> '41.522717',
					'_longitude'	=> '-81.448645',
				),
				array(
					'_address'	=> '98 Quarter Horse Ln, Hampstead, Nebraska, United States',
					'_country_region'	=> 'us',
					'_zipcode'	=> '28443',
					'_latitude'	=> '34.420532',
					'_longitude'	=> '-77.648799',
				),
				array(
					'_address'	=> '356 Arsenal St, Watertown, Maine, United States',
					'_country_region'	=> 'us',
					'_zipcode'	=> '02472',
					'_latitude'	=> '42.363869',
					'_longitude'	=> '-71.165794',
				),
			);

			shuffle($address_array);

			$address	= $address_array['0'];
			
			update_post_meta($profile_id, '_address', $address['_address']);
			update_post_meta($profile_id, '_country_region', $address['_country_region']);
			update_post_meta($profile_id, '_zipcode', $address['_zipcode']);
			update_post_meta($profile_id, '_latitude', $address['_latitude']);
			update_post_meta($profile_id, '_longitude', $address['_longitude']);
		}

		if(empty(get_post_field('post_content', $profile_id))){

			$user_introduction	= 'On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish.
			<div class="tu-blogfeatures">
				<figure><img class="alignnone size-medium wp-image-113" src="http://amentotech.com/projects/tuturn/wp-content/uploads/2022/03/img-02.jpg" alt="Banner" width="300" height="218" /></figure>
				<div class="tu-featurelist">
					<div class="tu-description">			
					Sed ut perspiciatis unde omnis iste natus error sit voluptatem antium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.
					</div>
					<ul class="tu-mainlist">
						<li>Accusantium doloremque laudantium totam rem aperiam.</li>
						<li>Eicta sunt explicaboemo enim ipsam voluptatemuia</li>
						<li>Voluptas sit aspernatur aut odit aut fugited</li>
						<li>Quia consequuntur magni dolores eos qui ratione</li>
					</ul>
				</div>
			</div>
			When our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.';
			$profile_post = array(
				'ID'           => $profile_id,
				'post_content'   => $user_introduction,
			);
			wp_update_post( $profile_post );
		}

		$existing_tuturn_options	= default_array();
		$existing_tuturn_options	= !empty($existing_tuturn_options) ? $existing_tuturn_options : array();

		$tuturn_options	= get_post_meta($profile_id, 'profile_details', true);
		$tuturn_options	= !empty($tuturn_options) ? $tuturn_options : array();
		$media_gallery_options	= get_post_meta($profile_id, 'media_gallery', true);
		$media_gallery_options	= !empty($media_gallery_options) ? $media_gallery_options : array();
		$teaching_preference	= get_post_meta($profile_id, 'teaching_preference', true);
		$tuturn_bookings		= get_post_meta($profile_id, 'tuturn_bookings', true);
		$teaching_preference	= !empty($teaching_preference) ? $teaching_preference : array();
		$hourly_rate			= get_post_meta($profile_id, 'hourly_rate', true);
		$hourly_rate			= !empty($hourly_rate) ? $hourly_rate : '';

		$first_name		= $user_meta->first_name;
		$last_name		= $user_meta->last_name;
		$company		= $user_meta->first_name.' '.$user_meta->last_name;
		$user_email		= $user_meta->user_email;

		if(empty($tuturn_options['first_name'])){
			$tuturn_options['first_name']	= $first_name;
		}

		if(empty($tuturn_options['last_name'])){
			$tuturn_options['last_name']	= $last_name;
			$tuturn_options['name']	= $first_name.' '.$last_name;
		}

		if(empty($tuturn_options['company'])){
			$tuturn_options['company']	= $company;
		}

		if(empty($tuturn_options['tagline'])){
			$tagline	= tuturn_user_tagline();
			$tuturn_options['tagline']	= $tagline;
		}

		if(empty($tuturn_options['contact_info'])){
			$contact_details	= $existing_tuturn_options['contact_info'];
			$contact_details['email_address']	= $user_email;
			$contact_details['skypeid']			= $company;
			$tuturn_options['contact_info']		= $contact_details;
		}

		if(empty($tuturn_options['languages'])){
			$social_media_details	= $existing_tuturn_options['languages'];
			$tuturn_options['languages']	= $social_media_details;
		}

		if(empty($teaching_preference)){
			$existing_teaching_preference	= $existing_tuturn_options['teaching_preference'];
			if(!empty($existing_teaching_preference)){
				update_post_meta($profile_id, 'teaching_preference', $existing_teaching_preference);
			}
		}

		if($user_type == 'tuturn-instructor'){

			if(empty($hourly_rate)){
				$hourly_rate	= tuturn_user_hourly_rate();
				if(!empty($hourly_rate)){
					update_post_meta($profile_id, 'hourly_rate', $hourly_rate);
				}
			}

			if(empty($tuturn_options['subject'])){				
				$categories	= tuturn_categories($profile_id);
				$tuturn_options['subject']	= $categories;
			}
	
			if(empty($tuturn_options['education'])){
				$existing_education	= $existing_tuturn_options['education'];
				$tuturn_options['education']	= $existing_education;
			}
	
			if(empty($media_gallery_options)){
				$existing_media_gallery	= $existing_tuturn_options['media_gallery'];
				if(!empty($existing_media_gallery)){
					shuffle($existing_media_gallery);
					update_post_meta($profile_id, 'media_gallery', $existing_media_gallery);
				}
			}	
			
			if(empty($tuturn_bookings)){
				$existing_bookings	= $existing_tuturn_options['bookings'];
				if(!empty($existing_bookings)){
					update_post_meta($profile_id, 'tuturn_bookings', $existing_bookings);
				}
			}
		}

		if(!empty($tuturn_options)){
			update_post_meta($profile_id, 'profile_details', $tuturn_options);
		}
	}

}

/**
 * Set random tagline
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_categories')) {
    function tuturn_categories($profile_id='') {

		$categories_array	= array();
		$category_ids_array	= array();
		$categories = get_categories( array(
			'taxonomy' => 'product_cat',
			'orderby' => 'name',
			'hide_empty'	=> false,
			'parent'  => 0
		) );

		$count_subcategories	= 8;		

		foreach ( $categories as $category ) {
			$category_array	= array();			
			$category_array['parent_category_id']	= $category->term_id;
			$category_array['parent_category']  = array(
                'id'	=> $category->term_id,
                'slug'	=> $category->slug,
                'name'	=> $category->name,
            );
			$subcategories = get_categories( array(
				'taxonomy' => 'product_cat',
				'orderby' => 'name',
				'hide_empty'	=> false,
				'parent'  => $category->term_id
			) );			

			if(!empty($subcategories) && count($subcategories) > 2){
				shuffle( $subcategories );
				if(count($subcategories) > $count_subcategories){
					$subcategories = array_slice( $subcategories, 0, $count_subcategories );
				}
			
				foreach($subcategories as $subcategory){
					if (!empty($subcategory->term_id)) {
						$category_ids_array[]	= $category->term_id;
						$category_ids_array[]	= $subcategory->term_id;
						$category_array['subcategories'][]  = array(
							'id'    => $subcategory->term_id,
							'slug'  => $subcategory->slug,
							'name'  => $subcategory->name,
						);
					}
				}

				$count_subcategories	= $count_subcategories + 2;	
				$categories_array[$category->term_id]	= $category_array;
			}
					
		}

		if(!empty($category_ids_array)){
			wp_set_post_terms($profile_id, $category_ids_array, 'product_cat');		
		}

		return $categories_array;
	}
}

/**
 * Set random tagline
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_user_hourly_rate')) {
    function tuturn_user_hourly_rate() {
		$hourly_rate_array	= array(
			62.32,
			21.00,
			40.00,
			99.00,
			66.00,
			19.00,
			29.00,
			55.00,
			69.00,
			74.00,
			59.00,
			33.00,
			87.00,
			89.00,
			77.00,
			32.00,
			52.00,
			17.00,
			39.00,
			28.00,
			44.00,
			79.00,
			24.00,
			50.00,
			55.00
		);
		shuffle($hourly_rate_array);
		return $hourly_rate_array['0'];
	}
}

/**
 * Set random tagline
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_user_tagline')) {
    function tuturn_user_tagline() {
		$taglines_array	= array(
			'Making thoughts ideas into super reality',
			'Creating idea into true reality',
			'Technology making thats all I do',
			'Food lovers expressive shopping mall',
			'Creative design for motor vehicles',
			'Wedding and events planner',
			'Leading you from dark to bright future',
			'Business, The Smart Choice',
			'Business - Go For It!',
			'Be young, have fun, taste Business',
			'Think Once, Think Twice, Think Technology',
			'Technology to play it safe',
			'Services - The Revolution',
			'Services pointing in the right direction',
			'Get Serious. Get Services',
			'Everything We Do is Driven by Internet',
			'Internet knocks out the competition',
			'Internet always and forever',
			'Internet solves your problems',
		);
		shuffle($taglines_array);
		return $tagline	= $taglines_array['0'];
	}
}

/**
 * Add instructor reviews
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_instructor_add_reviews')) {
    function tuturn_instructor_add_reviews() {
		$users = get_users(
			array(
				//'fields' => array( 'ID' ),
				'role' => 'subscriber',
				'meta_query' => array(
					array(
						'key' => '_user_type',
						'value' => 'student',
						'compare' => '=='
					)
				),
				'orderby' => 'ID',
				'order'   => 'ASC'
			)
		);

		$arg_instructors    = array(
            //'fields'            => 'ids',
            'post_type'         => array('tuturn-instructor'),
            'post_status'       => 'any',
            'numberposts'       => -1
        );
        $instructors    = get_posts($arg_instructors);

		$content_array	= array(
			0	=> 'Elit amet ut dui nam enim consectetur arcu amet varius. Viverra ac nisl quam nec justo, posuere suspendisse consequat. Sit aliquam purus mattis libero, pellentesque tellus sed amet pretium. Porttitor massa lectus dolor at enim. Ultricies varius diam elementum quis id eleifend. Eu vulputate urna, nulla dignissim ultrices.',
			1	=> 'Hac lacus nulla tristique lectus lectus enim. Est eget penatibus et in tempus. Cursus habitant at mauris arcu sed pellentesque viverra massa. Facilisis tristique bibendum dictum amet posuere. Facilisis quis nisi facilisis orci nulla. Hac nullam ut tortor eget.',
			2	=> 'Ipsum quisque risus nisl sed tortor nulla. Scelerisque neque, velit dui eget. Mi, viverra sagittis est sapien blandit. Sit mi erat turpis integer accumsan. Mi, quis eget tincidunt dictum. Lorem maecenas a faucibus mattis laoreet quis.',
			3	=> 'Dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris neimit utnaeliquip ex ea commodo consequat volupte ateian essemae cillume.',
			4	=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada sem eget tortor ultricies rutrum. Sed sed finibus libero. Integer eget mauris sed urna sagittis varius. Duis tincidunt lectus at quam faucibus',
			5	=> 'Cras ut egestas felis. Nunc eleifend facilisis purus. Nunc mattis mauris vitae rhoncus porttitor. Aenean finibus metus vel fermentum mollis. Integer sed diam ac nibh cursus viverra eu a est. Etiam dapibus quam quis massa bibendum imperdiet',
		);
        
        if( !empty($instructors) ){

            foreach($instructors as $instructor){
				$comments	= get_comments(array('post_id' => $instructor->ID));
				$total_comments	= !empty($comments) ? count($comments) : 0;

				if($total_comments<2){

					$counter	= 0;
					foreach ( $users as $user ){

						$user_id     	= !empty($user->ID) ? intval($user->ID)  : '';
						$profile_id     = !empty($instructor->ID) ? intval($instructor->ID)  : '';
						$rating     	= 5;
						$content     	= !empty($content_array[$counter]) ? $content_array[$counter]  : '';
						$user_email     = !empty($user->user_email) ? $user->user_email : '';
						$user_name      = !empty($user->display_name) ? $user->display_name : '';
						
						if(!empty($user_id) && !empty($profile_id) && !empty($content) && !empty($user_email)){

							$time = current_time('mysql');
							// prepare data array for insertion
							$comment_data = array(
								'comment_post_ID' 		    => $profile_id,
								'comment_author' 		    => $user_name,
								'comment_author_email' 	    => $user_email,
								'comment_author_url' 	    => 'http://',
								'comment_content' 		    => esc_html($content),
								'comment_type' 			    => 'instructor_reviews',
								'comment_parent' 		    => 0,
								'user_id' 				    => $user_id,
								'comment_date' 			    => $time,
								'comment_approved' 		    => 1,
							);

							// insert data
							$comment_id = wp_insert_comment($comment_data);
							if (!empty($comment_id)) {
								$counter++;
								update_comment_meta($comment_id, 'rating', $rating);
								$tu_total_rating        = get_post_meta( $profile_id, 'tu_total_rating', true );
								$tu_total_rating		= !empty($tu_total_rating) ? $tu_total_rating : 0;
								$tu_review_users		= get_post_meta( $profile_id, 'tu_review_users', true );
								$tu_review_users		= !empty($tu_review_users) ? intval($tu_review_users) : 0;
								$tu_total_rating		= $tu_total_rating + $rating;
								$tu_review_users++;
								$tu_average_rating		= ($tu_total_rating / $tu_review_users);
								update_post_meta( $profile_id, 'tu_average_rating', $tu_average_rating );
								update_post_meta( $profile_id, 'tu_total_rating', $tu_total_rating );
								update_post_meta( $profile_id, 'tu_review_users', $tu_review_users );
							}
						}
					}
				} else {
					$user_array	= array();

					foreach ( $users as $user ){
						$user_id     	= !empty($user->ID) ? intval($user->ID)  : '';
						$profile_id     = !empty($instructor->ID) ? intval($instructor->ID)  : '';
						$user_email     = !empty($user->user_email) ? $user->user_email : '';
						$user_name      = !empty($user->display_name) ? $user->display_name : '';
						$user_array[]	= array(
							'user_id' => $user_id,
							'profile_id' => $profile_id,
							'user_email' => $user_email,
							'user_name' => $user_name,
						);
					}

					if ( $comments ) { 
						$update_counter	= 0;
						foreach ( $comments as $comment ) {
							$user_id	= '';
							$user_email	= '';
							$user_name	= '';
							$user_data	= array();

							if(!empty($user_array[$update_counter])){
								$user_data		= $user_array[$update_counter];
								$user_id     	= !empty($user_data['user_id']) ? intval($user_data['user_id'])  : 0;
								$user_email		= !empty($user_data['user_email']) ? intval($user_data['user_email'])  : 0;
								$user_name     	= !empty($user_data['user_name']) ? intval($user_data['user_name'])  : 0;
							}

							$comment_ID	= $comment->comment_ID;
							if(!empty($comment_ID) && !empty($user_id) && !empty($user_name) && !empty($user_email)){
								$commentarr = array();
								$commentarr['comment_ID'] = $comment_ID;
								$commentarr['user_id'] = $user_id;
								$commentarr['comment_author'] = $user_name;
								$commentarr['comment_author_email'] = $user_email;
								wp_update_comment( $commentarr );
								$update_counter++;
							}

						}

					}

				}
			}
		}

	}
	
}

/**
 * Set user order package update
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_user_order_package_update')) {
    function tuturn_user_order_package_update($user_ID='') {
		$package_order_id	= get_user_meta( $user_ID, 'package_order_id', true);

		if( empty($package_order_id) ){
			$user_profile_id		= get_user_meta( $user_ID, '_linked_profile', true);
			$order_id				= 375;			
			$_linked_profile		= get_post_meta( $order_id, '_linked_profile', true);
			$_customer_user			= get_post_meta( $_linked_profile, '_linked_profile', true);
			$package_expriy_date	= get_post_meta( $_linked_profile, 'package_expriy_date', true);
			$featured_expriy_date	= get_post_meta( $_linked_profile, 'featured_expriy_date', true);
			$featured_profile		= get_post_meta( $_linked_profile, 'featured_profile', true);

			$user_package_create_date	= get_user_meta( $_customer_user, 'package_create_date', true);
			$user_package_expriy_date	= get_user_meta( $_customer_user, 'package_expriy_date', true);
			$user_package_details		= get_user_meta( $_customer_user, 'package_details', true);

			update_user_meta($user_ID, 'package_order_id', $order_id);
			update_user_meta($user_ID, 'package_create_date', $user_package_create_date);
			update_user_meta($user_ID, 'package_expriy_date', $user_package_expriy_date);
			update_user_meta($user_ID, 'package_details', $user_package_details);

			update_post_meta($user_profile_id, 'package_expriy_date', $package_expriy_date);
			update_post_meta($user_profile_id, 'featured_expriy_date', $featured_expriy_date);
			update_post_meta($user_profile_id, 'featured_profile', $featured_profile);

		}
	}
}

/**
 * Set user order package update
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_update_user_package')) {
    function tuturn_update_user_package() {
		$users_data	= get_users(array(
			'meta_key' => '_user_type',
			'meta_value' => 'instructor',
			'fields' => array( 'ID' ),
		));		
		foreach($users_data as $user){
			tuturn_user_order_package_update($user->ID);
		}
		
	}
}

/**
 * Set user orders update
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_user_order_update')) {
    function tuturn_user_order_update() {
		$loop = new WP_Query( array(
			'post_type'         => 'shop_order',
			'post_status'       =>  array_keys( wc_get_order_statuses() ),
			'posts_per_page'    => -1,
			'meta_query' => array(
				array(
					'key'		=> 'payment_type',
					'value'		=> array('package', 'booking'),
					'compare'	=> 'IN'
				)
			)
		) );		
		// The Wordpress post loop
		if ( $loop->have_posts() ): 
			while ( $loop->have_posts() ) : $loop->the_post();			
				// The order ID
				$order_id = $loop->post->ID;

				if(!empty($order_id)){
					$order 			= wc_get_order($order_id);
					$order_id 		= $order->get_id();
					$items 			= $order->get_items();					
					$payment_type	= get_post_meta($order_id, 'payment_type', true);	

					if($payment_type == 'package'){
						$profile_id	= get_post_meta($order_id, '_linked_profile', true);					
						$user_id	= get_post_meta($profile_id, '_linked_profile', true);				
						update_post_meta( $order_id, '_customer_user',$user_id );
						foreach ($items as $key => $item) {
							$order_detail 	= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );	
							tuturn_update_packages_data($order_id,$order_detail,$user_id);
						}
					} else if($payment_type == 'booking'){	
						tuturn_update_booking_migration( $order_id);
					}
				}			
			endwhile;			
			wp_reset_postdata();
		endif;				
	}
}

/**
 * Update User Hiring payment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */

if (!function_exists('tuturn_update_booking_migration')) {
    function tuturn_update_booking_migration( $order_id) {
		$student_profile_id   = get_post_meta($order_id, 'student_profile_id', true);
		$instructor_profile_id   = get_post_meta($order_id, 'instructor_profile_id', true);

		$student_id	= get_post_meta($student_profile_id, '_linked_profile', true);
		$instructor_id	= get_post_meta($instructor_profile_id, '_linked_profile', true);

		update_post_meta( $order_id, 'student_id',$student_id );
		update_post_meta( $order_id, '_customer_user',$student_id );
    	update_post_meta( $order_id, 'instructor_id',$instructor_id );
	}
}

/**
 * Set seller orders
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_update_billing_details')) {

    function tuturn_update_billing_details($user_id) {
		$user = get_user_by( 'ID', $user_id );
		$country	= array(
			'35801' => 'US',
			'99501' => 'US',
			'90001' => 'US',
			'19901' => 'US',
			'20001' => 'US',
			'32501' => 'US',
			'33124' => 'US',
			'32801' => 'US',
			'35801' => 'US',
			'99501' => 'US',
			'90001' => 'US',
			'19901' => 'US',
			'20001' => 'US',
			'32501' => 'US',
			'33124' => 'US',
			'32801' => 'US',
			'62701' => 'US',
			'46201' => 'US'
		);		
		
		$country_key	= array_rand($country,1);
		$phone_numbers	= array('0800-271749-749','0800-407315-29','0800-41258-93');
		$phone_key		= array_rand($phone_numbers);
		$cities_array	= array('Springhampton','Southley','Hallborough City','East Passburg','Backwich');
		$cities_key		= array_rand($cities_array);
		$address_array	= array('xyz Heritage Drive Homestead, FL 33030','29 Andover Street Oxon Hill, MD 20745','765 Fairview Ave. Tampa, FL 33604');
		$address_key	= array_rand($address_array);
		$list = array(
            'billing_first_name'    => $user->first_name,
            'billing_last_name'    	=> $user->last_name,
            'billing_company'    	=> 'AmentoTech',
            'billing_address_1'    	=> $address_array[$address_key],
            'billing_country'   	=> 'US',
            'billing_city'    		=> $cities_array[$cities_key],
            'billing_postcode'    	=> $country_key,
            'billing_phone'    		=> $phone_numbers[$phone_key],
			'billing_email'    		=> $user->user_email,
        );
		foreach ($list as $meta_key => $meta_value ) {
            update_user_meta( $user_id,$meta_key, sanitize_text_field( $meta_value ) );
        }
	}
}

function tuturn_address_update($profile_id = ''){
	$_address	= get_post_meta($profile_id, '_address', true);
	if(!empty($_address)){

		$address_array	= array(
			array(
				'_address'	=> 'Albuquerque, CA',
				'_country_region'	=> 'us',
				'_zipcode'	=> '94566',
				'_latitude'	=> '37.671924',
				'_longitude'	=> '-121.873301',
			),
			array(
				'_address'	=> 'Austin, AZ',
				'_country_region'	=> 'us',
				'_zipcode'	=> '94566',
				'_latitude'	=> '37.671924',
				'_longitude'	=> '-121.873301',
			),			
			array(
				'_address'	=> 'Sacramento, CA',
				'_country_region'	=> 'us',
				'_zipcode'	=> '44124',
				'_latitude'	=> '41.522717',
				'_longitude'	=> '-81.448645',
			),
			array(
				'_address'	=> 'Tampa, LA',
				'_country_region'	=> 'us',
				'_zipcode'	=> '44124',
				'_latitude'	=> '41.522717',
				'_longitude'	=> '-81.448645',
			),
			array(
				'_address'	=> 'New Orleans, OR',
				'_country_region'	=> 'us',
				'_zipcode'	=> '44124',
				'_latitude'	=> '41.522717',
				'_longitude'	=> '-81.448645',
			),
			array(
				'_address'	=> 'Charlotte, OK',
				'_country_region'	=> 'us',
				'_zipcode'	=> '28443',
				'_latitude'	=> '34.420532',
				'_longitude'	=> '-77.648799',
			),
			array(
				'_address'	=> 'San Francisco, ID',
				'_country_region'	=> 'us',
				'_zipcode'	=> '28443',
				'_latitude'	=> '34.420532',
				'_longitude'	=> '-77.648799',
			),
			array(
				'_address'	=> 'Baltimore, NV',
				'_country_region'	=> 'us',
				'_zipcode'	=> '02472',
				'_latitude'	=> '42.363869',
				'_longitude'	=> '-71.165794',
			),
			array(
				'_address'	=> 'Indianapolis, CA',
				'_country_region'	=> 'us',
				'_zipcode'	=> '02472',
				'_latitude'	=> '42.363869',
				'_longitude'	=> '-71.165794',
			),
			array(
				'_address'	=> 'Phoenix, MN',
				'_country_region'	=> 'us',
				'_zipcode'	=> '02472',
				'_latitude'	=> '42.363869',
				'_longitude'	=> '-71.165794',
			),
		);

		shuffle($address_array);
		$address	= $address_array['0'];		
		update_post_meta($profile_id, '_address', $address['_address']);
		update_post_meta($profile_id, '_country_region', $address['_country_region']);
		update_post_meta($profile_id, '_zipcode', $address['_zipcode']);
		update_post_meta($profile_id, '_latitude', $address['_latitude']);
		update_post_meta($profile_id, '_longitude', $address['_longitude']);
	}
}

/**
 * Default user data array
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
function default_array(){
	return array (
		'first_name' => 'Arianne',
		'last_name' => 'Kearns',
		'company' => 'Arianne Kearns',
		'tagline' => 'Internet knocks out the competition',
		'languages' =>  array (
			'english-uk' => 'English',
			'arabic' => 'Arabic',
			'chinese' => 'Chinese',
			'hebrew' => 'Hebrew',
			'french' => 'French',
			'spanish' => 'Spanish',
			'german' => 'German',
			'portuguese' => 'Portuguese',
		),
		'contact_info' => array (
		  'phone' => '0800-271749-749',
		  'email_address' => 'arianne@amentotech.com',
		  'skypeid' => 'ariannekearns',
		  'website' => 'www.cindylorex77.com',
		  'whatsapp_number' => '1324 - 14568-624',
		),
		'teaching_preference' => array (
		  'home',
		  'student_home',
		  'online',
		),
		'media_gallery_first_image' =>  array (
			0 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-11.jpg',
			  'fileName' => 'gallery-11.jpg',
			  'attachment_id' => '290',
			),
			1 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-4.jpg',
			  'fileName' => 'gallery-4.jpg',
			  'attachment_id' => '283',
			),
			2 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-7.jpg',
			  'fileName' => 'gallery-7.jpg',
			  'attachment_id' => '284',
			),
			3 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-8.jpg',
			  'fileName' => 'gallery-8.jpg',
			  'attachment_id' => '288',
			),
			4 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-10.jpg',
			  'fileName' => 'gallery-10.jpg',
			  'attachment_id' => '289',
			),
			5 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery01.jpg',
			  'fileName' => 'gallery-10.jpg',
			  'attachment_id' => '1129',
			),
			6 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery02.jpg',
			  'fileName' => 'gallery02.jpg',
			  'attachment_id' => '1133',
			),
			7 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery03.jpg',
			  'fileName' => 'gallery03.jpg',
			  'attachment_id' => '1134',
			),
			8 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery04.jpg',
			  'fileName' => 'gallery04.jpg',
			  'attachment_id' => '1135',
			),
			9 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery05.jpg',
			  'fileName' => 'gallery05.jpg',
			  'attachment_id' => '1138',
			),
			10 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery06.jpg',
			  'fileName' => 'gallery06.jpg',
			  'attachment_id' => '1140',
			),
			11 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery07.jpg',
			  'fileName' => 'gallery07.jpg',
			  'attachment_id' => '1141',
			),
			12 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery08.jpg',
			  'fileName' => 'gallery08.jpg',
			  'attachment_id' => '1142',
			),
			13 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery09.jpg',
			  'fileName' => 'gallery09.jpg',
			  'attachment_id' => '1143',
			),
			14 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery10.jpg',
			  'fileName' => 'gallery10.jpg',
			  'attachment_id' => '1144',
			),
			15 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery011.jpg',
			  'fileName' => 'gallery011.jpg',
			  'attachment_id' => '1145',
			),
			16 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery12.jpg',
			  'fileName' => 'gallery12.jpg',
			  'attachment_id' => '1146',
			),
			17 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery13.jpg',
			  'fileName' => 'gallery13.jpg',
			  'attachment_id' => '1147',
			),
			18 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery14.jpg',
			  'fileName' => 'gallery14.jpg',
			  'attachment_id' => '1148',
			),
			19 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery15.jpg',
			  'fileName' => 'gallery15.jpg',
			  'attachment_id' => '1149',
			),
			20 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery16.jpg',
			  'fileName' => 'gallery16.jpg',
			  'attachment_id' => '1150',
			),
			21 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery17.jpg',
			  'fileName' => 'gallery17.jpg',
			  'attachment_id' => '1151',
			),
			22 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery18.jpg',
			  'fileName' => 'gallery18.jpg',
			  'attachment_id' => '1152',
			),
			23 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery19.jpg',
			  'fileName' => 'gallery19.jpg',
			  'attachment_id' => '1153',
			),
			24 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/allery20.jpg',
			  'fileName' => 'allery20.jpg',
			  'attachment_id' => '1157',
			),
			25 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery21.jpg',
			  'fileName' => 'gallery21.jpg',
			  'attachment_id' => '1158',
			),
			26 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery22.jpg',
			  'fileName' => 'gallery22.jpg',
			  'attachment_id' => '1159',
			),
			27 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery23.jpg',
			  'fileName' => 'gallery23.jpg',
			  'attachment_id' => '1160',
			),
			28 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery24.jpg',
			  'fileName' => 'gallery24.jpg',
			  'attachment_id' => '1161',
			),
			29 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/agallery25.jpg',
			  'fileName' => 'gallery25.jpg',
			  'attachment_id' => '1162',
			),
			30 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery26.jpg',
			  'fileName' => 'gallery26.jpg',
			  'attachment_id' => '1163',
			),
			31 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/allery27.jpg',
			  'fileName' => 'allery27.jpg',
			  'attachment_id' => '1164',
			),
			32 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/allery28.jpg',
			  'fileName' => 'allery28.jpg',
			  'attachment_id' => '1165',
			),
			33 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery29.jpg',
			  'fileName' => 'gallery29.jpg',
			  'attachment_id' => '1166',
			),
			34 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery30.jpg',
			  'fileName' => 'gallery30.jpg',
			  'attachment_id' => '1167',
			),
			35 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery31.jpg',
			  'fileName' => 'gallery31.jpg',
			  'attachment_id' => '1168',
			),
			36 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery32.jpg',
			  'fileName' => 'gallery32.jpg',
			  'attachment_id' => '1169',
			),
			37 => array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/04/gallery33.jpg',
			  'fileName' => 'gallery33.jpg',
			  'attachment_id' => '1170',
			),
		),
		'media_gallery' =>  array (
			0 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-11.jpg',
			  'fileName' => 'gallery-11.jpg',
			  'attachment_id' => '290',
			),
			1 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-4.jpg',
			  'fileName' => 'gallery-4.jpg',
			  'attachment_id' => '283',
			),
			2 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-7.jpg',
			  'fileName' => 'gallery-7.jpg',
			  'attachment_id' => '284',
			),
			3 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-8.jpg',
			  'fileName' => 'gallery-8.jpg',
			  'attachment_id' => '288',
			),
			4 => 
			array (
			  'attachment_type' => 'image',
			  'file' => get_home_url().'/wp-content/uploads/2022/03/gallery-10.jpg',
			  'fileName' => 'gallery-10.jpg',
			  'attachment_id' => '289',
			),
		),
		'subject' =>  array (
			21 =>  array (
				'parent_category_id' => 21,
				'parent_category' => array (
					'id' => 21,
					'slug' => 'short-courses',
					'name' => 'Short courses',
				),
				'subcategories' => array (
					0 => array (
						'id' => 22,
						'slug' => 'physical-education',
						'name' => 'Physical education',
					),
					1 =>  array (
						'id' => 25,
						'slug' => 'english',
						'name' => 'English',
					),
					2 => array (
						'id' => 24,
						'slug' => 'history',
						'name' => 'History',
					),
				),

			)
		),		
		'education' =>  array (
			1642762695 => array (
				'degree_title' => 'MBBS, MD, DM rheumatology',
				'institute_title' => 'University of Florida',
				'institute_location' => 'San Francisco, TN',
				'education_start_date' => '2018-06-01',
				'education_end_date' => '',
				'education_description' => 'Accusamus et iusto odio dignissie corrupti quos dolores etolestias excepo officiale deserunt mollitia animi idendication estame laborum.Accusamus etae iusto odioignissie corrupti quos dolores etolestias excepto officiale deserunt mollitia animi endication estame laborum.',
				'currently_studying' => 'on',
			),
			1642762748 =>  array (
				'degree_title' => 'MBBS, MS, Mch neurosurgery',
				'institute_title' => 'University of Florida',
				'institute_location' => 'San Francisco, TN',
				'education_start_date' => '2014-05-01',
				'education_end_date' => '2018-04-30',
				'education_description' => 'Accusamus et iusto odio dignissie corrupti quos dolores etolestias excepo officiale deserunt mollitia animi idendication estame laborum.Accusamus etae iusto odioignissie corrupti quos dolores etolestias excepto officiale deserunt mollitia animi endication estame laborum.',
				'currently_studying' => 'off',
			),
			1642762804 => array (
				'degree_title' => 'MBBS, MD pathology',
				'institute_title' => 'Auburn University',
				'institute_location' => 'Atlanta, CO',
				'education_start_date' => '2011-04-01',
				'education_end_date' => '2013-12-31',
				'education_description' => 'Accusamus et iusto odio dignissie corrupti quos dolores etolestias excepo officiale deserunt mollitia animi idendication estame laborum.Accusamus etae iusto odioignissie corrupti quos dolores etolestias excepto officiale deserunt mollitia animi endication estame laborum.',
				'currently_studying' => '',
			),
		),
		'bookings' =>  array (
			'bookings' => 
			array (
				'timeSlots' => 
				array (
				'bookings_slots' => 
				array (
					'monday' => 
					array (
					'slots' => 
					array (
						4533913241609 => 
						array (
						'time' => '0800-0900',
						'slot' => '2',
						),
						1944850160541 => 
						array (
						'time' => '0900-1000',
						'slot' => '2',
						),
						3074966722062 => 
						array (
						'time' => '1000-1100',
						'slot' => '2',
						),
						3066568607191 => 
						array (
						'time' => '1100-1200',
						'slot' => '2',
						),
						9734086957910 => 
						array (
						'time' => '1200-1300',
						'slot' => '2',
						),
						1263850442679 => 
						array (
						'time' => '1300-1400',
						'slot' => '2',
						),
						8424092599957 => 
						array (
						'time' => '1400-1500',
						'slot' => '2',
						),
						8963719692733 => 
						array (
						'time' => '1500-1600',
						'slot' => '2',
						),
						7785481166183 => 
						array (
						'time' => '1600-1700',
						'slot' => '2',
						),
						2218618518631 => 
						array (
						'time' => '1700-1800',
						'slot' => '2',
						),
						5809556745169 => 
						array (
						'time' => '1800-1900',
						'slot' => '2',
						),
						139754637526 => 
						array (
						'time' => '1900-2000',
						'slot' => '2',
						),
						9678453658391 => 
						array (
						'time' => '2000-2100',
						'slot' => '2',
						),
						9293331027728 => 
						array (
						'time' => '2100-2200',
						'slot' => '2',
						),
					),
					),
					'tuesday' => 
					array (
					'slots' => 
					array (
						8492226317955 => 
						array (
						'time' => '0800-0900',
						'slot' => '2',
						),
						8061473345475 => 
						array (
						'time' => '0900-1000',
						'slot' => '2',
						),
						9614114617689 => 
						array (
						'time' => '1000-1100',
						'slot' => '2',
						),
						5012709759539 => 
						array (
						'time' => '1100-1200',
						'slot' => '2',
						),
						1926155421011 => 
						array (
						'time' => '1200-1300',
						'slot' => '2',
						),
						7364144388593 => 
						array (
						'time' => '1300-1400',
						'slot' => '2',
						),
						8953633797285 => 
						array (
						'time' => '1400-1500',
						'slot' => '2',
						),
						4580809087343 => 
						array (
						'time' => '1500-1600',
						'slot' => '2',
						),
						666543036022 => 
						array (
						'time' => '1600-1700',
						'slot' => '2',
						),
						9617181071705 => 
						array (
						'time' => '1700-1800',
						'slot' => '2',
						),
						2375502418491 => 
						array (
						'time' => '1800-1900',
						'slot' => '2',
						),
						7595998088772 => 
						array (
						'time' => '1900-2000',
						'slot' => '2',
						),
						5674006267880 => 
						array (
						'time' => '2000-2100',
						'slot' => '2',
						),
						256958406538 => 
						array (
						'time' => '2100-2200',
						'slot' => '2',
						),
					),
					),
					'wednesday' => 
					array (
					'slots' => 
					array (
						2555088804737 => 
						array (
						'time' => '0800-0900',
						'slot' => '2',
						),
						6397281755782 => 
						array (
						'time' => '0900-1000',
						'slot' => '2',
						),
						46437846543 => 
						array (
						'time' => '1000-1100',
						'slot' => '2',
						),
						358379687415 => 
						array (
						'time' => '1100-1200',
						'slot' => '2',
						),
						4826815666419 => 
						array (
						'time' => '1200-1300',
						'slot' => '2',
						),
						4720060873823 => 
						array (
						'time' => '1300-1400',
						'slot' => '2',
						),
						3277904170945 => 
						array (
						'time' => '1400-1500',
						'slot' => '2',
						),
						7652612816733 => 
						array (
						'time' => '1500-1600',
						'slot' => '2',
						),
						440463550712 => 
						array (
						'time' => '1600-1700',
						'slot' => '2',
						),
						4886405122113 => 
						array (
						'time' => '1700-1800',
						'slot' => '2',
						),
						9062452994669 => 
						array (
						'time' => '1800-1900',
						'slot' => '2',
						),
						4964718697314 => 
						array (
						'time' => '1900-2000',
						'slot' => '2',
						),
						4743679620280 => 
						array (
						'time' => '2000-2100',
						'slot' => '2',
						),
						8249895374072 => 
						array (
						'time' => '2100-2200',
						'slot' => '2',
						),
					),
					),
					'thursday' => 
					array (
					'slots' => 
					array (
						2980619548165 => 
						array (
						'time' => '0800-0900',
						'slot' => '2',
						),
						7420171660120 => 
						array (
						'time' => '0900-1000',
						'slot' => '2',
						),
						9372292898317 => 
						array (
						'time' => '1000-1100',
						'slot' => '2',
						),
						3179864899670 => 
						array (
						'time' => '1100-1200',
						'slot' => '2',
						),
						3327091837525 => 
						array (
						'time' => '1200-1300',
						'slot' => '2',
						),
						9497189481830 => 
						array (
						'time' => '1300-1400',
						'slot' => '2',
						),
						3964382128033 => 
						array (
						'time' => '1400-1500',
						'slot' => '2',
						),
						582659955671 => 
						array (
						'time' => '1500-1600',
						'slot' => '2',
						),
						646547652350 => 
						array (
						'time' => '1600-1700',
						'slot' => '2',
						),
						9301630186247 => 
						array (
						'time' => '1700-1800',
						'slot' => '2',
						),
						5628961839458 => 
						array (
						'time' => '1800-1900',
						'slot' => '2',
						),
						5938928006503 => 
						array (
						'time' => '1900-2000',
						'slot' => '2',
						),
						4661923047548 => 
						array (
						'time' => '2000-2100',
						'slot' => '2',
						),
						3405887747097 => 
						array (
						'time' => '2100-2200',
						'slot' => '2',
						),
					),
					),
					'friday' => 
					array (
					'slots' => 
					array (
						1331102859607 => 
						array (
						'time' => '0800-0900',
						'slot' => '2',
						),
						3007983230681 => 
						array (
						'time' => '0900-1000',
						'slot' => '2',
						),
						6913243382733 => 
						array (
						'time' => '1000-1100',
						'slot' => '2',
						),
						2749546447752 => 
						array (
						'time' => '1100-1200',
						'slot' => '2',
						),
						2635249746671 => 
						array (
						'time' => '1200-1300',
						'slot' => '2',
						),
						1800211868266 => 
						array (
						'time' => '1300-1400',
						'slot' => '2',
						),
						6747958370776 => 
						array (
						'time' => '1400-1500',
						'slot' => '2',
						),
						4709931585129 => 
						array (
						'time' => '1500-1600',
						'slot' => '2',
						),
						6429800320008 => 
						array (
						'time' => '1600-1700',
						'slot' => '2',
						),
						8531893521497 => 
						array (
						'time' => '1700-1800',
						'slot' => '2',
						),
						9520796003262 => 
						array (
						'time' => '1800-1900',
						'slot' => '2',
						),
						5904735023954 => 
						array (
						'time' => '1900-2000',
						'slot' => '2',
						),
						2521033455193 => 
						array (
						'time' => '2000-2100',
						'slot' => '2',
						),
						8906336071093 => 
						array (
						'time' => '2100-2200',
						'slot' => '2',
						),
					),
					),
				),
				),
				'unavailabledays' => 
				array (
				),
			),		
		),		
	);		
}
