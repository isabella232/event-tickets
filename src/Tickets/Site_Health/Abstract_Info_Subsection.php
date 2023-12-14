<?php
/**
 * Abstract class that handles adding fields to Site Health.
 *
 * This class serves as a base for specific subsections within the Site Health
 * info page, allowing for organized and modular representation of different data sets.
 *
 * @since   TBD
 * @package TEC\Tickets\Site_Health
 */

namespace TEC\Tickets\Site_Health;

/**
 * Abstract class for creating subsections in the Site Health information section.
 *
 * It provides a standard way to add and manage fields specific to different subsections
 * in the Site Health info panel.
 *
 * @since   TBD
 */
abstract class Abstract_Info_Subsection {

	/**
	 * An array to hold the fields for the subsection.
	 *
	 * @var array
	 */
	private array $fields = [];

	/**
	 * Determines if the subsection is enabled and should be displayed.
	 *
	 * The method should be overridden in the subclass to provide specific logic
	 * determining whether the subsection should be active.
	 *
	 * @return bool True if the subsection is enabled, false otherwise.
	 */
	abstract protected function is_subsection_enabled(): bool;

	/**
	 * Retrieves the fields for the subsection.
	 *
	 * This method checks if the subsection is enabled and returns the generated fields.
	 *
	 * @return array An array of fields for the subsection.
	 */
	public function get_subsection(): array {
		if ( ! $this->is_subsection_enabled() ) {
			return [];
		}

		$this->fields = $this->generate_subsection();
		return $this->fields;
	}

	/**
	 * Generates the fields for the subsection.
	 *
	 * This method should be implemented in the subclass to define the specific fields
	 * for the subsection.
	 */
	abstract protected function generate_subsection();
}