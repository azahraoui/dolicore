<?php

namespace Dolibarr\Dolibase\Core\Pages;

use Dolibase\Core\Class\FormPage;
use ExtraFields;

class ListPage extends FormPage
{
	protected array $arrayFields = [];

	protected string $contentPage = '';

	protected ExtraFields $extraFields;

	protected array $extraLabels = [];

	protected array $searchArrayOptions = [];


	/**
	 * Constructor
	 *
	 * @param     $page_title     HTML page title
	 * @param     $access_perm    Access permission
	 * @param     $contextpage    Page context
	 */
	public function __construct($page_title, $access_perm = '', $contextpage = '')
	{
		global $db;

		// Set page attributes
		$this->extraFields = new ExtraFields($db);
		$this->contentPage = (!empty($contextpage) ? $contextpage : get_rights_class(false, true) . 'list');

		parent::__construct($page_title, $access_perm);
	}

	/**
	 * Open list / print list head
	 *
	 * @param   $title               List title
	 * @param   $picture             List picture
	 * @param   $list_fields         List fields
	 * @param   $search_fields       List search fields
	 * @param   $nbofshownrecords    Number of shown records
	 * @param   $nbtotalofrecords    Total number of records
	 * @param   $fieldstosearchall   Fields to search all
	 * @param   $sortfield           Sort field
	 * @param   $sortorder           Sort order
	 * @param   $morehtmlright       More HTML to show on the right of the list title
	 * @return  $this
	 */
	public function openList($title, $picture = 'title_generic.png', $list_fields, $search_fields, $nbofshownrecords, $nbtotalofrecords, $fieldstosearchall = array(), $sortfield = '', $sortorder = '', $morehtmlright = ''): static
	{
		global $langs, $conf;

		// Get parameters
		$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : $this->contentPage;
		$sall = isset($search_fields['all']) ? $search_fields['all'] : GETPOST('sall', 'alphanohtml');
		$page = GETPOST('page', 'int') ? GETPOST('page', 'int') : 0;
		if (empty($sortorder)) $sortorder = GETPOST('sortorder', 'alpha');
		if (empty($sortfield)) $sortfield = GETPOST('sortfield', 'alpha');
		$limit = GETPOST('limit') ? GETPOST('limit', 'int') : $conf->liste_limit;
		$optioncss = GETPOST('optioncss', 'alpha');

		// List form
		echo '<form action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
		echo '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
		echo '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
		echo '<input type="hidden" name="action" value="list">';
		echo '<input type="hidden" name="sortfield" value="' . $sortfield . '">';
		echo '<input type="hidden" name="sortorder" value="' . $sortorder . '">';

		// Add list parameters
		$param = '';
		foreach ($search_fields as $key => $value) {
			if ($value != '') $param .= '&' . $key . '=' . urlencode($value);
		}
		if (!empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param .= '&contextpage=' . urlencode($contextpage);
		if ($limit > 0 && $limit != $conf->liste_limit) $param .= '&limit=' . urlencode($limit);
		if ($optioncss != '') $param .= '&optioncss=' . urlencode($optioncss);
		// Loop to complete $param for extrafields
		foreach ($this->searchArrayOptions as $key => $val) {
			$tmpkey = preg_replace('/search_options_/', '', $key);
			if ($val != '') $param .= '&search_options_' . $tmpkey . '=' . urlencode($val);
		}

		// List title
		$title = $langs->trans($title);
		print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $nbofshownrecords, $nbtotalofrecords, $picture, 0, $morehtmlright, '', $limit);

		if ($sall) {
			foreach ($fieldstosearchall as $key => $val) {
				$fieldstosearchall[$key] = $langs->trans($val);
			}
			echo $langs->trans('FilterOnInto', $sall) . join(', ', $fieldstosearchall);
		}

		echo '<div class="div-table-responsive">';
		echo '<table class="tagtable liste">' . "\n";

		// Generate $arrayfields
		$this->arrayFields = array();
		foreach ($list_fields as $field) {
			$checked = (isset($field['checked']) ? $field['checked'] : 1);
			$enabled = (isset($field['enabled']) ? verifCond($field['enabled']) : 1);
			$this->arrayFields[$field['name']] = array('label' => $field['label'], 'checked' => $checked, 'enabled' => $enabled);
		}
		// Extra fields
		if (is_array($this->extraFields->attribute_label) && count($this->extraFields->attribute_label)) {
			foreach ($this->extraFields->attribute_label as $key => $val) {
				$this->arrayFields['ef.' . $key] = array('label' => $this->extraFields->attribute_label[$key], 'checked' => (($this->extraFields->attribute_list[$key] < 0) ? 0 : 1), 'position' => $this->extraFields->attribute_pos[$key], 'enabled' => $this->extraFields->attribute_perms[$key]);
			}
		}
		// This change content of $arrayfields
		$varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
		$selectedfields = $this->form->multiSelectArrayWithCheckbox('selectedfields', $this->arrayFields, $varpage);
		$old_dolibarr = compare_version(DOL_VERSION, '<', '6.0.0');

		// List fields
		echo '<tr class="liste_titre">';
		foreach ($list_fields as $field) {
			if (!empty($this->arrayFields[$field['name']]['checked'])) {
				$field_align = (isset($field['align']) ? 'align="' . $field['align'] . '"' : '');
				$field_class = (isset($field['class']) ? $field['class'] . ' ' : '');
				$label = $old_dolibarr ? $langs->trans($field['label']) : $field['label'];
				print_liste_field_titre($label, $_SERVER["PHP_SELF"], $field['name'], '', $param, $field_align, $sortfield, $sortorder, $field_class);
			}
		}
		// Loop to show all columns of extrafields for the title line
		if (is_array($this->extraFields->attribute_label) && count($this->extraFields->attribute_label)) {
			foreach ($this->extraFields->attribute_label as $key => $val) {
				if (!empty($this->arrayFields["ef." . $key]['checked'])) {
					$align = $this->extraFields->getAlignFlag($key);
					$sortonfield = "ef." . $key;
					if (!empty($this->extraFields->attribute_computed[$key])) $sortonfield = '';
					$label = $old_dolibarr ? $langs->trans($this->extraLabels[$key]) : $this->extraLabels[$key];
					echo getTitleFieldOfList($label, 0, $_SERVER["PHP_SELF"], $sortonfield, "", $param, ($align ? 'align="' . $align . '"' : ''), $sortfield, $sortorder) . "\n";
				}
			}
		}
		print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"], '', '', '', 'align="right"', $sortfield, $sortorder, 'maxwidthsearch ');
		echo "</tr>\n";

		// List search fields
		if ($optioncss != 'print') {
			echo '<tr class="liste_titre liste_titre_filter">';
			foreach ($list_fields as $field) {
				if (!empty($this->arrayFields[$field['name']]['checked'])) {
					$field_align = (isset($field['align']) ? ' align="' . $field['align'] . '"' : '');
					$field_class = (isset($field['class']) ? ' ' . $field['class'] : '');
					$search_input = (isset($field['search_input']) ? $field['search_input'] : '');
					echo '<td class="liste_titre' . $field_class . '"' . $field_align . '>' . $search_input . '</td>';
				}
			}
			// Loop to show all columns of extrafields for the search title line
			if (is_array($this->extraFields->attribute_label) && count($this->extraFields->attribute_label)) {
				foreach ($this->extraFields->attribute_label as $key => $val) {
					if (!empty($this->arrayFields['ef.' . $key]['checked'])) {
						$align = $this->extraFields->getAlignFlag($key);
						$typeofextrafield = $this->extraFields->attribute_type[$key];
						echo '<td class="liste_titre' . ($align ? ' ' . $align : '') . '">';
						if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')) && empty($this->extraFields->attribute_computed[$key])) {
							$tmpkey = preg_replace('/search_options_/', '', $key);
							$searchclass = '';
							if (in_array($typeofextrafield, array('varchar', 'select'))) {
								$searchclass = 'searchstring';
							} else if (in_array($typeofextrafield, array('int', 'double'))) {
								$searchclass = 'searchnum';
							}

							if ($typeofextrafield == 'select') {
								echo $this->form->listInput('search_options_' . $tmpkey, $this->extraFields->attribute_param[$tmpkey]['options'], dol_escape_htmltag($this->searchArrayOptions['search_options_' . $tmpkey]), 1);
							} else {
								echo '<input class="flat' . ($searchclass ? ' ' . $searchclass : '') . '" size="4" type="text" name="search_options_' . $tmpkey . '" value="' . dol_escape_htmltag($this->searchArrayOptions['search_options_' . $tmpkey]) . '">';
							}
						} /*else if (in_array($typeofextrafield, array('datetime', 'timestamp')))
						{
							// TODO
							// Use showInputField in a particular manner to have input with a comparison operator, not input for a specific value date-hour-minutes
						}*/
						else {
							// for the type as 'checkbox', 'chkbxlst', 'sellist' we should use code instead of id (example: I declare a 'chkbxlst' to have a link with dictionnairy, I have to extend it with the 'code' instead of 'rowid')
							$morecss = '';
							if ($typeofextrafield == 'sellist') {
								$morecss = 'maxwidth200';
							}
							echo $this->extraFields->showInputField($key, $this->searchArrayOptions['search_options_' . $key], '', '', 'search_', $morecss);
						}
						echo '</td>';
					}
				}
			}
			// search buttons
			echo '<td class="liste_titre" align="right"><input type="image" class="liste_titre" name="button_search" src="' . img_picto($langs->trans('Search'), 'search.png', '', '', 1) . '" value="' . dol_escape_htmltag($langs->trans('Search')) . '" title="' . dol_escape_htmltag($langs->trans('Search')) . '">';
			echo '<input type="image" class="liste_titre" name="button_removefilter" src="' . img_picto($langs->trans('Search'), 'searchclear.png', '', '', 1) . '" value="' . dol_escape_htmltag($langs->trans('RemoveFilter')) . '" title="' . dol_escape_htmltag($langs->trans('RemoveFilter')) . '">';
			echo "</td></tr>\n";
		}

