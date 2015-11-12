<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 * Copyright (C) 2010-2015 by Laurent Declercq <l.declercq@nuxwin.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace iMSCP\ApsStandard\Service;

use iMSCP\ApsStandard\ApsDocument;
use iMSCP\ApsStandard\Entity\ApsPackage;
use iMSCP\ApsStandard\Entity\ApsPackageDetails;
use JMS\Serializer\Serializer;
use Zend_Session as SessionHandler;

/**
 * Class ApsPackageService
 * @package iMSCP\ApsStandard\Service
 */
class ApsPackageService extends ApsAbstractService
{
	const PACKAGE_ENTITY_CLASS = 'iMSCP\\ApsStandard\\Entity\\ApsPackage';

	/**
	 * Get all packages
	 *
	 * @return ApsPackage[]
	 */
	public function getPackages()
	{
		$this->getEventManager()->dispatch('onGetApsPackages', array('context' => $this));
		$packages = $this->getEntityManager()->getRepository('Aps:ApsPackage')->findBy(array(
			'status' => ($this->getAuth()->getIdentity()->admin_type === 'admin') ? array('locked', 'unlocked') : 'unlocked'
		));
		return $packages;
	}

	/**
	 * Get one package
	 *
	 * @throws \Exception
	 * @param int $id Package identifier
	 * @return ApsPackage
	 */
	public function getPackage($id)
	{
		$this->getEventManager()->dispatch('onGetApsPackage', array('id' => $id, 'context' => $this));
		$package = $this->getEntityManager()->getRepository('Aps:ApsPackage')->findOneBy(array(
			'id' => $id,
			'status' => ($this->getAuth()->getIdentity()->admin_type === 'admin') ? array('locked', 'unlocked') : 'unlocked'
		));

		if (!$package) {
			throw new \Exception(tr('Package not found.'), 404);
		}

		return $package;
	}

	/**
	 * Get package from the given JSON payload
	 *
	 * @param string $payload JSON payload
	 * @return ApsPackage
	 */
	public function getPackageFromPayload($payload)
	{
		return $this->getSerializer()->deserialize($payload, self::PACKAGE_ENTITY_CLASS, 'json');
	}

	/**
	 * Get package details
	 *
	 * @throws \Exception
	 * @param int $id Package identifier
	 * @return ApsPackageDetails
	 */
	public function getPackageDetails($id)
	{
		$package = $this->getPackage($id);
		$this->getEventManager()->dispatch('onGetApsPackageDetails', array('package' => $package, 'context' => $this));
		$meta = $this->getMetadataDir() . '/' . $package->getApsVersion() . '/' . $package->getName() . '/APP-META.xml';

		if (!file_exists($meta) || filesize($meta) == 0) {
			throw new \RuntimeException(tr('The %s package META file is missing or invalid.', $meta));
		}

		$doc = new ApsDocument($meta);
		$packageDetails = new ApsPackageDetails();
		$packageDetails->setDescription(str_replace(array('  ', "\n"), '', trim($doc->getXPathValue('//root:description'))));
		$packageDetails->setPackager($doc->getXPathValue('//root:packager/root:name') ?:
			parse_url($doc->getXPathValue('//root:package-homepage'), PHP_URL_HOST) ?: tr('Unknown')
		);
		return $packageDetails;
	}

	/**
	 * Update package status
	 *
	 * @param int $id Package identitier
	 * @param string $status New package status
	 * @return void
	 */
	public function updatePackageStatus($id, $status)
	{
		$package = $this->getPackage($id);
		$this->getEventManager()->dispatch('onUpdateApsPackageStatus', array(
			'package' => $package, 'status' => $status, 'context' => $this
		));
		$package->setStatus($status);
		$this->validatePackage($package);
		$this->getEntityManager()->flush($package);
	}

	/**
	 * Update package index
	 *
	 * @return void
	 */
	public function updatePackageIndex()
	{
		$this->getEventManager()->dispatch('onUpdateApsPackageIndex', array('context' => $this));
		SessionHandler::writeClose();
		$this->getServiceLocator()->get('ApsSpiderService')->exploreCatalog();
	}

	/**
	 * Validate package
	 *
	 * @throws \DomainException
	 * @param ApsPackage $package
	 * @return void
	 */
	public function validatePackage(ApsPackage $package)
	{
		if (count($this->getValidator()->validate($package)) > 0) {
			throw new \DomainException(tr('Invalid package.'), 400);
		}
	}

	/**
	 * Get serializer service
	 *
	 * @return Serializer
	 */
	protected function getSerializer()
	{
		return $this->getServiceLocator()->get('Serializer');
	}
}