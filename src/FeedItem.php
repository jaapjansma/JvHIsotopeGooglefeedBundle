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

use Contao\StringUtil;
use Rhyme\IsotopeFeedsBundle\FeedItem\Rss20;

class FeedItem extends Rss20 {
  /**
   * Cache the item's XML node to a file
   * @param string
   */
  public function cache($strLocation)
  {
    $arrGoogleFields = array
    (
      'id',
      'price',
      'availability',
      'availability_date',
      'condition',
      'image_link',
      'product_type',
      'google_product_category',
      'brand',
      'gtin',
      'mpn',
      'additional_image_link',
      'sale_price',
      'sale_price_effective_date',
      'item_group_id',
      'shipping_label',
      'shipping_weight',
      'color',
      'material',
      'pattern',
      'size',
      'gender',
      'age_group',
    );

    if(!strlen($this->gtin)) {
      $this->identifier_exists = 'no';
      $arrGoogleFields[] = 'identifier_exists';
    }


    $xml = '	<item>' . "\n";
    $xml .= '      <title>' . StringUtil::specialchars($this->title) . '</title>' . "\n";
    $xml .= '      <description><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $this->description) . ']]></description>' . "\n";
    $xml .= '      <link><![CDATA[' . StringUtil::specialchars($this->link) . ']]></link>' . "\n";

    foreach($arrGoogleFields as $strKey)
    {
      if($this->__isset($strKey) )
      {
        if(is_array($this->{$strKey}) && count($this->{$strKey}))
        {
          foreach($this->{$strKey} as $value)
          {
            $xml .= '      <g:'.$strKey.'><![CDATA[' . StringUtil::specialchars($value) . ']]></g:'.$strKey.'>' . "\n";
          }
        }
        elseif(!is_array($this->{$strKey}) && strlen($this->{$strKey}))
        {
          $xml .= '      <g:'.$strKey.'><![CDATA[' . StringUtil::specialchars($this->{$strKey}) . ']]></g:'.$strKey.'>' . "\n";
        }
      }
    }
    if($this->shipping)
    {
      $xml .= $this->shipping;
    }
    $xml .= '	</item>' . "\n";

    $this->write($xml, $strLocation);
  }

}