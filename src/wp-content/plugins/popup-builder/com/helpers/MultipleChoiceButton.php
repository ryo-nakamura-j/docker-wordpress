<?php
namespace sgpb;

class MultipleChoiceButton
{
	private $buttonsData = array();
	private $savedValue = '';
	private $template = array();
	private $buttonPosition = 'right';
	private $fields = array();

	/**
	 * RadioButtons constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param $buttonsData
	 * @param $savedValue
	 */
	public function __construct($buttonsData, $savedValue)
	{
		$this->setButtonsData($buttonsData);
		$this->setSavedValue($savedValue);
		$this->prepareBuild();
	}

	public function __toString()
	{
		return $this->render();
	}

	public function setButtonsData($buttonsData)
	{
		$this->buttonsData = $buttonsData;
	}

	public function getButtonsData()
	{
		return $this->buttonsData;
	}

	/**
	 * Radio buttons saved value
	 *
	 * @since 1.0.0
	 *
	 * @param string $savedValue
	 */
	public function setSavedValue($savedValue)
	{
		$this->savedValue = $savedValue;
	}

	public function getSavedValue()
	{
		return $this->savedValue;
	}

	/**
	 * Radio buttons template
	 *
	 * @since 1.0.0
	 *
	 * @param array $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Radio buttons position
	 *
	 * @since 1.0.0
	 *
	 * @param string $buttonPosition
	 */
	public function setButtonPosition($buttonPosition)
	{
		$this->buttonPosition = $buttonPosition;
	}

	public function getButtonPosition()
	{
		return $this->buttonPosition;
	}

	/**
	 * Fields Data
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields
	 */
	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	private function prepareBuild()
	{
		$buttonsData = $this->getButtonsData();

		if (!empty($buttonsData['template'])) {
			$this->setTemplate($buttonsData['template']);
		}
		if (!empty($buttonsData['buttonPosition'])) {
			$this->setButtonPosition($buttonsData['buttonPosition']);
		}
		if (!empty($buttonsData['fields'])) {
			$this->setFields($buttonsData['fields']);
		}
	}

