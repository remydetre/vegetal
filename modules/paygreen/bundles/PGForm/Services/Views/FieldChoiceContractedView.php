<?php
/**
 * 2014 - 2020 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 */

class PGFormServicesViewsFieldChoiceContractedView extends PGFormFoundationsAbstractFieldChoice
{
    public function getData()
    {
        $data = parent::getData();

        if (!array_key_exists('choices', $data)) {
            throw new Exception("FieldChoiceExpandedView require 'choices' configuration key.");
        } elseif (is_string($data['choices'])) {
            $data['choices'] = $this->getSelectHandler()->getChoices($data['choices']);
        }

        if ($data['translate']) {
            array_walk($data['choices'], array($this, 'translate'));
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function completeFieldAttributes(array $data)
    {
        $attr = array_key_exists('attr', $data) ? $data['attr'] : array();

        if ($data['multiple']) {
            $attr['multiple'] = "multiple";
        }

        $attr['name'] = $this->getField()->getFormName();
        $attr['id'] = 'pg_field_' . $this->getField()->getFieldPrimary();

        if ($this->getField()->isRequired()) {
            $attr['required'] = 'required';
        }

        return $attr;
    }
}
