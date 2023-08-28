<?php
/**
 * Copyright (C) 2023  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace JvH\IsotopeGooglefeedBundle;

use DateTime;
use DateTimeInterface;
use Isotope\Model\Product;
use Krabo\IsotopeStockBundle\Helper\ProductHelper;

class EventListeners {

  public function feedItem($strType, $objItem, Product $objProduct) {
    if ($strType == 'jvh_googlebase') {
      $objItem->gtin = $objProduct->gtin;
      $objItem->mpn = $objProduct->sku;
      $objItem->brand = $objProduct->gid_brand;
      $objItem->availability = 'out of stock';
      if ($objProduct->isostock_preorder || $objProduct->isotope_packaging_slip_scheduled_shipping_date) {
        $objItem->availability = 'preorder';
        $availabilityDate = new DateTime();
        $availabilityDate->setTimestamp($objProduct->isotope_packaging_slip_scheduled_shipping_date);
        $availabilityDate->modify('+1 day');
        $objItem->availability_date = $availabilityDate->format(DateTimeInterface::ATOM);
      } elseif (ProductHelper::isProductAvailableToOrder($objProduct->id)) {
        $objItem->availability = 'in stock';
      }
    }
    return $objItem;
  }

}