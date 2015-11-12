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

namespace iMSCP\ApsStandard\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ApsPackage
 * @package iMSCP\ApsStandard\Entity
 * @ORM\Table(
 *   name="aps_package",
 *   indexes={@ORM\Index(columns={"name", "version", "release"}), @ORM\Index(columns={"status"})},
 *   options={"collate"="utf8_unicode_ci", "charset"="utf8", "engine"="InnoDB"}
 * )
 * @ORM\Entity
 * @JMS\AccessType("public_method")
 */
class ApsPackage
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @JMS\Type("integer")
	 * @JMS\AccessType("property")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 * @JMS\Type("string")
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="summary", type="text", length=65535, nullable=false)
	 * @JMS\Type("string")
	 */
	private $summary;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="version", type="string", length=15, nullable=false)
	 * @JMS\Type("string")
	 */
	private $version;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="`release`", type="string", length=15, nullable=false)
	 * @JMS\Type("string")
	 */
	private $release;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="aps_version", type="string", length=15, nullable=false)
	 * @JMS\Type("string")
	 */
	private $apsVersion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="category", type="string", length=50, nullable=false)
	 * @JMS\Type("string")
	 */
	private $category;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="vendor", type="string", length=255, nullable=false)
	 * @JMS\Type("string")
	 */
	private $vendor;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="vendor_uri", type="string", length=255, nullable=false)
	 * @JMS\Type("string")
	 */
	private $vendorUri;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="url", type="string", length=255, nullable=false)
	 * @JMS\Type("string")
	 */
	private $url;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="icon_url", type="string", length=255, nullable=false)
	 * @JMS\Type("string")
	 */
	private $iconUrl;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="cert", type="string", length=15, nullable=false)
	 * @JMS\Type("string")
	 */
	private $cert;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="status", type="string", length=255, nullable=false)
	 * @JMS\Type("string")
	 * @Assert\Choice(choices = {"locked", "unlocked", "outdated", "obsolete"}, message = "Invalid status.")
	 */
	private $status;

	/**
	 * Get package identifier
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set package name
	 *
	 * @param string $name
	 * @return ApsPackage
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get package name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set package summary
	 *
	 * @param string $summary
	 * @return ApsPackage
	 */
	public function setSummary($summary)
	{
		$this->summary = $summary;
		return $this;
	}

	/**
	 * Get package summary
	 *
	 * @return string
	 */
	public function getSummary()
	{
		return $this->summary;
	}

	/**
	 * Set package version
	 *
	 * @param string $version
	 * @return ApsPackage
	 */
	public function setVersion($version)
	{
		$this->version = $version;
		return $this;
	}

	/**
	 * Get package version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Set package release
	 *
	 * @param integer $release
	 * @return ApsPackage
	 */
	public function setRelease($release)
	{
		$this->release = $release;
		return $this;
	}

	/**
	 * Get package release
	 *
	 * @return integer
	 */
	public function getRelease()
	{
		return $this->release;
	}

	/**
	 * Set package aps version
	 *
	 * @param string $apsVersion
	 * @return ApsPackage
	 */
	public function setApsVersion($apsVersion)
	{
		$this->apsVersion = $apsVersion;
		return $this;
	}

	/**
	 * Get package aps version
	 *
	 * @return string
	 */
	public function getApsVersion()
	{
		return $this->apsVersion;
	}

	/**
	 * Set package category
	 *
	 * @param string $category
	 * @return ApsPackage
	 */
	public function setCategory($category)
	{
		$this->category = $category;
		return $this;
	}

	/**
	 * Get package category
	 *
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Set package vendor
	 *
	 * @param string $vendor
	 * @return ApsPackage
	 */
	public function setVendor($vendor)
	{
		$this->vendor = $vendor;
		return $this;
	}

	/**
	 * Get package vendor
	 *
	 * @return string
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * Set package vendor URI
	 *
	 * @param string $vendorUri
	 * @return ApsPackage
	 */
	public function setVendorUri($vendorUri)
	{
		$this->vendorUri = $vendorUri;
		return $this;
	}

	/**
	 * Get package vendor URI
	 *
	 * @return string
	 */
	public function getVendorUri()
	{
		return $this->vendorUri;
	}

	/**
	 * Set package URL
	 *
	 * @param string $url
	 * @return ApsPackage
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * Get package URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Set package icon URL
	 *
	 * @param string $iconUrl
	 * @return ApsPackage
	 */
	public function setIconUrl($iconUrl)
	{
		$this->iconUrl = $iconUrl;
		return $this;
	}

	/**
	 * Get package icon URL
	 *
	 * @return string
	 */
	public function getIconUrl()
	{
		return $this->iconUrl;
	}

	/**
	 * Set package cert
	 *
	 * @param string $cert
	 * @return ApsPackage
	 */
	public function setCert($cert)
	{
		$this->cert = $cert;
		return $this;
	}

	/**
	 * Get package cert
	 *
	 * @return string
	 */
	public function getCert()
	{
		return $this->cert;
	}

	/**
	 * Set package status
	 *
	 * @param string $status
	 * @return ApsPackage
	 */
	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * Get package status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}
}