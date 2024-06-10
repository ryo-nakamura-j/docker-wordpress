<?php
namespace sgpb;
class ConditionCreator
{
	public function __construct($targetData)
	{
		if (!empty($targetData)) {
			$this->setConditionsObj($targetData);
		}
	}

	private $conditionsObj;
	//When there is not group set groupId -1
	private $prevGroupId = -1;

	public function setPrevGroupId($prevGroupId)
	{
		$this->prevGroupId = $prevGroupId;
	}

	public function getPrevGroupId()
	{
		return $this->prevGroupId;
	}

	public function setConditionsObj($conditionsObj)
	{
		$this->conditionsObj = $conditionsObj;
	}

	public function getConditionsObj()
	{
		return $this->conditionsObj;
	}

	public function render()
	{
		$conditionsObj = $this->getConditionsObj();
		$view = '';

		if (empty($conditionsObj)) {
			return array();
		}

		foreach ($conditionsObj as $conditionObj) {

			$currentGroupId = $conditionObj->getGroupId();

			$prevGroupId =  $this->getPrevGroupId();
			$openGroupDiv = '';
			$separator = '';
			$closePrevGroupDiv = '';

			if ($currentGroupId > $prevGroupId) {
				if ($currentGroupId != 0) {
					$closePrevGroupDiv = '</div>';
					$separator = ConditionCreator::getOrRuleSeparator();
				}
				$openGroupDiv = '<div class="sgpb-wrapper sgpb-box-'.$conditionObj->getConditionName().' sg-target-group sg-target-group-'.$conditionObj->getGroupId().'" data-group-id="'.$currentGroupId.'">';
			}

			$view .= $closePrevGroupDiv;
			$view .= $separator;
			$view .= $openGroupDiv;
			$view .= ConditionCreator::createConditionRuleRow($conditionObj);

			$this->setPrevGroupId($currentGroupId);
		}

		$view .= '</div>';

		return $view;
	}

	public static function getOrRuleSeparator()
	{
		return '<h4 class="sgpb-rules-or"><span>'.__('OR', SG_POPUP_TEXT_DOMAIN).'</span></h4>';
	}

	public static function createConditionRuleRow($conditionDataObj)
	{
		ob_start();
		?>
		<div class="events form sg-target-rule sgpb-margin-bottom-40 sg-target-rule-<?php echo esc_attr($conditionDataObj->getRuleId()); ?> sgpb-event-row" data-rule-id="<?php echo esc_attr($conditionDataObj->getRuleId()); ?>">
			<div class="formItem sgpb-align-item-start">
				<?php
				$savedData = $conditionDataObj->getSavedData();

				if (!isset($savedData['value'])) {
					$savedData['value'] = '';
				}
				?>
				<?php $idHiddenDiv = $conditionDataObj->getConditionName().'_'.$conditionDataObj->getGroupId().'_'.$conditionDataObj->getRuleId();?>
				<?php foreach ($savedData as $conditionName => $conditionSavedData): ?>
					<?php
					$showRowStatusClass = '';
					$hideStatus = self::getParamRowHideStatus($conditionDataObj, $conditionName);
					$ruleElementData = self::getRuleElementData($conditionDataObj, 'param');
					$ruleSavedData = $ruleElementData['saved'];
					$currentArgs = array('savedData' => $ruleSavedData, 'conditionName' => $conditionName);

					if (!self::allowToShowOperatorColumn($conditionDataObj, $currentArgs)) {
						$hideStatus = true;
					}
					$showRowStatusClass = ($hideStatus) ? 'sgpb-hide-condition-row' : $showRowStatusClass;
					?>
					<?php if ($conditionName != 'hiddenOption'): ?>
						<div data-condition-name="<?php echo esc_attr($conditionName);?>" class="<?php echo esc_attr('inputBlock sg-condition-'.$conditionName.'-wrapper'.' '.$showRowStatusClass); ?>">
							<?php
							if (!$hideStatus) {
								echo wp_kses(self::createConditionElement($conditionDataObj, $conditionName), AdminHelper::allowed_html_tags());
							}
							?>
						</div>
					<?php endif; ?>
					<?php if (($conditionName == 'hiddenOption')): ?>
						<?php $hiddenContent = self::getHiddenDataContent($conditionDataObj); ?>
							<div class="sgpb-hide-condition-row"><div id="<?php echo esc_attr($idHiddenDiv);?>"><?php echo wp_kses($hiddenContent, AdminHelper::allowed_html_tags()); ?></div></div>
					<?php endif; ?>
				<?php endforeach;?>
				<?php echo wp_kses(self::createConditionOperators($conditionDataObj, $idHiddenDiv), AdminHelper::allowed_html_tags()); ?>
			</div>
		</div>
		<?php
		$targetOptionRow = ob_get_contents();
		ob_end_clean();

		return $targetOptionRow;
	}