	public function render()
	{
		$allowed_html = AdminHelper::allowed_html_tags();
		ob_start();
		?>

		<?php echo wp_kses($this->renderFields(), $allowed_html);?>

		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	private function renderFields()
	{
		$fields = $this->getFields();
		$groupAttrStr = '';
		$template = $this->getTemplate();
		$buttonPosition = $this->getButtonPosition();
		$buttonsView = '';

		if (empty($fields)) {
			return $buttonsView;
		}

		if (!empty($template['groupWrapperAttr'])) {
			$groupAttrStr = $this->createAttrs($template['groupWrapperAttr']);
		}
		foreach ($fields as $field) {
			$labelView = $this->createNewBrandSelectTitle($field);
			$radioButton = $this->createNewBrandLabel($field);

			$buttonsView .= "<div $groupAttrStr>";
			$buttonsView .= $labelView.$radioButton;
			$buttonsView .= '</div>';
		}
		return '<div class="subForm dimensionForm">'.$buttonsView.'</div>';
	}

	private function createNewBrandLabel($field)
	{
		$template = $this->getTemplate();
		$savedValue = $this->getSavedValue();
		$parentAttrsStr = '';
		$classList = '';
		$label =  '';
		$inputName = '';
		$checked = '';
		$value = '';
		$inputDataAttr = '';

		if (empty($field['label'])) {
			return $label;
		}
		if (!empty($field['attr']['value'])) {
			$value = $field['attr']['value'];
		}
		if (!empty($field['attr']['class'])) {
			$classList = $field['attr']['class'];
		}

		$labelData = $field['label'];
		if (!empty($template['labelAttr'])) {
			$parentAttrsStr = $this->createAttrs($template['labelAttr']);
		}

		if (!empty($field['attr']['name'])) {
			$inputName = $field['attr']['name'];
		}
		if (!empty($field['attr']['data-attr-href'])) {
			$inputDataAttr = $field['attr']['data-attr-href'];
		}

		if (is_array($savedValue) && in_array($value, $savedValue)) {
			$checked = 'checked';
		}
		else if (!is_array($savedValue) && $savedValue == $value) {
			$checked = 'checked';
		}

		$info = '';
		if (isset($field['label']['info'])) {
			$info = '<div class="question-mark">B</div>';
			$info .= '<div class="sgpb-info-wrapper">';
			$info .= '<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">';
			$info .= $field['label']['info'];
			$info .= '</span>';
			$info .= '</div>';
		}

		$label .= '<label class="control control__radio sgpb-choice-option-wrapper">';
		$label .= '<input value="'.$value.'" class="sgpb-radio-input '.$classList.'" type="radio" name="'.$inputName.'" '.$checked.' data-attr-href="'.$inputDataAttr.'">';
		$label .= '<div class="control__indicator"></div>';
		$label .= '</label>';
		$label .= $info;

		return $label;
	}

	private function createNewBrandSelectTitle($field)
	{
		$template = $this->getTemplate();
		$parentAttrsStr = '';
		$title =  '';
		$labelName = '';
		if (empty($field['label'])) {
			return $title;
		}

		$labelData = $field['label'];
		if (!empty($template['labelAttr'])) {
			$parentAttrsStr = $this->createAttrs($template['labelAttr']);
		}

		if (!empty($labelData['name'])) {
			$labelName = $labelData['name'];
		}

		$title = "<span $parentAttrsStr>";
		$title .= $labelName;
		$title .=  '</span>';

		return $title;
	}

	private function createRadioButton($field)
	{
		$template = $this->getTemplate();
		$savedValue = $this->getSavedValue();
		$parentAttrsStr = '';
		$inputAttrStr = '';
		$value = '';
		$checked = '';

		if (!empty($template['fieldWrapperAttr'])) {
			$parentAttrsStr = $this->createAttrs($template['fieldWrapperAttr']);
		}

		if (!empty($field['attr'])) {

			if (!empty($field['attr']['value'])) {
				$value = $field['attr']['value'];
			}

 			$inputAttrStr = $this->createAttrs($field['attr']);
		}

		if (is_array($savedValue) && in_array($value, $savedValue)) {
			$checked = 'checked';
		}
		else if (!is_array($savedValue) && $savedValue == $value) {
			$checked = 'checked';
		}

		$info = '';
		if (isset($field['label']['info'])) {
			$info = '<div class="question-mark">B</div>';
			$info = '<div class="sgpb-info-wrapper">';
			$info = '<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">';
			$info .= $field['label']['info'];
			$info = '</span>';
			$info .= '</div>';
		}

		$label = "<div $parentAttrsStr>";
		$label .= "<input $inputAttrStr $checked >";
		$label .= $info;
		$label .=  '</div>';

		return $label;
	}

	private function createLabel($field)
	{
		$template = $this->getTemplate();
		$parentAttrsStr = '';
		$label =  '';
		$labelName = '';
		$checkBoxFor = '';

		if (!empty($field['attr']['id'])) {
			$checkBoxFor = $field['attr']['id'];
		}
		if (empty($field['label'])) {
			return $label;
		}

		$labelData = $field['label'];
		if (!empty($template['labelAttr'])) {
			$parentAttrsStr = $this->createAttrs($template['labelAttr']);
		}

		if (!empty($labelData['name'])) {
			$labelName = $labelData['name'];
		}

		$label = "<div $parentAttrsStr>";
		$label .= "<label for=\"$checkBoxFor\">$labelName</label>";
		$label .=  '</div>';

		return $label;
	}

	/**
	 * Create html attrs
	 *
	 * @since 1.0.0
	 *
	 * @param array $attrs
	 *
	 * @return string $attrStr
	 */
	private function createAttrs($attrs)
	{
		$attrStr = '';

		if (empty($attrs)) {
			return $attrStr;
		}

		foreach ($attrs as $attrKey => $attrValue) {
			$attrStr .= $attrKey.'="'.$attrValue.'" ';
		}

		return $attrStr;
 	}
}
