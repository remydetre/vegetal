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

/**
 * Class PGDomainServicesRepositoriesButtonRepository
 * @package PGModule\Services\Repositories
 */
class PGDomainServicesRepositoriesButtonRepository extends PGFrameworkFoundationsAbstractRepositoryDatabase implements PGDomainInterfacesRepositoriesButtonRepositoryInterface
{
    /** @var PGDomainInterfacesShopHandlerInterface */
    private $shopHandler;

    public function __construct(
        PGFrameworkServicesHandlersDatabaseHandler $databaseHandler,
        PGDomainInterfacesShopHandlerInterface $shopHandler,
        array $config
    ) {
        parent::__construct($databaseHandler, $config);

        $this->shopHandler = $shopHandler;
    }

    protected function getShopPrimary()
    {
        return $this->shopHandler->getCurrentShopPrimary();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function findAll()
    {
        return $this->findAllEntities("`id_shop` = '{$this->getShopPrimary()}'");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create()
    {
        return $this->wrapEntity(array(
            'paymentNumber' => 1,
            'displayType' => 'DEFAULT',
            'integration' => 'EXTERNAL',
            'paymentType' => 'CB',
            'paymentMode' => 'CASH',
            'height' => 60,
            'id_shop' => $this->getShopPrimary()
        ));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function insert(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->insertEntity($button);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function update(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->updateEntity($button);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function delete(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->deleteEntity($button);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function count()
    {
        $table = "%{database.entities.button.table}";
        return (int) $this->getRequester()->fetchValue("SELECT COUNT(*) AS value FROM `$table`");
    }
}
