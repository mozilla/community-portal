<?php
/**
 * Used by the buddypress and front-end editors to display event time-related information
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

/** Ticket Class */
class EM_Ticket extends EM_Object {
	/**  DB Fields. */
	/**
	 * ID for ticket
	 *
	 * @var unknown_type
	 */
	public $ticket_id;
	/**
	 * ID for event
	 *
	 * @var unknown_type
	 */
	public $event_id;
	/**
	 * Name for ticket
	 *
	 * @var string
	 * */
	public $ticket_name;
		/**
		 * Description of ticket
		 *
		 * @var string
		 * */
	public $ticket_description;
		/**
		 * Price of ticket
		 *
		 * @var string
		 * */
	public $ticket_price;
	/**
	 * Start for ticket
	 *
	 * @var string
	 * */
	protected $ticket_start;
	/**
	 * End for ticket
	 *
	 * @var string
	 * */
	protected $ticket_end;
	/**
	 * Min for ticket
	 *
	 * @var string
	 * */
	public $ticket_min;
	/**
	 * Max for ticket
	 *
	 * @var string
	 * */
	public $ticket_max;
	/**
	 * Spaces for ticket
	 *
	 * @var string
	 * */
	public $ticket_spaces = 9999;
	/**
	 * Members for ticket
	 *
	 * @var array
	 * */
	public $ticket_members = false;
	/**
	 * Member roles for ticket
	 *
	 * @var array
	 * */
	public $ticket_members_roles = array();
	/**
	 * Guests for ticket
	 *
	 * @var array
	 * */
	public $ticket_guests = false;
	/**
	 * Is ticket required
	 *
	 * @var boolean
	 * */
	public $ticket_required = false;
	/**
	 * Meta for ticket
	 *
	 * @var array
	 * */
	public $ticket_meta = array();
	/**
	 * Fields for ticket
	 *
	 * @var array
	 * */
	public $fields = array(
		'ticket_id'            => array(
			'name' => 'id',
			'type' => '%d',
		),
		'event_id'             => array(
			'name' => 'event_id',
			'type' => '%d',
		),
		'ticket_name'          => array(
			'name' => 'name',
			'type' => '%s',
		),
		'ticket_description'   => array(
			'name' => 'description',
			'type' => '%s',
			'null' => 1,
		),
		'ticket_price'         => array(
			'name' => 'price',
			'type' => '%f',
			'null' => 1,
		),
		'ticket_start'         => array(
			'type' => '%s',
			'null' => 1,
		),
		'ticket_end'           => array(
			'type' => '%s',
			'null' => 1,
		),
		'ticket_min'           => array(
			'name' => 'min',
			'type' => '%s',
			'null' => 1,
		),
		'ticket_max'           => array(
			'name' => 'max',
			'type' => '%s',
			'null' => 1,
		),
		'ticket_spaces'        => array(
			'name' => 'spaces',
			'type' => '%s',
			'null' => 1,
		),
		'ticket_members'       => array(
			'name' => 'members',
			'type' => '%d',
			'null' => 1,
		),
		'ticket_members_roles' => array(
			'name' => 'ticket_members_roles',
			'type' => '%s',
			'null' => 1,
		),
		'ticket_guests'        => array(
			'name' => 'guests',
			'type' => '%d',
			'null' => 1,
		),
		'ticket_required'      => array(
			'name' => 'required',
			'type' => '%d',
			'null' => 1,
		),
		'ticket_meta'          => array(
			'name' => 'ticket_meta',
			'type' => '%s',
			'null' => 1,
		),
	);
	// Other Vars.
	/**
	 * Contains only bookings belonging to this ticket.
	 *
	 * @var EM_Booking
	 */
	public $bookings = array();
	/**
	 * Required fields
	 *
	 * @var array
	 * */
	public $required_fields = array( 'ticket_name' );
	/**
	 * Start
	 *
	 * @var string
	 * */
	protected $start;
	/**
	 * End
	 *
	 * @var string
	 * */
	protected $end;
	/**
	 * Is this ticket limited by spaces allotted to this ticket? false if no limit (i.e. the events general limit of seats).
	 *
	 * @var unknown_type
	 */
	public $spaces_limit = true;

	/**
	 * An associative array containing event IDs as the keys and pending spaces as values.
	 * This is in array form for future-proofing since at one point tickets could be used for multiple events.
	 *
	 * @var array
	 */
	protected $pending_spaces = array();
	/**
	 * Booked spaces
	 *
	 * @var array
	 * */
	protected $booked_spaces = array();
	/**
	 * Bookings count
	 *
	 * @var array
	 * */
	protected $bookings_count = array();

