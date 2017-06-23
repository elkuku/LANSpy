<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 21/06/17
 * Time: 12:26
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Host
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @UniqueEntity(fields="max", message="There is already a host with this mac.")
 */
class Host
{
    /**
     * @ORM\Id;
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $mac = '';

    /**
     * @ORM\Column(type="string")
     */
    private $vendor = '';

    /**
     * @ORM\Column(type="string")
     */
    private $hostname = '';

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocked = false;

    /**
     * @ORM\Column(type="string")
     */
    private $name = '';

    /**
     * @param mixed $mac
     *
     * @return Host
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @param mixed $vendor
     *
     * @return Host
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $hostname
     *
     * @return Host
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @param mixed $blocked
     *
     * @return Host
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Host
     */
    public function setName(string $name)
    {
        if (!$name) {
            throw new \UnexpectedValueException('Host name con not be empty');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return mixed
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
