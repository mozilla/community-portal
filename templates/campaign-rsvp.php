<div class="lightbox__container campaign-rsvp">
	<button id="close-rsvp-lightbox" class="btn btn--close">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M25 1L1 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 1L25 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <h2 class="campaign-rsvp__title title"><?php print __('Get Involved', 'community-portal')?></h2>
	<div>
		<h3 class="campaign-rsvp__subtitle"><?php print __('Sign Up', 'community-portal') ?></h3>
		<p>
			<?php print __('Create a Community Portal account to connect with the community and participate in a number of exciting campaigns.', 'community-portal')?>
		</p>
		<div class="campaign-rsvp__section--center">
			<a class="btn btn--dark btn--submit" href=""><?php print __('Sign Up', 'community-portal') ?></a>
			<p>
				<?php print __('Already have an account?', 'community-portal') ?>
				<a href="/wp-login.php?action=login" class="nav__login-link"><?php print __('Log In', 'community-portal') ?></a>
			</p>
		</div>
	</div>
	<div class="campaign-rsvp__form">
		<h3 class="campaign-rsvp__subtitle"><?php print __('Get involved without a user account', 'community-portal') ?></h3>
		<p><?php print __('Fill out the form below to join this campaign without creating a user account', 'community-portal') ?></p>
		<form method="post">
			<div class="row campaign-rsvp__section">
				<div class="col-lg-6 campaign-rsvp__row campaign-rsvp__row--double">
					<label class="campaign-rsvp__label" for="first-name"><?php print __('First Name', 'community-portal') ?></label>
					<input class="campaign-rsvp__input" type="text" id="first-name" name="first-name" required>
				</div>
				<div class="col-lg-6 campaign-rsvp__row campaign-rsvp__row--double">
					<label class="campaign-rsvp__label" for="last-name"><?php print __('Last Name', 'community-portal') ?></label>
					<input class="campaign-rsvp__input" type="text" id="last-name" name="last-name" required>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 campaign-rsvp__row">
					<label class="campaign-rsvp__label" for="email"><?php print __('Email') ?></label>
					<input class="campaign-rsvp__input" type="email" name="email" required>
				</div>
				<div class="col-sm-12 cpg">
					<input class="checkbox--hidden" type="checkbox" name="privacy-policy" id="privacy-policy">
					<label class="cpg__label" for="privacy-policy">
						<?php print __('I\'m okay with Mozilla handling my info as explained in this', 'community-portal')?> 
						<a href="#"><?php print __('Privacy Policy', 'community-portal')?></a>
					</label>
				</div>
				<div class="col-sm-12 campaign-rsvp__section--center">
					<input class="btn btn--light btn--submit" type="submit" value="<?php print __('Get Involved', 'community-portal') ?>">
				</div>
			</div>


		</form>
	</div>
</div>