	/**
	 * Creates ticket object and retreives ticket data (default is a blank ticket object). Accepts either array of ticket data (from db) or a ticket id.
	 *
	 * @param mixed $ticket_data ticket data.
	 * @return null
	 */
	public function __construct( $ticket_data = false ) {
		$this->ticket_name = __( 'Standard Ticket', 'community-portal' );
		$ticket            = array();
		if ( false !== $ticket_data ) {
			// Load ticket data.
			if ( is_array( $ticket_data ) ) {
				$ticket = $ticket_data;
			} elseif ( is_numeric( $ticket_data ) ) {
				// Retreiving from the database.
				global $wpdb;
				$sql    = 'SELECT * FROM ' . EM_TICKETS_TABLE . " WHERE ticket_id ='$ticket_data'"; // phpcs:ignore
				$ticket = $wpdb->get_row( $sql, ARRAY_A ); // phpcs:ignore
			}
			// Save into the object.
			$this->to_object( $ticket );
			// serialized arrays.
			$this->ticket_meta          = ( ! empty( $ticket['ticket_meta'] ) ) ? maybe_unserialize( $ticket['ticket_meta'] ) : array();
			$this->ticket_members_roles = maybe_unserialize( $this->ticket_members_roles );
			if ( ! is_array( $this->ticket_members_roles ) ) {
				$this->ticket_members_roles = array();
			}
			// sort out recurrence meta to save extra empty() checks, the 'true' cut-off info is here for the ticket if part of a recurring event.
			if ( ! empty( $this->ticket_meta['recurrences'] ) ) {
				if ( ! array_key_exists( 'start_days', $this->ticket_meta['recurrences'] ) ) {
					$this->ticket_meta['recurrences']['start_days'] = false;
				}
				if ( ! array_key_exists( 'end_days', $this->ticket_meta['recurrences'] ) ) {
					$this->ticket_meta['recurrences']['end_days'] = false;
				}
				if ( ! array_key_exists( 'start_time', $this->ticket_meta['recurrences'] ) ) {
					$this->ticket_meta['recurrences']['start_time'] = false;
				}
				if ( ! array_key_exists( 'end_time', $this->ticket_meta['recurrences'] ) ) {
					$this->ticket_meta['recurrences']['end_time'] = false;
				}
				// if we have start and end times, we'll set the ticket start/end properties.
				if ( ! empty( $this->ticket_meta['recurrences']['start_time'] ) ) {
					$this->ticket_start = gmdate( 'Y-m-d ' ) . $this->ticket_meta['recurrences']['start_time'];
				}
				if ( ! empty( $this->ticket_meta['recurrences']['end_time'] ) ) {
					$this->ticket_end = gmdate( 'Y-m-d ' ) . $this->ticket_meta['recurrences']['end_time'];
				}
			}
		}
		$this->compat_keys();
		do_action( 'em_ticket', $this, $ticket_data, $ticket );
	}

	/**
	 * Get var
	 *
	 * @param mixed $var variable.
	 * @return null
	 */
	private function __get( $var ) {
		if ( 'ticket_start' === $var || 'ticket_end' === $var ) {
			return $this->$var;
		} elseif ( 'start_timestamp' === $var || 'start' === $var ) {
			if ( ! $this->start()->valid ) {
				return 0;
			}
			return $this->start()->getTimestampWithOffset();
		} elseif ( 'end_timestamp' === $var || 'end' === $var ) {
			if ( ! $this->end()->valid ) {
				return 0;
			}
			return $this->end()->getTimestampWithOffset();
		}
		return null;
	}

	/**
	 * Set var
	 *
	 * @param mixed $prop property.
	 * @param mixed $val value.
	 * @return null
	 */
	private function __set( $prop, $val ) {
		if ( 'ticket_start' === $prop ) {
			$this->$prop = $val;
			$this->start = false;
		} elseif ( 'ticket_end' === $prop ) {
			$this->$prop = $val;
			$this->end   = false;
		} elseif ( 'start_timestamp' === $prop ) {
			if ( false !== $this->start() ) {
				$this->start()->setTimestamp( $val );
			}
		} elseif ( 'end_timestamp' === $prop ) {
			if ( $false !== $this->end() ) {
				$this->end()->setTimestamp( $val );
			}
		} elseif ( 'start' === $prop || 'end' === $prop ) {
			// start and end properties are inefficient to set, and deprecated. Set ticket_start and ticket_end with a valid MySQL DATETIME value instead.
			$em_date_time = new EM_DateTime( $val, $this->get_event()->get_timezone() );
			if ( ! $em_date_time->valid ) {
				return false;
			}
			$when_prop        = 'ticket_' . $prop;
			$this->$when_prop = $em_date_time->getDateTime();
		}
		$this->$prop = $val;
	}

	/**
	 * Set isset
	 *
	 * @param mixed $prop variable.
	 * @return null
	 */
	public function __isset( $prop ) {
		// start_timestamp and end_timestamp are deprecated, don't use them anymore.
		if ( 'ticket_start' === $prop || 'start_timestamp' === $prop ) {
			return ! empty( $this->ticket_start );
		} elseif ( 'ticket_end' === $prop || 'end_timestamp' === $prop ) {
			return ! empty( $this->ticket_end );
		}
	}

