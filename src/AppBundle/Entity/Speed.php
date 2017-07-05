<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 04/07/17
 * Time: 19:31
 */

namespace AppBundle\Entity;

/**
 * Class Speed
 * @package AppBundle\Entity
 */
class Speed
{
    private $timestamp;
    private $upload;
    private $download;
    private $sponsor;

    /**
     * @param mixed $timestamp
     *
     * @return Speed
     */
    public function setTimestamp($timestamp)
    {
        if (is_string($timestamp)) {
            $this->timestamp = new \DateTime($timestamp);

            return $this;
        }

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @param mixed $upload
     *
     * @return Speed
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;

        return $this;
    }

    /**
     * @param mixed $download
     *
     * @return Speed
     */
    public function setDownload($download)
    {
        $this->download = $download;

        return $this;
    }

    /**
     * @param mixed $sponsor
     *
     * @return Speed
     */
    public function setSponsor($sponsor)
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * @return mixed
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * @return mixed
     */
    public function getDownload()
    {
        return $this->download;
    }
}
