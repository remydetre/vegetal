<?php
/**
 * 2014 - 2015 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PayGreen <contact@paygreen.fr>
 *  @copyright 2014-2014 Watt It Is
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

class PaygreenEntitiesPaygreenTransaction extends PaygreenFoundationsAbstractEntityRecursiveObject
{
    /** @var int */
    protected $id;

    /** @var int|null  */
    protected $orderPrimary = null;

    /** @var int */
    protected $amount;

    /** @var string */
    protected $currency;

    /** @var string */
    protected $mode;

    /** @var string */
    protected $type;

    /** @var string */
    protected $url;

    /** @var bool */
    protected $testing;

    /** @var int */
    protected $fingerPrintPrimary;

    /** @var DateTime */
    protected $createdAt;

    /** @var DateTime|null */
    protected $valueAt = null;

    /** @var DateTime */
    protected $answeredAt;

    /** @var PaygreenEntitiesPaygreenTransactionResult|null  */
    protected $result = null;

    protected $card = null;

    protected $buyer = null;

    protected $donations = null;

    /** @var array  */
    protected $metadata = array();

    protected $schedules = array();

    /** @var string */
    private $transactionType;

    /** @var string */
    private $pid;

    /**
     * @throws Exception
     */
    protected function compile()
    {
        $this
            ->setScalar('id', 'id')
            ->setScalar('orderId', 'orderPrimary', 'int')
            ->setScalar('amount', 'amount', 'int')
            ->setScalar('currency', 'currency')
            ->setScalar('type', 'mode')
            ->setScalar('paymentType', 'type')
            ->setScalar('url', 'url')
            ->setScalar('testMode', 'testing', 'bool')
            ->setScalar('idFingerprint', 'fingerPrintPrimary', 'int')
            ->setObject('createdAt', 'createdAt', 'DateTime')
            ->setObject('answeredAt', 'answeredAt', 'DateTime')
            ->setObject('result', 'result', 'PaygreenEntitiesPaygreenTransactionResult')
        ;

        $this->metadata = $this->hasRaw('metadata') ?
            (array) $this->getRaw('metadata') :
            array()
        ;

        $this->pid = Tools::substr($this->getId(), 2);
        $this->transactionType = Tools::strtoupper(Tools::substr($this->getId(), 0, 2));

        if (!in_array($this->getTransactionType(), array('TR', 'PR'))) {
            throw new Exception("Unknown transaction type : '{$this->getTransactionType()}'.");
        }

        if (!in_array($this->getMode(), array('CASH', 'RECURRING', 'XTIME', 'TOKENIZE'))) {
            throw new Exception("Unknown payment mode : '{$this->getMode()}'.");
        }

        if ($this->hasRaw('valueAt') and ($this->getRaw('valueAt') > 0)) {
            $this->valueAt = new DateTime($this->getRaw('valueAt'));
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getOrderPrimary()
    {
        return $this->orderPrimary;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getUserAmount()
    {
        return (float) $this->amount / 100;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isTesting()
    {
        return (bool) $this->testing;
    }

    /**
     * @return int
     */
    public function getFingerPrintPrimary()
    {
        return $this->fingerPrintPrimary;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getValueAt()
    {
        return $this->valueAt;
    }

    /**
     * @return DateTime
     */
    public function getAnsweredAt()
    {
        return $this->answeredAt;
    }

    public function getMetadata($name)
    {
        return $this->hasMetadata($name) ? $this->metadata[$name] : null;
    }

    public function hasMetadata($name)
    {
        return array_key_exists($name, $this->metadata);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return PaygreenEntitiesPaygreenTransactionResult|null
     */
    public function getResult()
    {
        return $this->result;
    }
}
