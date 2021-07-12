<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

/**
 * @OA\Schema(
 *     schema="Lang",
 *     title="Lang",
 *     description="Lang model"
 * )
 */
class Lang extends BaseApiModel
{
    static $serviceAliases = ["Language"];

    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $code;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $locale;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $url;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $dateFormat;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $timeFormat;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $datetimeFormat;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $decimalSeparator;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $thousandsSeparator;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $active;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $visible;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $decimals;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $byDefault;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Lang
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Lang
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Lang
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Lang
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Lang
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     *
     * @return Lang
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    /**
     * @param string $timeFormat
     *
     * @return Lang
     */
    public function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    /**
     * @param string $datetimeFormat
     *
     * @return Lang
     */
    public function setDatetimeFormat($datetimeFormat)
    {
        $this->datetimeFormat = $datetimeFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->decimalSeparator;
    }

    /**
     * @param string $decimalSeparator
     *
     * @return Lang
     */
    public function setDecimalSeparator($decimalSeparator)
    {
        $this->decimalSeparator = $decimalSeparator;

        return $this;
    }

    /**
     * @return string
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * @param string $thousandsSeparator
     *
     * @return Lang
     */
    public function setThousandsSeparator($thousandsSeparator)
    {
        $this->thousandsSeparator = $thousandsSeparator;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return Lang
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return Lang
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param string $decimals
     *
     * @return Lang
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return bool
     */
    public function isByDefault()
    {
        return $this->byDefault;
    }

    /**
     * @param bool $byDefault
     *
     * @return Lang
     */
    public function setByDefault($byDefault)
    {
        $this->byDefault = $byDefault;

        return $this;
    }
}
