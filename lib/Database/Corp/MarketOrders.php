<?php
/**
 * Contains MarketOrders class.
 *
 * PHP version 5.4
 *
 * LICENSE:
 * This file is part of Yet Another Php Eve Api Library also know as Yapeal
 * which can be used to access the Eve Online API data and place it into a
 * database.
 * Copyright (C) 2014-2016 Michael Cummings
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * You should be able to find a copy of this license in the LICENSE.md file. A
 * copy of the GNU GPL should also be available in the GNU-GPL.md file.
 *
 * @copyright 2014-2016 Michael Cummings
 * @license   http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @author    Stephen Gulick <stephenmg12@gmail.com>
 */
namespace Yapeal\Database\Corp;

use Yapeal\Database\AttributesDatabasePreserverTrait;
use Yapeal\Database\EveApiNameTrait;

/**
 * Class MarketOrders
 */
class MarketOrders extends AbstractCorpSection
{
    use EveApiNameTrait, AttributesDatabasePreserverTrait;
    /**
     * @param string $xml
     * @param string $ownerID
     *
     * @return self
     */
    protected function preserverToMarketOrders(
        $xml,
        $ownerID
    ) {
        $columnDefaults = [
            'ownerID' => $ownerID,
            'orderID' => null,
            'charID' => null,
            'stationID' => null,
            'volEntered' => null,
            'volRemaining' => null,
            'minVolume' => null,
            'orderState' => null,
            'typeID' => null,
            'range' => null,
            'accountKey' => null,
            'duration' => null,
            'escrow' => null,
            'price' => null,
            'bid' => null,
            'issued' => null
        ];
        $this->attributePreserveData(
            $xml,
            $columnDefaults,
            'corpMarketOrders'
        );
        return $this;
    }
    /**
     * @type int $mask
     */
    protected $mask = 4096;
}
