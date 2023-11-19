<?php

namespace AZahraoui\DoliCore\Class;

class Field
{
    /**
     * @var string Field name
     */
    public $name;
    /**
     * @var string Field translation
     */
    public $trans;
    /**
     * @var string Field validation rules
     */
    public $validation_rules;

    /**
     * Constructor
     *
     * @param     $name                    Field name
	 * @param     $trans                   Field translation
	 * @param     $validation_rules        Field validation rules, possible values: 'required|numeric|string|validEmail|validTel|validUrl|validID|greaterThan()|lessThan()|minLength()|maxLength()'
	 */
	public function __construct($name, $trans, $validation_rules = '')
	{
		$this->name             = $name;
		$this->trans            = $trans;
		$this->validation_rules = $validation_rules;
	}
}
