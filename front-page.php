<?php get_header(); ?>
	<div class="content">
		<?php 
			$fields = array(
				'hero_title',
				'hero_subtitle',
				'hero_image',
				'hero_cta_existing',
				'hero_cta_new',
				'hero_cta_text',
				'featured_events',
				'featured_events_title',
				'featured_events_cta_text',
				'featured_events_secondary_cta_text',
				'featured_groups',
				'featured_groups_title',
				'featured_groups_cta_text',
				'featured_groups_secondary_cta_text'
			);

			$fieldValues = new stdClass();
			foreach ($fields as $field) {
				$fieldValues->$field = get_field($field);
			}
		?>
		<div class="homepage homepage__container">
		<div class="homepage__hero">
			<div class="homepage__hero__background">
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-offset-1 homepage__hero__splash">
					<div class="homepage__hero__image">
						<img src="<?php echo $fieldValues->hero_image['url'] ?>" alt="<?php echo $fieldValues->hero_image['alt'] ?>">
					</div>
				</div>
				<div class="col-md-5 col-md-offset-1 homepage__hero__text">
					<h1 class="homepage__hero__title title title--main"><?php echo preg_replace('/\b(Community)\b/i', '<span>$0</span>', $fieldValues->hero_title); ?></h1>
					<p class="homepage__hero__subtitle subtitle"><?php echo $fieldValues->hero_subtitle ?></p>
					<a href="<?php echo (is_user_logged_in() ? esc_attr($fieldValues->hero_cta_existing) : esc_attr($fieldValues->hero_cta_new)) ?>"class="btn btn--dark btn--small homepage__hero__cta"><?php echo __($fieldValues->hero_cta_text) ?></a>
				</div>
				</div>
			</div>
			<div class="homepage__events">
				<div class="homepage__events__background"></div>
				<div class="row homepage__events__meta">
					<div class="col-md-6 col-sm-12">
						<h2 class="subheader homepage__events__subheader"><?php echo __($fieldValues->featured_events_title)?></h2>
					</div>
					<div class="col-md-6 col-sm-12 homepage__events__cta">
						<a href="/events" class="btn btn--small btn--dark"><?php echo __($fieldValues->featured_events_cta_text); ?></a>
					</div>
				</div>
				<div class="row homepage__events__grid">
					<?php 
						foreach($fieldValues->featured_events as $featured_event):
							if($featured_event['single_event']) {
								$event = EM_Events::get(array('post_id' => $featured_event['single_event']->ID, 'scope'	=>	'all'));
								$event = array_shift(array_values($event));
					
								include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
							} 
						endforeach;
					?>
					<div class="col-lg-4 col-md-6 events__column homepage__events__count">
						<?php 
							$eventsTotal = count(EM_Events::get());
							if ($eventsTotal > 15 && $eventsTotal <= 105):
								$eventsTotal = floor(($eventsTotal / 10)) * 10;
								$eventsTotal .= '+';
							elseif ($eventsTotal > 105 && $eventsTotal <= 1005):
								$eventsTotal = floor(($eventsTotal) / 100) * 100;
								$eventsTotal .= '+';
							elseif ($eventsTotal > 1005): 
								$eventsTotal = floor(($eventsTotal / 1000) * 1000);
								$eventsTotal .= '+';
							else: 
								$eventsTotal = 10;
							endif;
						?>
						<p>
							<span class="large-number homepage__events__count__span"><?php echo __($eventsTotal) ?></span>
							<?php echo __('More Events.')?>
							<a href="/events" class="homepage__events__count__link"><?php echo __($fieldValues->featured_events_secondary_cta_text) ?></a>
						</p>
					</div>
				</div>
			</div>
			<?php if(sizeof($fieldValues->featured_groups) > 0): ?>
			<div class="homepage__groups">
				<div class="homepage__groups__background"></div>
				<div class="row homepage__groups__meta">
					<div class="col-md-6 col-sm-12">
						<h2 class="subheader homepage__groups__subheader"><?php echo __($fieldValues->featured_groups_title)?></h2>
					</div>
					<div class="col-md-6 col-sm-12 homepage__groups__cta">
						<a href="/events" class="btn btn--small btn--dark"><?php echo __($fieldValues->featured_groups_cta_text); ?></a>
					</div>
				</div>
				<div class="row homepage__groups__grid">
					<?php 
						foreach($fieldValues->featured_groups as $featured_group):
							$group = groups_get_group(array('group_id' => $featured_group['featured_group']));
							$meta = groups_get_groupmeta($group->id, 'meta');
							$member_count = groups_get_total_member_count($group->id);
					?>
					<div class="col-lg-4 col-md-6 groups__column">
						<a href="/groups/<?php print $group->slug; ?>/" class="groups__card groups__card--homepage">
							<?php 
								if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
									$meta['group_image_url'] = preg_replace("/^http:/i", "https:", $meta['group_image_url']);
								}
							?>

							<div class="groups__group-image" style="background-image: url('<?php print (isset($meta['group_image_url']) && strlen($meta['group_image_url']) > 0) ? $meta['group_image_url'] : get_stylesheet_directory_uri().'/images/group.png'; ?>');">
							</div>
							<div class="groups__card-content">
								<h2 class="groups__group-title"><?php print str_replace('\\', '', stripslashes($group->name)); ?></h2>
								<?php if(isset($meta['group_city']) && strlen(trim($meta['group_city'])) > 0 || isset($meta['group_country']) && $meta['group_country'] != "0"): ?>
								<div class="groups__card-location">
										<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php 
											print trim($meta['group_city']);?><?php 
											if(isset($meta['group_country']) && strlen($meta['group_country']) > 0) {
												if(isset($meta['group_city']) && strlen($meta['group_city']) > 0) {
													print trim(", {$countries[$meta['group_country']]}");
												} else {
													print $countries[$meta['group_country']];
												}
											}
										?>
								</div>
								<?php endif; ?>
								<div class="groups__card-members">
										<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M12.3334 14V12.6667C12.3334 11.9594 12.0525 11.2811 11.5524 10.781C11.0523 10.281 10.374 10 9.66675 10H4.33341C3.62617 10 2.94789 10.281 2.4478 10.781C1.9477 11.2811 1.66675 11.9594 1.66675 12.6667V14" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M6.99992 7.33333C8.47268 7.33333 9.66659 6.13943 9.66659 4.66667C9.66659 3.19391 8.47268 2 6.99992 2C5.52716 2 4.33325 3.19391 4.33325 4.66667C4.33325 6.13943 5.52716 7.33333 6.99992 7.33333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M16.3333 14.0002V12.6669C16.3328 12.0761 16.1362 11.5021 15.7742 11.0351C15.4122 10.5682 14.9053 10.2346 14.3333 10.0869" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M11.6667 2.08691C12.2404 2.23378 12.7488 2.56738 13.1118 3.03512C13.4749 3.50286 13.672 4.07813 13.672 4.67025C13.672 5.26236 13.4749 5.83763 13.1118 6.30537C12.7488 6.77311 12.2404 7.10671 11.6667 7.25358" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php print "{$member_count}&nbsp;".__("Members"); ?>
								</div>
								<div class="groups__card-info">
									<div class="groups__card-tags">
										<?php 
												$tag_counter = 0;
										?>
										<?php foreach($meta['group_tags'] AS $key =>  $value): ?>
											<span class="groups__tag"><?php print $value; ?></span>
											<?php $tag_counter++; ?>
											<?php if($tag_counter === 2 && sizeof($meta['group_tags']) > 2): ?>
												<span class="groups__tag">+ <?php print sizeof($meta['group_tags']) - 2; ?> <?php print __(' more tags'); ?></span>
												<?php break; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</a>
					</div>
					<?php endforeach; ?>
					<div class="col-lg-4 col-md-6 events__column homepage__events__count">
						<?php
							$groups_total = count(groups_get_groups(array()));
							if ($groups_total > 15 && $groups_total <= 105):
								$groups_total = floor(($groups_total / 15)) * 15;
								$groups_total .= '+';
							elseif ($groups_total > 105 && $groups_total <= 1005):
								$groups_total = floor(($groups_total) / 105) * 105;
								$groups_total .= '+';
							elseif ($groups_total > 1005): 
								$groups_total = floor(($groups_total / 1005) * 1005);
								$groups_total .= '+';
							else: 
								$groups_total = 10;
							endif;
						?>
						<p>
							<span class="large-number homepage__events__count__span"><?php echo __($groups_total) ?></span>
							<?php echo __('More Groups.')?>
							<a href="/groups/" class="homepage__events__count__link"><?php echo __($fieldValues->featured_groups_secondary_cta_text) ?></a>
						</p>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
<?php get_footer(); ?>