		return $this;
	}

	/**
	 * Add a table column
	 *
	 * @param   $content      string content
	 * @param   $attr         string attributes
	 * @param   $field_name   string name
	 * @return  $this
	 */
	public function addColumn(string $content, string $attr = '', string $field_name = ''): static
	{
		if (empty($field_name) || !empty($this->arrayFields[$field_name]['checked'])) {
			parent::addColumn($content, $attr);
		}

		return $this;
	}

	/**
	 * Add extrafields columns
	 *
	 * @param   $obj   object
	 * @return  $this
	 */
	public function addExtraFields($obj): static
	{
		global $db;

		if (is_array($this->extraFields->attribute_label) && count($this->extraFields->attribute_label)) {
			foreach ($this->extraFields->attribute_label as $key => $val) {
				if (!empty($this->arrayFields['ef.' . $key]['checked'])) {
					$align = $this->extraFields->getAlignFlag($key);
					echo '<td';
					if ($align) echo ' align="' . $align . '"';
					echo '>';
					$tmpkey = 'options_' . $key;
					if (in_array($this->extraFields->attribute_type[$key], array('date', 'datetime', 'timestamp'))) {
						$value = $db->jdate($obj->$tmpkey);
					} else {
						$value = $obj->$tmpkey;
					}
					echo $this->extraFields->showOutputField($key, $value, '');
					echo '</td>';
				}
			}
		}

		return $this;
	}

	/**
	 * Fetch extrafields
	 *
	 * @param   $elementtype   element type
	 * @param   $qb            query builder instance
	 * @return  $this
	 */
	public function fetchExtraFields($elementtype, &$qb): static
	{
		global $db;

		// fetch optionals attributes and labels
		$this->extraLabels = $this->extraFields->fetch_name_optionals_label($elementtype);
		$this->searchArrayOptions = $this->extraFields->getOptionalsFromPost($this->extraLabels, '', 'search_');

		// Add fields from extrafields
		$more_fields = '';
		foreach ($this->extraFields->attribute_label as $key => $val) {
			$more_fields .= ($this->extraFields->attribute_type[$key] != 'separate' ? ', ef.' . $key . ' as options_' . $key : '');
		}
		$qb->addSelect($more_fields);
		if (is_array($this->extraFields->attribute_label) && count($this->extraFields->attribute_label)) {
			$qb->join($elementtype . '_extrafields as ef', '(t.rowid = ef.fk_object)', 'left');
		}

		// Loop to complete the sql search criterias from extrafields
		$where = '';
		foreach ($this->searchArrayOptions as $key => $val) {
			$tmpkey = preg_replace('/search_options_/', '', $key);
			$type = $this->extraFields->attribute_type[$tmpkey];

			if (in_array($type, array('date', 'datetime')) && !empty($val)) {
				$where .= " AND date(ef." . $tmpkey . ") = date('" . $db->idate($val) . "')";
			} else {
				$crit = $val;
				$mode_search = 0;

				if (in_array($type, array('int', 'double', 'real'))) {
					$mode_search = 1; // Search on a numeric
				} else if (in_array($type, array('sellist', 'link', 'chkbxlst', 'checkbox')) && $crit != '0' && $crit != '-1') {
					$mode_search = 2; // Search on a foreign key int
				}

				if ($crit != '' && (!in_array($type, array('select', 'sellist')) || ($crit != '0' && $crit != '-1')) && (!in_array($type, array('link')) || $crit != '-1')) {
					$where .= natural_search('ef.' . $tmpkey, $crit, $mode_search);
				}
			}
		}
		$qb->where($where);

		return $this;
	}

	/**
	 * Close list
	 *
	 * @param   $buttons        buttons to add before close list
	 * @param   $hide_buttons   hide buttons by default
	 * @return  $this
	 */
	public function closeList($buttons = array(), $hide_buttons = false): static
	{
		echo "</table>\n";

		$optioncss = GETPOST('optioncss', 'alpha');

		if (!empty($buttons) && $optioncss != 'print') $this->addButtons($buttons, $hide_buttons);

		echo "</div></form>\n";

		return $this;
	}

	/**
	 * Add buttons to the list
	 *
	 * @param   $buttons        buttons to add
	 * @param   $hide_buttons   hide buttons by default
	 * @return  $this
	 */
	protected function addButtons($buttons, $hide_buttons): static
	{
		global $langs;

		echo '<div class="tabsAction' . ($hide_buttons ? ' hidden' : '') . '">';

		foreach ($buttons as $button) {
			if (!isset($button['enabled']) || verifCond($button['enabled'])) {
				echo '<input type="submit" class="butAction" name="' . $button['name'] . '" value="' . $langs->trans($button['label']) . '">';
			}
		}

		echo '</div>';

		return $this;
	}

	/**
	 * Close table row
	 *
	 * @return  $this
	 */
	public function closeRow(): static
	{
		echo '<td></td>';

		parent::closeRow();

		return $this;
	}

	/**
	 * Load default actions
	 *
	 */
	protected function loadDefaultActions(): void
	{
		global $hookmanager;

		// Initialize technical object to manage hooks of thirdparties.
		$hookmanager->initHooks(array($this->contentPage));

		// Selection of new fields
		global $db, $conf, $user, $contextpage;
		$contextpage = $this->contentPage;
		include DOL_DOCUMENT_ROOT . '/core/actions_changeselectedfields.inc.php';

		// Purge search criteria
		if (GETPOST('button_removefilter_x') || GETPOST('button_removefilter')) // Both test are required to be compatible with all browsers
		{
			$_POST = array();
			$_GET = array();
		}
	}
}
