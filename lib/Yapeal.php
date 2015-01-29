<?php
/**
 * Contains Yapeal class.
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
namespace Yapeal;

use FilePathNormalizer\FilePathNormalizerTrait;
use PDO;
use PDOException;
use PDOStatement;
use Yapeal\Configuration\Wiring;
use Yapeal\Configuration\WiringInterface;
use Yapeal\Container\ContainerInterface;
use Yapeal\Event\ContainerAwareEventDispatcher;
use Yapeal\Event\EveApiEventInterface;
use Yapeal\Exception\YapealDatabaseException;
use Yapeal\Exception\YapealException;
use Yapeal\Log\Logger;
use Yapeal\Sql\CommonSqlQueries;
use Yapeal\Xml\EveApiReadWriteInterface;

/**
 * Class Yapeal
 */
class Yapeal implements WiringInterface
{
    use FilePathNormalizerTrait;
    /**
     * @param ContainerInterface $dic
     */
    public function __construct(ContainerInterface $dic)
    {
        $this->setDic($dic);
        $this->wire($this->getDic());
    }
    /**
     * Starts Eve API processing
     *
     * @return int Returns 0 if everything was fine else something >= 1 for any
     * errors.
     */
    public function autoMagic()
    {
        $dic = $this->getDic();
        /**
         * @type ContainerAwareEventDispatcher $yed
         */
        $yed = $dic['Yapeal.Event.EventDispatcher'];
        $mess = 'Let the magic begin!';
        $yed->dispatchLogEvent('Yapeal.Log.log', Logger::INFO, $mess);
        /**
         * @type CommonSqlQueries $csq
         */
        $csq = $dic['Yapeal.Database.CommonQueries'];
        $sql = $csq->getActiveApis();
        $yed->dispatchLogEvent('Yapeal.Log.log', Logger::INFO, $sql);
        try {
            /**
             * @type PDO $pdo
             */
            $pdo = $dic['Yapeal.Database.Connection'];
            /**
             * @type PDOStatement $smt
             */
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exc) {
            $mess = 'Could not access utilEveApi table';
            $yed->dispatchLogEvent(
                'Yapeal.Log.log',
                Logger::INFO,
                $mess,
                ['exception' => $exc]
            );
            return 1;
        }
        // Always check APIKeyInfo.
        array_unshift(
            $result,
            [
                'apiName' => 'APIKeyInfo',
                'interval' => '300',
                'sectionName' => 'Account'
            ]
        );
        foreach ($result as $record) {
            /**
             * @type EveApiReadWriteInterface $data
             */
            $data = $dic['Yapeal.Xml.Data'];
            $data->setEveApiName($record['apiName'])
                 ->setEveApiSectionName($record['sectionName'])
                 ->setCacheInterval($record['interval']);
            $this->emitEvents($data, $yed);
        }
        return 0;
    }
    /**
     * @param ContainerInterface $value
     *
     * @return self
     */
    public function setDic(ContainerInterface $value)
    {
        $this->dic = $value;
        return $this;
    }
    /**
     * @param ContainerInterface $dic
     *
     * @throws YapealException
     * @throws YapealDatabaseException
     */
    public function wire(ContainerInterface $dic)
    {
        $path = $this->getFpn()
                     ->normalizePath(dirname(__DIR__));
        if (empty($dic['Yapeal.cwd'])) {
            $dic['Yapeal.cwd'] = $path;
        }
        if (empty($dic['Yapeal.baseDir'])) {
            $dic['Yapeal.baseDir'] = $path;
        }
        if (empty($dic['Yapeal.vendorParentDir'])) {
            $vendorPos = strpos($path, 'vendor/');
            if (false !== $vendorPos) {
                $dic['Yapeal.vendorParentDir'] = substr($path, 0, $vendorPos);
            }
        }
        (new Wiring($dic))->wireConfig()
                          ->wireError()
                          ->wireCache()
                          ->wireDatabase()
                          ->wireEvent()
                          ->wireLog()
                          ->wireNetwork()
                          ->wireXml();
    }
    /**
     * @param EveApiReadWriteInterface      $data
     * @param ContainerAwareEventDispatcher $yed
     *
     * @return self
     */
    protected function emitEvents(
        EveApiReadWriteInterface $data,
        ContainerAwareEventDispatcher $yed
    )
    {
        // Yapeal.EveApi.Section.Api.start, Yapeal.EveApi.Api.start,
        // Yapeal.EveApi.Section.start, Yapeal.EveApi.start
        $eventNames = sprintf(
            '%3$s.%1$s.%2$s.start,%3$s.%2$s.start,%3$s.%1$s.start,%3$s.start',
            $data->getEveApiSectionName(),
            $data->getEveApiName(),
            'Yapeal.EveApi'
        );
        foreach (explode(',', $eventNames) as $eventName) {
            /**
             * @type EveApiEventInterface $event
             */
            $event = $yed->dispatchEveApiEvent($eventName, $data);
            $data = $event->getData();
            if ($event->isHandled()) {
                break;
            }
        }
        return $this;
    }
    /**
     * @return array
     */
    protected function getActiveEveApiList()
    {
        $list = [];
        return $list;
    }
    /**
     * @return ContainerInterface
     */
    protected function getDic()
    {
        return $this->dic;
    }
    /**
     * @type ContainerInterface $dic
     */
    protected $dic;
}