	/**
	 * Get notes
	 *
	 * @return null
	 */
	private function get_notes() {
		global $wpdb;
		if ( ! is_array( $this->notes ) && ! empty( $this->ticket_id ) ) {
			$notes = $wpdb->get_results( 'SELECT * FROM ' . EM_META_TABLE . " WHERE meta_key='ticket-note' AND object_id ='{$this->ticket_id}'", ARRAY_A ); // phpcs:ignore
			foreach ( $notes as $note ) {
				$this->ticket_id[] = unserialize( $note['meta_value'] );
			}
		} elseif ( empty( $this->booking_id ) ) {
			$this->notes = array();
		}
		return $this->notes;
	}

	/**
	 * Saves the ticket into the database, whether a new or existing ticket
	 *
	 * @return boolean
	 */
	public function save() {
		global $wpdb;
		$table = EM_TICKETS_TABLE;
		do_action( 'em_ticket_save_pre', $this );
		// First the person.
		if ( $this->validate() && $this->can_manage() ) {
			// Now we save the ticket.
			$data = $this->to_array( true ); // add the true to remove the nulls.
			if ( ! empty( $data['ticket_meta'] ) ) {
				$data['ticket_meta'] = serialize( $data['ticket_meta'] );
			}
			if ( ! empty( $data['ticket_members_roles'] ) ) {
				$data['ticket_members_roles'] = serialize( $data['ticket_members_roles'] );
			}
			if ( ! empty( $this->ticket_meta['recurrences'] ) ) {
				$data['ticket_end']   = null;
				$data['ticket_start'] = $dat['ticket_end'];
			}
			if ( '' !== $this->ticket_id ) {
				// since currently wpdb calls don't accept null, let's build the sql ourselves.
				$set_array = array();
				foreach ( $this->fields as $field_name => $field ) {
					if ( empty( $data[ $field_name ] ) && $field['null'] ) {
						$set_array[] = "{$field_name}=NULL";
					} else {
						$set_array[] = "{$field_name}='" . esc_sql( $data[ $field_name ] ) . "'";
					}
				}
				$sql                    = "UPDATE $table SET " . implode( ', ', $set_array ) . " WHERE ticket_id={$this->ticket_id}"; // phpcs:ignore
				$result                 = $wpdb->query( $sql ); // phpcs:ignore
				$this->feedback_message = __( 'Changes saved', 'community-portal' );
			} else {
				if ( isset( $data['ticket_id'] ) && empty( $data['ticket_id'] ) ) {
					unset( $data['ticket_id'] );
				}
				$result                 = $wpdb->insert( $table, $data, $this->get_types( $data ) );
				$this->ticket_id        = $wpdb->insert_id;
				$this->feedback_message = __( 'Ticket created', 'community-portal' );
			}
			if ( false === $result ) {
				$this->feedback_message = __( 'There was a problem saving the ticket.', 'community-portal' );
				$this->errors[]         = __( 'There was a problem saving the ticket.', 'community-portal' );
			}
			$this->compat_keys();
			return apply_filters( 'em_ticket_save', ( count( $this->errors ) === 0 ), $this );
		} else {
			$this->feedback_message = __( 'There was a problem saving the ticket.', 'community-portal' );
			$this->errors[]         = __( 'There was a problem saving the ticket.', 'community-portal' );
			return apply_filters( 'em_ticket_save', false, $this );
		}
		return true;
	}

