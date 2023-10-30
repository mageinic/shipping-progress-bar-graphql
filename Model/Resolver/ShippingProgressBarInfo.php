<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_ShippingProgressBarGraphql
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\ShippingProgressBarGraphql\Model\Resolver;

use MageINIC\ShippingProgressBar\Block\Cart\Sidebar;
use MageINIC\ShippingProgressBar\Model\ShippingProgressBar;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class ShippingProgressBarInfo Resolver File.
 */
class ShippingProgressBarInfo implements ResolverInterface
{
    /**
     * @var Sidebar
     */
    private Sidebar $sidebar;

    /**
     * @var ShippingProgressBar
     */
    private ShippingProgressBar $shippingProgressBar;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * ShippingProgressBarInfo Construct.
     *
     * @param Sidebar $sidebar
     * @param ShippingProgressBar $shippingProgressBar
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Sidebar                 $sidebar,
        ShippingProgressBar     $shippingProgressBar,
        CartRepositoryInterface $cartRepository
    ) {
        $this->sidebar = $sidebar;
        $this->shippingProgressBar = $shippingProgressBar;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Return the Shipping Progress Bar Information.
     *
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): mixed
    {
        if (!isset($args['quoteId']) || empty($args['quoteId'])) {
            throw new GraphQlAuthorizationException(__('Quote Id is required.'));
        }
        try {
            $result = $this->getShippingProgressBarInfo($args['quoteId']);
            return !empty($result) ? $result : [];
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        }
    }

    /**
     * Shipping Progress Bar Information fetch by quote id.
     *
     * @param string $quoteId
     * @return ShippingProgressBar
     * @throws NoSuchEntityException
     */
    public function getShippingProgressBarInfo($quoteId): mixed
    {
        $shippingProgressBar = $this->shippingProgressBar;
        $moduleEnable = $this->sidebar->getShippingProgressBarEnable();
        if (!$moduleEnable) {
            throw new NoSuchEntityException(__('Module is disable.'));
        }
        if (!$quoteId) {
            throw new NoSuchEntityException(__('Quote ID is missing.'));
        }
        $orderMinimunAmount = $this->sidebar->getPriceForShippingProgressBar();
        if (!$orderMinimunAmount) {
            throw new NoSuchEntityException(__('Order minimun amount is missing.'));
        }
        $quote = $this->cartRepository->get($quoteId);
        $currencySymbol = $this->sidebar->getCurrentCurrencySymbol();
        if ($quote->getSubtotal() >= $orderMinimunAmount) {
            if ($this->sidebar->getAchievedMessage()) {
                $shippingProgressBar->setAchievedMessage($this->sidebar->getAchievedMessage());
            }
        } else {
            $remainingPrice = ($orderMinimunAmount - $quote->getSubtotal());
            if ($this->sidebar->inProgressMessage() && ($orderMinimunAmount != $remainingPrice)) {
                $price = $currencySymbol . $remainingPrice;
                $shippingProgressBar->setInProgressMessage(
                    strtr($this->sidebar->inProgressMessage(), ['{{price}}' => $price])
                );
            }
            if ($this->sidebar->getInitialMessage() && ($orderMinimunAmount == $remainingPrice)) {
                $price = $currencySymbol . $orderMinimunAmount;
                $shippingProgressBar->setInitialMessage(
                    strtr($this->sidebar->getInitialMessage(), ['{{price}}' => $price])
                );
            }
        }

        return $shippingProgressBar;
    }
}