	private static function allowToShowOperatorColumn($conditionDataObj, $currentArgs = array())
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$conditionName = $conditionDataObj->getConditionName();
		$conditionData = $SGPB_DATA_CONFIG_ARRAY[$conditionName];
		$operatorAllowInConditions = array();

		if (!empty($conditionData['operatorAllowInConditions'])) {
			$operatorAllowInConditions  = $conditionData['operatorAllowInConditions'];
		}

		$savedData = $conditionDataObj->getSavedData();

		$status = true;

		if ($currentArgs['conditionName'] == 'operator') {
			$currentSavedData = $currentArgs['savedData'];

			if (($currentSavedData == 'not_rule' || $currentSavedData == 'select_role' || $currentSavedData == 'select_event')) {
				$status = false;
			}

			// unset old customOperator
			$SGPB_DATA_CONFIG_ARRAY[$conditionName]['paramsData']['customOperator'] = '';
			if (is_array($operatorAllowInConditions)) {

				if (in_array($savedData['param'], $operatorAllowInConditions)) {
					$operator = '';
					if (!empty($conditionData['paramsData'][$savedData['param'].'Operator'])) {
						$operator = $conditionData['paramsData'][$savedData['param'].'Operator'];
					}
					$SGPB_DATA_CONFIG_ARRAY[$conditionName]['paramsData']['customOperator'] = $operator;
					return true;
				}
				// there is no need to show is/is not column for not specific conditions (everywhere, all posts/pages/other_custom_post_types)
				if (isset($savedData['param']) && !is_array($savedData['param']) && (strpos($savedData['param'], '_all') || $savedData['param'] == 'everywhere' || $savedData['param'] == 'post_tags')) {
					return false;
				}
				if (!empty($savedData['tempParam']) && in_array($savedData['tempParam'], $operatorAllowInConditions)) {
					$SGPB_DATA_CONFIG_ARRAY[$conditionName]['paramsData']['operator'] = $conditionData['paramsData'][$savedData['tempParam'].'Operator'];
				}
			}

			if (empty($SGPB_DATA_CONFIG_ARRAY[$conditionName]['paramsData']['operator'])) {
				$status = false;
			}
		}

