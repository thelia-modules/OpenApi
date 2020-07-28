<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\Customer as TheliaCustomer;
use Thelia\Model\Lang as TheliaLang;
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
    /**
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $code;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $locale;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $url;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $dateFormat;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $timeFormat;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $datetimeFormat;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $decimalSeparator;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $thousandsSeparator;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $active;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $visible;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $decimals;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $byDefault;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Lang
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return Lang
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     *
     * @return Lang
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     *
     * @return Lang
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return Lang
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param mixed $dateFormat
     *
     * @return Lang
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    /**
     * @param mixed $timeFormat
     *
     * @return Lang
     */
    public function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    /**
     * @param mixed $datetimeFormat
     *
     * @return Lang
     */
    public function setDatetimeFormat($datetimeFormat)
    {
        $this->datetimeFormat = $datetimeFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecimalSeparator()
    {
        return $this->decimalSeparator;
    }

    /**
     * @param mixed $decimalSeparator
     *
     * @return Lang
     */
    public function setDecimalSeparator($decimalSeparator)
    {
        $this->decimalSeparator = $decimalSeparator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * @param mixed $thousandsSeparator
     *
     * @return Lang
     */
    public function setThousandsSeparator($thousandsSeparator)
    {
        $this->thousandsSeparator = $thousandsSeparator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @return Lang
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     *
     * @return Lang
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param mixed $decimals
     *
     * @return Lang
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getByDefault()
    {
        return $this->byDefault;
    }

    /**
     * @param mixed $byDefault
     *
     * @return Lang
     */
    public function setByDefault($byDefault)
    {
        $this->byDefault = $byDefault;
        return $this;
    }
}