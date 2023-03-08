<?php
/**
 * Class Ticket
 *
 * @package TEC\Tickets\Emails
 */

namespace TEC\Tickets\Emails\Email;

use TEC\Tickets\Commerce\Settings;
use TEC\Tickets\Emails\Email_Abstract;
use \TEC\Tickets\Emails\Email_Template;
use WP_Post;

/**
 * Class Ticket
 *
 * @since 5.5.9
 *
 * @package TEC\Tickets\Emails
 */
class Ticket extends \TEC\Tickets\Emails\Email_Abstract {

	/**
	 * Email ID.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public static $id = 'tec_tickets_emails_ticket';

	/**
	 * Email template.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public $template = 'ticket';

	/**
	 * Email recipient.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public $recipient = 'customer';

	/**
	 * Enabled option key.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public static $option_enabled = 'tec_tickets_emails_ticket_option_enabled';

	/**
	 * Subject option key.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public static $option_subject = 'tec_tickets_emails_ticket_option_subject';

	/**
	 * Subject option key for multiple tickets.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $option_subject_multiple = 'tec_tickets_emails_ticket_option_subject_multiple';

	/**
	 * Email heading option key.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public static $option_heading = 'tec_tickets_emails_ticket_option_heading';

	/**
	 * Email heading option key for multiple tickets.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $option_heading_multiple = 'tec_tickets_emails_ticket_option_heading_multiple';

	/**
	 * Email heading option key.
	 *
	 * @since 5.5.9
	 *
	 * @var string
	 */
	public static $option_add_content = 'tec_tickets_emails_ticket_option_add_content';

	/**
	 * Checks if this email is enabled.
	 *
	 * @since 5.5.9
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return tribe_is_truthy( tribe_get_option( static::$option_enabled, true ) );
	}

	/**
	 * Checks if this email is sent to customer.
	 *
	 * @since 5.5.9
	 *
	 * @return bool
	 */
	public function is_customer_email(): bool {
		return true;
	}

	/**
	 * Get email title.
	 *
	 * @since 5.5.9
	 *
	 * @return string The email title.
	 */
	public function get_title(): string {
		return esc_html__( 'Ticket Email', 'event-tickets' );
	}

	/**
	 * Get email heading.
	 *
	 * @since 5.5.9
	 *
	 * @return string The email heading.
	 */
	public function get_heading(): string {
		$heading = tribe_get_option( static::$option_heading, $this->get_default_heading() );

		/**
		 * Allow filtering the email heading.
		 *
		 * @since 5.5.9
		 *
		 * @param string $heading  The email heading.
		 * @param string $id       The email id.
		 */
		$heading = apply_filters( 'tec_tickets_emails_heading_' . self::$id, $heading, self::$id, $this->template );

		return $this->format_string( $heading );
	}

	/**
	 * Get default email heading.
	 *
	 * @since 5.5.9
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return sprintf(
			// Translators: %s Lowercase singular of ticket.
			esc_html__( 'Here\'s your %s, {attendee-name}!', 'event-tickets' ),
			tribe_get_ticket_label_singular_lowercase()
		);
	}

	/**
	 * Get default email heading for multiple tickets.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_default_heading_multiple() {
		return sprintf(
			// Translators: %s Lowercase plural of tickets.
			esc_html__( 'Here are your %s, {attendee-name}!', 'event-tickets' ),
			tribe_get_ticket_label_plural_lowercase()
		);
	}

	/**
	 * Get email subject.
	 *
	 * @since 5.5.9
	 *
	 * @return string The email subject.
	 */
	public function get_subject(): string {
		$subject = tribe_get_option( static::$option_subject, $this->get_default_subject() );

		$subject = $this->format_string( $subject );

		// @todo: Probably we want more data parsed, or maybe move the filters somewhere else as we're always gonna
		// apply filters on the subject maybe move the filter to the parent::get_subject() ?

		/**
		 * Allow filtering the email subject.
		 *
		 * @since 5.5.9
		 *
		 * @param string $subject  The email subject.
		 * @param string $id       The email id.
		 */
		$subject = apply_filters( 'tec_tickets_emails_subject_' . self::$id, $subject, self::$id, $this->template );

		return $subject;
	}

	/**
	 * Get default email subject.
	 *
	 * @since 5.5.9
	 *
	 * @return string
	 */
	public function get_default_subject() {
		$default_subject = sprintf(
			// Translators: %s - Lowercase singular of tickets.
			esc_html__( 'Your %s from {site-title}', 'event-tickets' ),
			tribe_get_ticket_label_singular_lowercase()
		);

		// If they already had a subject set in Tickets Commerce, let's make it the default.
		return tribe_get_option( Settings::$option_confirmation_email_subject, $default_subject );
	}