	/**
	 * Get posted data and save it into the object (not db)
	 *
	 * @param mixed $post the post.
	 * @return void
	 */
	public function get_post( $post = array() ) {
		// We are getting the values via POST or GET.
		global $allowedposttags;
		if ( empty( $post ) ) {
			$post = $_REQUEST;
		}
		do_action( 'em_ticket_get_post_pre', $this, $post );
		$this->ticket_id          = ( ! empty( $post['ticket_id'] ) && is_numeric( $post['ticket_id'] ) ) ? $post['ticket_id'] : '';
		$this->event_id           = ( ! empty( $post['event_id'] ) && is_numeric( $post['event_id'] ) ) ? $post['event_id'] : '';
		$this->ticket_name        = ( ! empty( $post['ticket_name'] ) ) ? wp_kses_data( wp_unslash( $post['ticket_name'] ) ) : '';
		$this->ticket_description = ( ! empty( $post['ticket_description'] ) ) ? wp_kses( wp_unslash( $post['ticket_description'] ), $allowedposttags ) : '';
		// spaces and limits.
		$this->ticket_min    = ( ! empty( $post['ticket_min'] ) && is_numeric( $post['ticket_min'] ) ) ? $post['ticket_min'] : '';
		$this->ticket_max    = ( ! empty( $post['ticket_max'] ) && is_numeric( $post['ticket_max'] ) ) ? $post['ticket_max'] : '';
		$this->ticket_spaces = ( ! empty( $post['ticket_spaces'] ) && is_numeric( $post['ticket_spaces'] ) ) ? $post['ticket_spaces'] : 10;
		// sort out price and un-format in the event of special decimal/thousand seperators.
		$price = ( ! empty( $post['ticket_price'] ) ) ? wp_kses_data( $post['ticket_price'] ) : '';
		if ( preg_match( '/^[0-9]*\.[0-9]+$/', $price ) || preg_match( '/^[0-9]+$/', $price ) ) {
			$this->ticket_price = $price;
		} else {
			$this->ticket_price = str_replace( array( get_option( 'dbem_bookings_currency_thousands_sep' ), get_option( 'dbem_bookings_currency_decimal_point' ) ), array( '', '.' ), $price );
		}
		// Sort out date/time limits.
		$this->ticket_start = ( ! empty( $post['ticket_start'] ) ) ? wp_kses_data( $post['ticket_start'] ) : '';
		$this->ticket_end   = ( ! empty( $post['ticket_end'] ) ) ? wp_kses_data( $post['ticket_end'] ) : '';
		$start_time         = ! empty( $post['ticket_start_time'] ) ? $post['ticket_start_time'] : $this->get_event()->start()->format( 'H:i' );
		if ( ! empty( $this->ticket_start ) ) {
			$this->ticket_start .= ' ' . $this->sanitize_time( $start_time );
		}
		$end_time = ! empty( $post['ticket_end_time'] ) ? $post['ticket_end_time'] : $this->get_event()->start()->format( 'H:i' );
		if ( ! empty( $this->ticket_end ) ) {
			$this->ticket_end .= ' ' . $this->sanitize_time( $end_time );
		}
		// sort out user availability restrictions.
		$this->ticket_members       = ( ! empty( $post['ticket_type'] ) && 'members' === $post['ticket_type'] ) ? 1 : 0;
		$this->ticket_guests        = ( ! empty( $post['ticket_type'] ) && 'guests' === $post['ticket_type'] ) ? 1 : 0;
		$this->ticket_members_roles = array();
		if ( $this->ticket_members && ! empty( $post['ticket_members_roles'] ) && is_array( $post['ticket_members_roles'] ) ) {
			$event_roles = new WP_Roles();
			foreach ( $event_roles->roles as $event_role => $role_data ) {
				if ( in_array( $event_role, $post['ticket_members_roles'], true ) ) {
					$this->ticket_members_roles[] = $event_role;
				}
			}
		}
		$this->ticket_required = ( ! empty( $post['ticket_required'] ) ) ? 1 : 0;
		// if event is recurring, store start/end restrictions of this ticket, which are determined by number of days before (negative number) or after (positive number) the event start date.
		if ( $this->get_event()->is_recurring() ) {
			if ( empty( $this->ticket_meta['recurrences'] ) ) {
				$this->ticket_meta['recurrences'] = array(
					'start_days' => false,
					'start_time' => false,
					'end_days'   => false,
					'end_time'   => false,
				);
			}
			foreach ( array( 'start', 'end' ) as $start_or_end ) {
				// start/end of ticket cut-off.
				if ( array_key_exists( 'ticket_' . $start_or_end . '_recurring_days', $post ) && is_numeric( $post[ 'ticket_' . $start_or_end . '_recurring_days' ] ) ) {
					if ( ! empty( $post[ 'ticket_' . $start_or_end . '_recurring_when' ] ) && 'after' === $post[ 'ticket_' . $start_or_end . '_recurring_when' ] ) {
						$this->ticket_meta['recurrences'][ $start_or_end . '_days' ] = absint( $post[ 'ticket_' . $start_or_end . '_recurring_days' ] );
					} else { // by default the start/end date is the point of reference.
						$this->ticket_meta['recurrences'][ $start_or_end . '_days' ] = absint( $post[ 'ticket_' . $start_or_end . '_recurring_days' ] ) * -1;
					}
					$this->ticket_meta['recurrences'][ $start_or_end . '_time' ] = ( ! empty( $post[ 'ticket_' . $start_or_end . '_time' ] ) ) ? $this->sanitize_time( $post[ 'ticket_' . $start_or_end . '_time' ] ) : $this->get_event()->$start_or_end()->format( 'H:i' );
				} else {
					unset( $this->ticket_meta['recurrences'][ $start_or_end . '_days' ] );
					unset( $this->ticket_meta['recurrences'][ $start_or_end . '_time' ] );
				}
			}
			$this->ticket_end   = null;
			$this->ticket_start = $this->ticket_end;
		}
		$this->compat_keys();
		do_action( 'em_ticket_get_post', $this, $post );
	}