		return $status;
	}

	public static function createConditionOperators($conditionDataObj, $idHiddenDiv = '')
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$groupId = $conditionDataObj->getRuleId();
		$groupTotal = $conditionDataObj->getGroupTotal();

		$conditionName = $conditionDataObj->getConditionName();
		$operatorsHtml = '';
		$conditionData = $SGPB_DATA_CONFIG_ARRAY[$conditionName];
		$eventsData = $SGPB_DATA_CONFIG_ARRAY['events'];

		$operatorsData = $conditionData['operators'];
		$eventButtonClasses = '';
		$eventButtonWrapperClass = '';
		$icon = '';

		if (empty($operatorsData)) {
			return $operatorsHtml;
		}

		foreach ($operatorsData as $operator) {
			$identificatorClass = '';
			$style = '';
			if (!isset($eventsData['hiddenOptionData'])) {
				continue;
			}
			$saveData = $conditionDataObj->getSavedData();
			if (empty($saveData['hiddenOption']) && $operator['name'] == 'Edit' && $saveData["param"] != 'load') {
				continue;
			}
			if ($operator['name'] == 'Edit') {
				$operator['name'] = 'Settings';
			}

			if ($operator['operator'] == 'edit') {
				$icon = 'D';
				$btnClass = ' icons_gray';
				$identificatorClass = $idHiddenDiv;
				$eventButtonClasses = 'sgpb-rules-'.$operator['operator'].'-rule ';
			}
			if ($operator['operator'] == 'add') {
				$icon = 'L';
				$btnClass = ' icons_blue';
				$eventButtonClasses = 'sgpb-rules-'.$operator['operator'].'-rule ';
				//Don't show add button if it's not for last element
				if ($groupId < $groupTotal) {
					$style = 'style="display: none;"';
				}
			}
			if ($operator['operator'] == 'delete') {
				$icon = 'I';
				$btnClass = ' icons_pink';
				$eventButtonClasses = 'sgpb-rules-'.$operator['operator'].'-rule ';
			}

			$element = '<i class="sgpb-icons '.$btnClass.'" data-id="'.$identificatorClass.'">'.$icon.'</i>';

			$operatorsHtml .= '<div class="'.$eventButtonClasses.' sgpb-rules-'.$operator['operator'].'-button-wrapper" '.$style.'>';
			$operatorsHtml .= $element;
			$operatorsHtml .= '</div>';
		}

		return $operatorsHtml;
	}

	public static function createConditionElement($conditionDataObj, $ruleName)
	{
		//more code added because of the lack of abstraction
		//todo: remove ASAP if possible
		$sData = $conditionDataObj->getSavedData();
		if ($ruleName == 'param' && !empty($sData['tempParam'])) {
			$sData['param'] = $sData['tempParam'];
			$newObj = clone $conditionDataObj;
			$newObj->setSavedData($sData);
			$conditionDataObj = $newObj;
		}

		$element = '';

		$ruleElementData = self::getRuleElementData($conditionDataObj, $ruleName);
		$elementHeader = self::createRuleHeader($ruleElementData);
		$field = self::createRuleField($ruleElementData);
		$element .= $elementHeader;
		$element .= $field;

		return $element;
	}

	public static function createConditionField($conditionDataObj, $ruleName)
	{
		$ruleElementData = self::getRuleElementData($conditionDataObj, $ruleName);

		return self::createRuleField($ruleElementData);
	}

	public static function createConditionFieldHeader($conditionDataObj, $ruleName)
	{
		$ruleElementData = self::getRuleElementData($conditionDataObj, $ruleName);

		return self::createRuleHeader($ruleElementData);
	}

	public static function optionLabelSupplement($conditionDataObj, $ruleName)
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$conditionName = $conditionDataObj->getConditionName();
		$conditionConfig = $SGPB_DATA_CONFIG_ARRAY[$conditionName];
		$attrs = $conditionConfig['attrs'][$ruleName];

		if (isset($attrs['infoAttrs']['rightLabel'])) {
			$labelData = $attrs['infoAttrs']['rightLabel'];
			$value = $labelData['value'];
			$classes = $labelData['classes'];
			return '<span class="'.esc_attr($classes).'">'.$value.'</span>';
		}

		return '';
	}

	private static function getRuleElementData($conditionDataObj, $ruleName = '')
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$ruleElementData = array();
		$savedParam = '';
		$conditionName = $conditionDataObj->getConditionName();
		$saveData = $conditionDataObj->getSavedData();
		$conditionConfig = $SGPB_DATA_CONFIG_ARRAY[$conditionName];
		$rulesType = $conditionConfig['columnTypes'];
		$paramsData = $conditionConfig['paramsData'];

		$attrs = $conditionConfig['attrs'];

		if (!empty($saveData[$ruleName])) {
			$savedParam =  $saveData[$ruleName];
		}
		else if (!empty($saveData['hiddenOption']) && isset($saveData['hiddenOption'][$ruleName])) {
			$savedParam = $saveData['hiddenOption'][$ruleName];
		}

		$ruleElementData['ruleName'] = $ruleName;
		if ($ruleName == 'value' && !empty($saveData[$conditionDataObj->getTakeValueFrom()])) {
			$index = $conditionDataObj->getTakeValueFrom();
			$ruleName = $saveData[$index];
		}

		$type = array();
		if (!empty($rulesType[$ruleName])) {
			$type = $rulesType[$ruleName];
		}
		$data = array();
		if (!empty($paramsData[$ruleName])) {
			$data = $paramsData[$ruleName];
		}

		// if exists customOperator it takes the custom one
		if ($ruleName == 'operator' && !empty($paramsData['customOperator'])) {
			$data = $paramsData['customOperator'];
		}

		$optionAttr = array();
		if (!empty($attrs[$ruleName])) {
			$optionAttr = $attrs[$ruleName];
		}

		$attr = array();

		if (!empty($optionAttr['htmlAttrs'])) {
			$attr = $optionAttr['htmlAttrs'];
		}

		$ruleElementData['type'] = $type;
		$ruleElementData['data'] = apply_filters('sgpb'.$ruleName.'ConditionCreator', $data, $saveData);
		$ruleElementData['saved'] = $savedParam;
		$ruleElementData['attr'] = $attr;
		$ruleElementData['conditionDataObj'] = $conditionDataObj;

		return $ruleElementData;
	}

	private static function createRuleHeader($ruleElementData)
	{
		return self::createElementHeader($ruleElementData);
	}

	private static function isAssociativeArrayOrEmptyString($args)
	{
		if (gettype($args) === 'string') return true;
	    if (array() === $args) return false;
	    return array_keys($args) !== range(0, count($args) - 1);
	}

	public static function createRuleField($ruleElementData)
	{
		$attr = array();
		$type = $ruleElementData['type'];
		$conditionObj = $ruleElementData['conditionDataObj'];

		$name = 'sgpb-'.$conditionObj->getConditionName().'['.$conditionObj->getGroupId().']['.$conditionObj->getRuleId().']['.$ruleElementData['ruleName'].']';
		$attr['name'] = $name;

		if (is_array($ruleElementData['attr'])) {
			$attr += $ruleElementData['attr'];
			$attr['data-rule-id'] = $conditionObj->getRuleId();
		}
		$rowField = '';

		switch($type) {

			case 'select':
				if (!empty($attr['multiple'])) {
					$attr['name'] .= '[]';
				}
				$savedData = $ruleElementData['saved'];

				if (empty($ruleElementData['data'])) {
					// this check is for current version update!
					// the old value was a simple array!
					// after update we need to convert them all to a associative array
					// this check will resolve UI issues and also prevent bugs after update-ing the existing popup
					// this change is for post_category and post_tags!
					if (self::isAssociativeArrayOrEmptyString($ruleElementData['saved'])){
						$ruleElementData['data'] = $ruleElementData['saved'];
						$savedData = array();

						if (!empty($ruleElementData['saved'])) {
							$savedData = array_keys($ruleElementData['saved']);
						}
					} else {
						$ruleElementData['data'] = $ruleElementData['saved'];

						if (!empty($ruleElementData['saved'])) {
							if (isset($attr['isPostCategory'])){
								$ruleElementData['data'] = \ConfigDataHelper::getTermsByIds($ruleElementData['saved']);
							} elseif(isset($attr['isPostTag'])) {
								$ruleElementData['data'] = \ConfigDataHelper::getTagsByIds($ruleElementData['saved']);
							}
							$savedData = $ruleElementData['saved'];
						}
					}
				}

				$rowField .= AdminHelper::createSelectBox($ruleElementData['data'], $savedData, $attr);
				break;
			case 'text':
			case 'url':
			case 'number':
				$attr['type'] = $type;

				//this is done to override the initial input value
				if (!empty($ruleElementData['saved'])) {
					$attr['value'] = esc_attr($ruleElementData['saved']);
				}

				$rowField .= AdminHelper::createInput($ruleElementData['data'], $ruleElementData['saved'], $attr);
				break;
			case 'checkbox':
				$attr['type'] = $type;
				$rowField .= AdminHelper::createCheckBox($ruleElementData['data'], $ruleElementData['saved'], $attr);
				break;
			case  'conditionalText':
				$popupId = self::getPopupId($conditionObj);
				if(!empty($popupId)) {
					$attr['value'] = $attr['value'].$popupId;
					$rowField .= AdminHelper::createInput($ruleElementData['data'], $ruleElementData['saved'].$popupId, $attr);
				}
				else {
					$rowField .= '<div class="sgpb-show-alert-before-save">'.$attr['beforeSaveLabel'].'</div>';
				}
				break;
		}

		return $rowField;
	}

	public static function getPopupId($conditionObj)
	{
		$popupId = 0;
		$conditionPopupId = $conditionObj->getPopupId();

		if (!empty($conditionPopupId)) {
			$popupId = $conditionObj->getPopupId();
		}
		else if(!empty($_GET['post'])) {
			$popupId = sanitize_text_field($_GET['post']);
		}

		return $popupId;
	}

	public static function createElementHeader($ruleElementData)
	{
		$labelAttributes = '';
		$info = '';
		$conditionObj = $ruleElementData['conditionDataObj'];
		$conditionName = $conditionObj->getConditionName();
		$ruleName = $ruleElementData['ruleName'];
		global $SGPB_DATA_CONFIG_ARRAY;
		$conditionConfig = $SGPB_DATA_CONFIG_ARRAY[$conditionName];
		$conditionAttrs = $conditionConfig['attrs'];

		$saveData = $conditionObj->getSavedData();
		$optionTitle = $ruleName;
		$titleKey = $ruleName;


		if ($ruleName == 'value' && !empty($saveData[$conditionObj->getTakeValueFrom()])) {
			$titleKey = $saveData[$conditionObj->getTakeValueFrom()];
		}

		if (!empty($conditionAttrs[$titleKey])) {
			$optionAttrs = $conditionAttrs[$titleKey];
			if (!empty($optionAttrs['infoAttrs'])) {
				// $conditionName => events, conditions, targets...
				// $ruleName => param, operator, value (1-st, 2-nd, 3-rd columns)
				$optionAttrs = apply_filters('sgpb'.$conditionName.$ruleName.'Param', $optionAttrs, $saveData);
				$optionTitle = $optionAttrs['infoAttrs']['label'];
				if (!empty($optionAttrs['infoAttrs']['labelAttrs'])) {
					$labelAttributes = AdminHelper::createAttrs($optionAttrs['infoAttrs']['labelAttrs']);
				}
			}
		}
		if (isset($optionAttrs['infoAttrs']['info']) && $optionAttrs['infoAttrs']['info']) {
			$info = '<div class="question-mark sgpb-info-icon">B</div>';
			$info .= '<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">'.$optionAttrs['infoAttrs']['info'].'</span>
					</div>';
		}

		return "<div class=\"sgpb-display-flex text\"><span class=\"inputBlock__title\" $labelAttributes>$optionTitle</span>$info</div>";
	}

	public static function getHiddenDataContent($conditionDataObj)
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$savedData = $conditionDataObj->getSavedData();
		$conditionName = $savedData['param'];
		$eventsData = $SGPB_DATA_CONFIG_ARRAY['events'];
		$hiddenOptions = $eventsData['hiddenOptionData'];
		$ruleId = $conditionDataObj->getRuleId();
		if (empty($hiddenOptions[$conditionName])) {
			return __('No Data', SG_POPUP_TEXT_DOMAIN);
		}

		$hiddenOptionsData = $hiddenOptions[$conditionName];

		$tabs = array_keys($hiddenOptionsData);
		ob_start();
		?>

		<div class="sgpb sgpb-wrapper">
			<div class="tab">
				<?php
				$activeTab = '';
				if (!empty($tabs[0])) {
					$activeTab = $tabs[0];
				}
				?>
				<?php foreach ($tabs as $tab): ?>
					<?php
					$activeClassName = '';
					if ($activeTab == $tab) {
						$activeClassName = 'sgpb-active';
					}
					?>
					<button class="tablinks sgpb-tab-links <?php echo esc_attr($activeClassName);?>" data-rule-id="<?php echo esc_attr($ruleId); ?>" data-content-id="<?php echo esc_attr($tab.'-'.$ruleId); ?>"><?php echo esc_html(ucfirst($tab)); ?></button>
				<?php endforeach;?>
			</div>
			<?php echo wp_kses(self::createHiddenFields($hiddenOptionsData, $conditionDataObj, $ruleId), AdminHelper::allowed_html_tags()); ?>
			<div class="modal-footer">
				<button type="button" class="sgpb-no-button events-option-close sgpb-modal-cancel sgpb-btn sgpb-btn-gray-light" href="#"><?php esc_html_e('Cancel', SG_POPUP_TEXT_DOMAIN); ?></button>
				<button class="sgpb-btn sgpb-btn-blue sgpb-popup-option-save"><?php esc_html_e('Save', SG_POPUP_TEXT_DOMAIN); ?></button>
			</div>
		</div>
		<?php
		$hiddenPopupContent = ob_get_contents();
		ob_end_clean();

		return $hiddenPopupContent;
	}

	private static function createHiddenFields($hiddenOptionsData, $conditionDataObj, $ruleId)
	{
		ob_start();
		?>
		<?php foreach ($hiddenOptionsData as $key => $hiddenData): ?>
		<div id="<?php echo esc_attr($key.'-'.$ruleId); ?>" class="sgpb-tab-content-<?php echo esc_attr($ruleId);?>">
			<div id="<?php echo esc_attr($key); ?>" class="sgpb-tab-content-options">
				<?php foreach ($hiddenData as $name => $label): ?>
					<?php
					$hiddenOptionsView = self::optionLabelSupplement($conditionDataObj, $name);
					$colMdValue = 6;
					if (!empty($hiddenOptionsView)) {
						$colMdValue = 2;
					}
					?>
					<div class="row form-group formItem sgpb-margin-y-10">
						<div class="col-md-6">
							<?php echo wp_kses(self::createConditionFieldHeader($conditionDataObj, $name), AdminHelper::allowed_html_tags()); ?>
						</div>
						<div class="col-md-<?php echo esc_attr($colMdValue); ?>">
							<?php echo wp_kses(self::createConditionField($conditionDataObj, $name), AdminHelper::allowed_html_tags()); ?>
						</div>
						<?php if (!empty($hiddenOptionsView)): ?>
							<div class="col-md-4">
								<?php echo wp_kses($hiddenOptionsView, AdminHelper::allowed_html_tags()); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach;?>
		<?php
		$hiddenPopupContent = ob_get_contents();
		ob_end_clean();

		return $hiddenPopupContent;
	}

	public static function hiddenSubOptionsView($parentOptionName, $conditionDataObj)
	{
		$subOptionsContent = '';
		$subOptions = self::getHiddenOptionSubOptions($parentOptionName);
		if (!empty($subOptions)) {
			$subOptionsContent =  self::createHiddenSubOptions($parentOptionName, $conditionDataObj, $subOptions);
		}

		return $subOptionsContent;
	}

	private static function createHiddenSubOptions($parentOptionName, $conditionDataObj, $subOptions)
	{
		$name = $parentOptionName;
		ob_start();
		?>
		<div class="row <?php echo esc_attr('sgpb-popup-hidden-content-'.$name.'-'.$conditionDataObj->getRuleId().'-wrapper')?> form-group">
			<?php foreach ($subOptions as $subOption): ?>
				<div class="col-md-6">
					<?php echo wp_kses(self::createConditionFieldHeader($conditionDataObj, $subOption), AdminHelper::allowed_html_tags()); ?>
				</div>
				<div class="col-md-6">
					<?php echo wp_kses(self::createConditionField($conditionDataObj, $subOption), AdminHelper::allowed_html_tags()); ?>
				</div>
				<?php  echo wp_kses(self::hiddenSubOptionsView($subOption, $conditionDataObj), AdminHelper::allowed_html_tags())?>
			<?php endforeach;?>
		</div>
		<?php
		$hiddenPopupContent = ob_get_contents();
		ob_end_clean();

		return $hiddenPopupContent;
	}

	public static function getHiddenOptionSubOptions($optionName)
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$childOptionNames = array();
		$eventsData = $SGPB_DATA_CONFIG_ARRAY['events'];
		$targetDataAttrs = $eventsData['attrs'];

		if (empty($targetDataAttrs[$optionName])) {
			return $childOptionNames;
		}

		if (empty($targetDataAttrs[$optionName]['childOptions'])) {
			return $childOptionNames;
		}
		$childOptionNames = $targetDataAttrs[$optionName]['childOptions'];

		return $childOptionNames;
	}

	private static function getParamRowHideStatus($conditionDataObj, $ruleName)
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		if ($ruleName == 'hiddenOption') {
			return '';
		}
		$status = false;
		$conditionName = $conditionDataObj->getConditionName();
		$saveData = $conditionDataObj->getSavedData();
		$conditionConfig = $SGPB_DATA_CONFIG_ARRAY[$conditionName];
		$paramsData = array();
		if (!empty($conditionConfig['paramsData'])) {
			$paramsData = $conditionConfig['paramsData'];
		}

		$ruleElementData['ruleName'] = $ruleName;
		if ($ruleName == 'value' && !empty($saveData) && !empty($saveData[$conditionDataObj->getTakeValueFrom()])) {
			$ruleName = $saveData[$conditionDataObj->getTakeValueFrom()];
		}
		if ((!isset($paramsData[$ruleName]) && empty($paramsData[$ruleName])) || is_null($paramsData[$ruleName])) {
			$status = true;
		}

		return $status;
	}

	public function targetHeader($targetName = '')
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$data = $SGPB_DATA_CONFIG_ARRAY[$targetName];
		$columnLabels = $data['columns'];
		$header = '<div class="sg-target-header-wrapper">';

		foreach ($columnLabels as $key => $columnLabel) {
			$header .= '<div class="sgpb-col-md">'.$columnLabel.'</div>';
		}
		$header .= '</div>';
		return $header;
	}
}