	/**
	 * Get default email subject for multiple tickets.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_default_subject_multiple() {
		return sprintf(
			// Translators: %s - Lowercase plural of tickets.
			esc_html__( 'Your %s from {site-title}', 'event-tickets' ),
			tribe_get_ticket_label_plural_lowercase()
		);
	}

	/**
	 * Get email content.
	 *
	 * @since 5.5.9
	 *
	 * @param array $args The arguments.
	 *
	 * @return string The email content.
	 */
	public function get_content( $args = [] ): string {
		// @todo: Parse args, etc.
		$context = ! empty( $args['context'] ) ? $args['context'] : [];

		// @todo: We need to grab the proper information that's going to be sent as context.

		$email_template = tribe( Email_Template::class );
		$email_template->set_preview( true );

		// @todo @juanfra @codingmusician: we may want to inverse these parameters.
		return $email_template->get_html( $context, $this->template );
	}

	/**
	 * Get email settings.
	 *
	 * @since 5.5.9
	 *
	 * @return array
	 */
	public function get_settings(): array {

		$settings = [
			[
				'type' => 'html',
				'html' => '<div class="tribe-settings-form-wrap">',
			],
			[
				'type' => 'html',
				'html' => '<h2>' . esc_html__( 'Ticket Email Settings', 'event-tickets' ) . '</h2>',
			],
			[
				'type' => 'html',
				'html' => '<p>' . esc_html__( 'Ticket purchasers will receive an email including their ticket and additional info upon completion of purchase. Customize the content of this specific email using the tools below. The brackets {event_name}, {event_date}, and {ticket_name} can be used to pull dynamic content from the ticket into your email. Learn more about customizing email templates in our Knowledgebase.' ) . '</p>',
			],
			static::$option_enabled => [
				'type'                => 'toggle',
				'label'               => esc_html__( 'Enabled', 'event-tickets' ),
				'default'             => true,
				'validation_type'     => 'boolean',
			],
			static::$option_subject => [
				'type'                => 'text',
				'label'               => esc_html__( 'Subject', 'event-tickets' ),
				'default'             => $this->get_default_subject(),
				'placeholder'         => $this->get_default_subject(),
				'size'                => 'large',
				'validation_callback' => 'is_string',
			],
			static::$option_subject_multiple => [
				'type'                => 'text',
				'label'               => esc_html__( 'Subject (multiple)', 'event-tickets' ),
				'default'             => $this->get_default_subject_multiple(),
				'placeholder'         => $this->get_default_subject_multiple(),
				'size'                => 'large',
				'validation_callback' => 'is_string',
			],
			static::$option_heading => [
				'type'                => 'text',
				'label'               => esc_html__( 'Heading', 'event-tickets' ),
				'default'             => $this->get_default_heading(),
				'placeholder'         => $this->get_default_heading(),
				'size'                => 'large',
				'validation_callback' => 'is_string',
			],
			static::$option_heading_multiple => [
				'type'                => 'text',
				'label'               => esc_html__( 'Heading (multiple)', 'event-tickets' ),
				'default'             => $this->get_default_heading_multiple(),
				'placeholder'         => $this->get_default_heading_multiple(),
				'size'                => 'large',
				'validation_callback' => 'is_string',
			],
			static::$option_add_content => [
				'type'                => 'wysiwyg',
				'label'               => esc_html__( 'Additional content', 'event-tickets' ),
				'default'             => '',
				'tooltip'             => esc_html__( 'Additional content will be displayed below the tickets in your email.', 'event-tickets' ),
				'validation_type'     => 'html',
				'settings'        => [
					'media_buttons' => false,
					'quicktags'     => false,
					'editor_height' => 200,
					'buttons'       => [
						'bold',
						'italic',
						'underline',
						'strikethrough',
						'alignleft',
						'aligncenter',
						'alignright',
					],
				],
			],
		];

		/**
		 * Allow filtering the settings for this email.
		 *
		 * @since TBD
		 *
		 * @param array          $settings  The settings array.
		 * @param Email_Abstract $this      Email object.
		 */
		return apply_filters( 'tec_tickets_emails_settings_' . self::$id, $settings, $this );
	}

	/**
	 * Get the `post_type` data for this email.
	 *
	 * @since 5.5.9
	 *
	 * @return array
	 */
	public function get_post_type_data(): array {
		$data = [
			'slug'      => self::$id,
			'title'     => $this->get_title(),
			'template'  => $this->template,
			'recipient' => $this->recipient,
		];

		return $data;
	}
}