	/**
	 * Validates the ticket for saving. Should be run during any form submission or saving operation.
	 *
	 * @return boolean
	 */
	public function validate() {
		$missing_fields = array();
		$this->errors   = array();
		foreach ( $this->required_fields as $field ) {
			if ( '' === $this->$field ) {
				$missing_fields[] = $field;
			}
		}
		if ( ! empty( $this->ticket_price ) && ! is_numeric( $this->ticket_price ) ) {
			$this->add_error( esc_html__( 'Please enter a valid ticket price e.g. 10.50 (no currency signs)', 'community-portal' ) );
		}
		if ( ! empty( $this->ticket_min ) && ! empty( $this->ticket_max ) && $this->ticket_max < $this->ticket_min ) {
			/* translators: %s: ticket id */
			$error = esc_html__( 'Ticket %s has a higher minimum spaces requirement than the maximum spaces allowed.', 'community-portal' );
			$this->add_error( sprintf( $error, '<em>' . esc_html( $this->ticket_name ) . '</em>' ) );
		}
		if ( count( $missing_fields ) > 0 ) {
			// TODO Create friendly equivelant names for missing fields notice in validation.
			$this->errors[] = __( 'Missing fields: ' ) . implode( ', ', $missing_fields ) . '. ';
		}
		return apply_filters( 'em_ticket_validate', count( $this->errors ) === 0, $this );
	}
	/**
	 * Is available function.
	 *
	 * @param mixed $ignore_member_restrictions ignore member restrictions.
	 * @param mixed $ignore_guest_restrictions ignore guest restrictions.
	 * @return boolean
	 */
	public function is_available( $ignore_member_restrictions = false, $ignore_guest_restrictions = false ) {
		if ( isset( $this->is_available ) && ! $ignore_member_restrictions && ! $ignore_guest_restrictions ) {
			return apply_filters( 'em_ticket_is_available', $this->is_available, $this ); // save extra queries if doing a standard check.
		}
		$is_available     = false;
		$em_event         = $this->get_event();
		$available_spaces = $this->get_available_spaces();
		$condition_1      = empty( $this->ticket_start ) || $this->start()->getTimestamp() <= time();
		$condition_2      = empty( $this->ticket_end ) || $this->end()->getTimestamp() >= time();
		$condition_3      = $em_event->rsvp_end()->getTimestamp() > time(); // either defined ending rsvp time, or start datetime is used here.
		$condition_4      = ! $this->ticket_members || ( $this->ticket_members && is_user_logged_in() ) || $ignore_member_restrictions;
		$condition_5      = true;
		if ( ! $ignore_member_restrictions && ! EM_Bookings::$disable_restrictions && $this->ticket_members && ! empty( $this->ticket_members_roles ) ) {
			// check if user has the right role to use this ticket.
			$condition_5 = false;
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				if ( count( array_intersect( $user->roles, $this->ticket_members_roles ) ) > 0 ) {
					$condition_5 = true;
				}
			}
		}
		$condition_6 = ! $this->ticket_guests || ( $this->ticket_guests && ! is_user_logged_in() ) || $ignore_guest_restrictions;
		if ( $condition_1 && $condition_2 && $condition_3 && $condition_4 && $condition_5 && $condition_6 ) {
			// Time Constraints met, now quantities.
			if ( $available_spaces > 0 && ( $available_spaces >= $this->ticket_min || empty( $this->ticket_min ) ) ) {
				$is_available = true;
			}
		}
		if ( ! $ignore_member_restrictions && ! $ignore_guest_restrictions ) { // $this->is_available is only stored for the viewing user.
			$this->is_available = $is_available;
		}
		return apply_filters( 'em_ticket_is_available', $is_available, $this, $ignore_guest_restrictions, $ignore_member_restrictions );
	}

	/**
	 * Returns whether or not this ticket should be displayed based on availability and other ticket properties and general settings
	 *
	 * @param bool $ignore_member_restrictions ignore member restrictions.
	 * @param bool $ignore_guest_restrictions ignore guest restrictions.
	 * @return boolean
	 */
	public function is_displayable( $ignore_member_restrictions = false, $ignore_guest_restrictions = false ) {
		$return = false;
		if ( $this->is_available( $ignore_member_restrictions, $ignore_guest_restrictions ) ) {
			$return = true;
		} else {
			if ( get_option( 'dbem_bookings_tickets_show_unavailable' ) ) {
				$return = true;
				if ( $this->ticket_members && ! get_option( 'dbem_bookings_tickets_show_member_tickets' ) ) {
					$return = false;
				}
			}
		}
		return apply_filters( 'em_ticket_is_displayable', $return, $this, $ignore_guest_restrictions, $ignore_member_restrictions );
	}

	/**
	 * Gets the total price for this ticket, includes tax if settings dictates that tax is added to ticket price.
	 * Use $this->ticket_price or $this->get_price_without_tax() if you definitely don't want tax included.
	 *
	 * @param boolean $format format.
	 * @return float
	 */
	public function get_price( $format = false ) {
		$price = $this->ticket_price;
		if ( get_option( 'dbem_bookings_tax_auto_add' ) ) {
			$price = $this->get_price_with_tax();
		}
		$price = apply_filters( 'em_ticket_get_price', $price, $this );
		if ( $format ) {
			return $this->format_price( $price );
		}
		return $price;
	}

	/**
	 * Calculates how much the individual ticket costs with applicable event/site taxes included.
	 *
	 * @param boolean $format format.
	 * @return float|int|string
	 */
	public function get_price_with_tax( $format = false ) {
		$price = $this->get_price_without_tax() * ( 1 + $this->get_event()->get_tax_rate( true ) );
		if ( $format ) {
			return $this->format_price( $price );
		}
		return $price;
	}

	/**
	 * Calculates how much the individual ticket costs with taxes excluded.
	 *
	 * @param boolean $format format.
	 * @return float|int|string
	 */
	public function get_price_without_tax( $format = false ) {
		if ( $format ) {
			return $this->format_price( $this->ticket_price );
		}
		return $this->ticket_price;
	}

	/**
	 * Shows the ticket price which can contain long decimals but will show up to 2 decimal places and remove trailing 0s
	 * For example: 10.010230 => 10.01023 and 10 => 10.00
	 *
	 * @param bool $format If true, the number is provided with localized digit separator and padded with 0, 2 or 4 digits.
	 * @return float|int|string
	 */
	public function get_price_precise( $format = false ) {
		$price = $this->ticket_price * 1;
		if ( floor( $price ) === (float) $price ) {
			$price = number_format( $price, 2, '.', '' );
		}
		if ( $format ) {
			$digits    = strlen( substr( strrchr( $price, '.' ), 1 ) );
			$precision = ( $digits > 2 ) ? 4 : 2;
			$price     = number_format( $price, $precision, get_option( 'dbem_bookings_currency_decimal_point', '.' ), '' );
		}
		return $price;
	}

	/**
	 * Get the total number of tickets (spaces) available, bearing in mind event-wide maxiumums and ticket priority settings.
	 *
	 * @return int
	 */
	public function get_spaces() {
		return apply_filters( 'em_ticket_get_spaces', $this->ticket_spaces, $this );
	}

	/**
	 * Returns the number of available spaces left in this ticket, bearing in mind event-wide restrictions, previous bookings, approvals and other tickets.
	 *
	 * @return int
	 */
	public function get_available_spaces() {
		$event_available_spaces  = $this->get_event()->get_bookings()->get_available_spaces();
		$ticket_available_spaces = $this->get_spaces() - $this->get_booked_spaces();
		if ( get_option( 'dbem_bookings_approval_reserved' ) ) {
			$ticket_available_spaces = $ticket_available_spaces - $this->get_pending_spaces();
		}
		$return = ( $ticket_available_spaces <= $event_available_spaces ) ? $ticket_available_spaces : $event_available_spaces;
		return apply_filters( 'em_ticket_get_available_spaces', $return, $this );
	}

	/**
	 * Get total number of pending spaces for this ticket.
	 *
	 * @param boolean $force_refresh force refresh.
	 * @return int
	 */
	public function get_pending_spaces( $force_refresh = false ) {
		global $wpdb;
		if ( ! array_key_exists( $this->event_id, $this->pending_spaces ) || $force_refresh ) {
			$sub_sql                                 = 'SELECT booking_id FROM ' . EM_BOOKINGS_TABLE . ' WHERE event_id=%d AND booking_status=0';
			$sql                                     = 'SELECT SUM(ticket_booking_spaces) FROM ' . EM_TICKETS_BOOKINGS_TABLE . " WHERE booking_id IN ($sub_sql) AND ticket_id=%d";
			$pending_spaces                          = $wpdb->get_var( $wpdb->prepare( $sql, $this->event_id, $this->ticket_id ) ); // phpcs:ignore
			$this->pending_spaces[ $this->event_id ] = $pending_spaces > 0 ? $pending_spaces : 0;
			$this->pending_spaces[ $this->event_id ] = apply_filters( 'em_ticket_get_pending_spaces', $this->pending_spaces[ $this->event_id ], $this, $force_refresh );
		}
		return $this->pending_spaces[ $this->event_id ];
	}

	/**
	 * Returns the number of booked spaces in this ticket.
	 *
	 * @param boolean $force_refresh force refresh.
	 * @return int
	 */
	public function get_booked_spaces( $force_refresh = false ) {
		global $wpdb;
		if ( ! array_key_exists( $this->event_id, $this->pending_spaces ) || $force_refresh ) {
			$status_cond                            = ! get_option( 'dbem_bookings_approval' ) ? 'booking_status IN (0,1)' : 'booking_status = 1';
			$sub_sql                                = 'SELECT booking_id FROM ' . EM_BOOKINGS_TABLE . " WHERE event_id=%d AND $status_cond";
			$sql                                    = 'SELECT SUM(ticket_booking_spaces) FROM ' . EM_TICKETS_BOOKINGS_TABLE . " WHERE booking_id IN ($sub_sql) AND ticket_id=%d";
			$booked_spaces                          = $wpdb->get_var( $wpdb->prepare( $sql, $this->event_id, $this->ticket_id ) ); // phpcs:ignore
			$this->booked_spaces[ $this->event_id ] = $booked_spaces > 0 ? $booked_spaces : 0;
			$this->booked_spaces[ $this->event_id ] = apply_filters( 'em_ticket_get_booked_spaces', $this->booked_spaces[ $this->event_id ], $this, $force_refresh );
		}
		return $this->booked_spaces[ $this->event_id ];
	}

	/**
	 * Returns the total number of bookings of all statuses for this ticket
	 *
	 * @param int     $status status.
	 * @param boolean $force_refresh force refresh.
	 * @return int
	 */
	public function get_bookings_count( $status = false, $force_refresh = false ) {
		global $wpdb;
		if ( ! array_key_exists( $this->event_id, $this->bookings_count ) || $force_refresh ) {
			$sql                                     = 'SELECT COUNT(*) FROM ' . EM_TICKETS_BOOKINGS_TABLE . ' WHERE booking_id IN (SELECT booking_id FROM ' . EM_BOOKINGS_TABLE . ' WHERE event_id=%d) AND ticket_id=%d';
			$bookings_count                          = $wpdb->get_var( $wpdb->prepare( $sql, $this->event_id, $this->ticket_id ) ); // phpcs:ignore
			$this->bookings_count[ $this->event_id ] = $bookings_count > 0 ? $bookings_count : 0;
			$this->bookings_count[ $this->event_id ] = apply_filters( 'em_ticket_get_bookings_count', $this->bookings_count[ $this->event_id ], $this, $force_refresh );
		}
		return $this->bookings_count[ $this->event_id ];
	}

	/**
	 * Smart event locator, saves a database read if possible.
	 *
	 * @return EM_Event
	 */
	public function get_event() {
		return em_get_event( $this->event_id );
	}

	/**
	 * Returns array of EM_Booking objects that have this ticket
	 *
	 * @return EM_Bookings
	 */
	public function get_bookings() {
		$bookings = array();
		foreach ( $this->get_event()->get_bookings()->bookings as $em_booking ) {
			foreach ( $em_booking->get_tickets_bookings()->tickets_bookings as $em_ticket_booking ) {
				if ( $em_ticket_booking->ticket_id === $this->ticket_id ) {
					$bookings[ $em_booking->booking_id ] = $em_booking;
				}
			}
		}
		$this->bookings = new EM_Bookings( $bookings );
		return $this->bookings;
	}

	/**
	 * I wonder what this does....
	 *
	 * @return boolean
	 */
	public function delete() {
		global $wpdb;
		$result = false;
		if ( $this->can_manage() ) {
			if ( count( $this->get_bookings()->bookings ) === 0 ) {
				$sql    = $wpdb->prepare( 'DELETE FROM ' . EM_TICKETS_TABLE . ' WHERE ticket_id=%d', $this->ticket_id ); // phpcs:ignore
				$result = $wpdb->query( $sql ); // phpcs:ignore
			} else {
				$this->feedback_message = __( 'You cannot delete a ticket that has a booking on it.', 'community-portal' );
				$this->add_error( $this->feedback_message );
				return false;
			}
		}
		return ( false !== $result );
	}

	/**
	 * Based on ticket minimums, whether required and if the event has more than one ticket this function will return the absolute minimum required spaces for a booking
	 */
	public function get_spaces_minimum() {
		$ticket_count = count( $this->get_event()->get_bookings()->get_tickets()->tickets );
		// count available tickets to make sure.
		$available_tickets = 0;
		foreach ( $this->get_event()->get_bookings()->get_tickets()->tickets as $em_ticket ) {
			if ( $em_ticket->is_available() ) {
				$available_tickets++;
			}
		}
		$min_spaces = 0;
		if ( $ticket_count > 1 ) {
			if ( $this->is_required() && $this->is_available() ) {
				$min_spaces = ( $this->ticket_min > 0 ) ? $this->ticket_min : 1;
			} elseif ( $this->is_available() && $this->ticket_min > 0 ) {
				$min_spaces = $this->ticket_min;
			} elseif ( $this->is_available() && 1 === $available_tickets ) {
				$min_spaces = 1;
			}
		} else {
			$min_spaces = $this->ticket_min > 0 ? $this->ticket_min : 1;
		}
		return $min_spaces;
	}

	/**
	 * I wonder what this does....
	 *
	 * @return boolean
	 */
	public function is_required() {
		if ( $this->ticket_required || count( $this->get_event()->get_tickets()->tickets ) === 1 ) {
			return true;
		}
		return false;
	}

	/**
	 * Get the html options for quantities to go within a <select> container
	 *
	 * @param bool  $zero_value zero value.
	 * @param mixed $default_value default value.
	 * @return string
	 */
	public function get_spaces_options( $zero_value = true, $default_value = 0 ) {
		$available_spaces = $this->get_available_spaces();
		if ( $this->is_available() ) {
			$min_spaces = $this->get_spaces_minimum();
			if ( $default_value > 0 ) {
				$default_value = $min_spaces > $default_value ? $min_spaces : $default_value;
			} else {
				$default_value = $this->is_required() ? $min_spaces : 0;
			}
			ob_start();
			?>
			<select name="em_tickets[<?php echo esc_attr( $this->ticket_id ); ?>][spaces]" class="em-ticket-select" id="em-ticket-spaces-<?php echo esc_attr( $this->ticket_id ); ?>">
				<?php
					$min = ( $this->ticket_min > 0 ) ? $this->ticket_min : 1;
					$max = ( $this->ticket_max > 0 ) ? $this->ticket_max : get_option( 'dbem_bookings_form_max' );
				if ( $this->get_event()->event_rsvp_spaces > 0 && $this->get_event()->event_rsvp_spaces < $max ) {
					$max = $this->get_event()->event_rsvp_spaces;
				}
				?>
				<?php
				if ( $zero_value && ! $this->is_required() ) :
					?>
					<option>0</option><?php endif; ?>
				<?php for ( $i = $min; $i <= $available_spaces && $i <= $max; $i++ ) : ?>
					<option
					<?php
					if ( $i === $default_value ) {
						echo 'selected="selected"';
						$shown_default = true; }
					?>
					><?php echo esc_html( $i ); ?></option>
				<?php endfor; ?>
				<?php
				if ( empty( $shown_default ) && $default_value > 0 ) :
					?>
					<option selected="selected"><?php echo esc_html( $default_value ); ?></option><?php endif; ?>
			</select>
			<?php
			return apply_filters( 'em_ticket_get_spaces_options', ob_get_clean(), $zero_value, $default_value, $this );
		} else {
			return false;
		}
	}

	/**
	 * Returns an EM_DateTime object of the ticket start date/time in local timezone of event.
	 * If no start date defined or if date is invalid, false is returned.
	 *
	 * @param bool $utc_timezone Returns em_date_time with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime|false
	 * @see EM_Event::get_datetime()
	 */
	public function start( $utc_timezone = false ) {
		return apply_filters( 'em_ticket_start', $this->get_datetime( 'start', $utc_timezone ), $this );
	}

	/**
	 * Returns an EM_DateTime object of the ticket end date/time in local timezone of event.
	 * If no start date defined or if date is invalid, false is returned.
	 *
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime|false
	 * @see EM_Event::get_datetime()
	 */
	public function end( $utc_timezone = false ) {
		return apply_filters( 'em_ticket_end', $this->get_datetime( 'end', $utc_timezone ), $this );
	}

	/**
	 * Generates an EM_DateTime for the the start/end date/times of the ticket in local timezone.
	 * If ticket has no start/end date, or an invalid format, false is returned.
	 *
	 * @param string $when 'start' or 'end' date/time.
	 * @param bool   $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default. Do not use if EM_DateTime->valid is false.
	 * @return EM_DateTime|false
	 */
	public function get_datetime( $when = 'start', $utc_timezone = false ) {
		if ( 'start' !== $when && 'end' !== $when ) {
			return new EM_DateTime(); // currently only start/end dates are relevant.
		}
		// Initialize EM_DateTime if not already initialized, or if previously initialized object is invalid (e.g. draft event with invalid dates being resubmitted).
		$when_date = 'ticket_' . $when;
		// we take a pass at creating a new datetime object if it's empty, invalid or a different time to the current start date.
		if ( ! empty( $this->$when_date ) ) {
			if ( empty( $this->$when ) || ! $this->$when->valid ) {
				$this->$when = new EM_DateTime( $this->$when_date, $this->get_event()->get_timezone() );
			}
		} else {
			$this->$when        = new EM_DateTime();
			$this->$when->valid = false;
		}
		// Set to UTC timezone if requested, local by default.
		$tz = $utc_timezone ? 'UTC' : $this->get_event()->get_timezone();
		$this->$when->setTimezone( $tz );
		return $this->$when;
	}

	/**
	 * Can the user manage this event?
	 *
	 * @param bool $owner_capability owner capability.
	 * @param bool $admin_capability admin capability.
	 * @param bool $user_to_check user to check.
	 */
	public function can_manage( $owner_capability = false, $admin_capability = false, $user_to_check = false ) {
		if ( '' === $this->ticket_id && ! is_user_logged_in() && get_option( 'dbem_events_anonymous_submissions' ) ) {
			$user_to_check = get_option( 'dbem_events_anonymous_user' );
		}
		return $this->get_event()->can_manage( 'manage_bookings', 'manage_others_bookings', $user_to_check );
	}

	/**
	 * Deprecated since 5.8.2, just access properties directly or use relevant functions such as $this->start() for ticket_start time - Outputs properties with formatting
	 *
	 * @param string $property property.
	 * @return string
	 */
	public function output_property( $property ) {
		switch ( $property ) {
			case 'start':
				$value = ( $this->start()->valid ) ? $this->start()->i18n( em_get_date_format() ) : '';
				break;
			case 'end':
				$value = ( $this->end()->valid ) ? $this->end()->i18n( em_get_date_format() ) : '';
				break;
			default:
				$value = $this->$property;
				break;
		}
		return apply_filters( 'em_ticket_output_property', $value, $this, $property );
	}
}
?>
