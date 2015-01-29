<?php
/**
 * Contains EveApiXmlData class.
 *
 * PHP version 5.4
 *
 * LICENSE:
 * This file is part of Yet Another Php Eve Api Library also know as Yapeal
 * which can be used to access the Eve Online API data and place it into a
 * database.
 * Copyright (C) 2014-2015 Michael Cummings
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
 * @copyright 2014-2015 Michael Cummings
 * @license   http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
namespace Yapeal\Xml;

use InvalidArgumentException;
use LogicException;

/**
 * Class EveApiXmlData
 */
class EveApiXmlData implements EveApiReadWriteInterface
{
    /**
     * @param string      $eveApiName
     * @param string      $eveApiSectionName
     * @param string[]    $eveApiArguments
     * @param bool|string $eveApiXml Only allows string or false NOT true.
     * @param int $cacheInterval
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $eveApiName = '',
        $eveApiSectionName = '',
        array $eveApiArguments = [],
        $eveApiXml = false,
        $cacheInterval = 300
    )
    {
        $this->setEveApiName($eveApiName);
        $this->setEveApiSectionName($eveApiSectionName);
        $this->setEveApiArguments($eveApiArguments);
        $this->setEveApiXml($eveApiXml);
        $this->setCacheInterval($cacheInterval);
    }
    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->eveApiXml;
    }
    /**
     * @inheritdoc
     */
    public function addEveApiArgument($name, $value)
    {
        if (!is_string($name)) {
            $mess = 'Name MUST be string but given ' . gettype($name);
            throw new InvalidArgumentException($mess);
        }
        $this->eveApiArguments[$name] = (string)$value;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function getCacheInterval()
    {
        if (!is_int($this->cacheInterval)) {
            $mess = 'Tried to access cache interval before it was set';
            throw new LogicException($mess);
        }
        return $this->cacheInterval;
    }
    /**
     * @inheritdoc
     */
    public function getEveApiArgument($name)
    {
        if (!is_string($name)) {
            $mess = 'Name MUST be string but given ' . gettype($name);
            throw new InvalidArgumentException($mess);
        }
        return $this->eveApiArguments[$name];
    }
    /**
     * @inheritdoc
     */
    public function getEveApiArguments()
    {
        if (null === $this->eveApiArguments) {
            $mess = 'Tried to access Eve Api arguments before it was set';
            throw new LogicException($mess);
        }
        return $this->eveApiArguments;
    }
    /**
     * @inheritdoc
     */
    public function getEveApiName()
    {
        if (0 === strlen($this->eveApiName)) {
            $mess = 'Tried to access Eve Api name before it was set';
            throw new LogicException($mess);
        }
        return $this->eveApiName;
    }
    /**
     * @inheritdoc
     */
    public function getEveApiSectionName()
    {
        if (0 === strlen($this->eveApiSectionName)) {
            $mess = 'Tried to access Eve Api section name before it was set';
            throw new LogicException($mess);
        }
        return $this->eveApiSectionName;
    }
    /**
     * @inheritdoc
     */
    public function getEveApiXml()
    {
        if (0 === strlen($this->eveApiXml)) {
            return false;
        }
        return $this->eveApiXml;
    }
    /**
     * @inheritdoc
     */
    public function getHash()
    {
        $hash = $this->getEveApiName() . $this->getEveApiSectionName();
        foreach ($this->getEveApiArguments() as $key => $value) {
            $hash .= $key . $value;
        }
        return hash('md5', $hash);
    }
    /**
     * @inheritdoc
     */
    public function setCacheInterval($value)
    {
        $this->cacheInterval = (int)$value;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function setEveApiArguments(array $values)
    {
        $this->eveApiArguments = [];
        if (0 === count($values)) {
            return $this;
        }
        foreach ($values as $name => $value) {
            $this->addEveApiArgument($name, $value);
        }
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function setEveApiName($value)
    {
        if (!is_string($value)) {
            $mess = 'Name MUST be string but given ' . gettype($value);
            throw new InvalidArgumentException($mess);
        }
        $this->eveApiName = $value;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function setEveApiSectionName($value)
    {
        if (!is_string($value)) {
            $mess = 'Section name MUST be string but given ' . gettype($value);
            throw new InvalidArgumentException($mess);
        }
        $this->eveApiSectionName = $value;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function setEveApiXml($xml = false)
    {
        if ($xml === false) {
            $xml = '';
        }
        if (!is_string($xml)) {
            $mess = 'Xml MUST be string but given ' . gettype($xml);
            throw new InvalidArgumentException($mess);
        }
        $this->eveApiXml = $xml;
        return $this;
    }
    /**
     * @type int $cacheInterval
     */
    protected $cacheInterval;
    /**
     * @type string[] $eveApiArguments
     */
    protected $eveApiArguments;
    /**
     * @type string $eveApiName
     */
    protected $eveApiName;
    /**
     * @type string $eveApiSectionName
     */
    protected $eveApiSectionName;
    /**
     * @type string $eveApiXml
     */
    protected $eveApiXml;
}
