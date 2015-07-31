<?php

namespace DDSBase\Stdlib;

use \DateTime as PHPDateTime;

/**
 * Description of DateTime
 *
 * @author domanski
 */
abstract class DateTime {
	const TYPE_DATE = 1;
	const TYPE_TIME = 2;
	const TYPE_DATETIME = 3;
	
	/**
	 * 
	 * @param \DateTime|int|string|null $date
	 * @return \DateTime
	 * @throws \Exception
	 */
	public static function getDate($date = null, $default = null) {		
		if($date instanceof PHPDateTime)
			return $date;
		
		if(is_int($date))
			return new PHPDateTime("@{$date}", new \DateTimeZone('UTC'));
			
		if(is_string($date))
			return new PHPDateTime($date, new \DateTimeZone('UTC'));
		
		if($date instanceof \Doctrine\Common\EventArgs) {
			return new PHPDateTime('now', new \DateTimeZone('UTC'));
		}
		
		return $default;
	}
	
	/**
	 * 
	 * @param string $date
	 * @param int $options default TYPE_DATETIME
	 * @return \DateTime
	 */
	public static function phpToBd($date, $options = self::TYPE_DATETIME, \DateTimeZone $timezone = null, $locale = null) {
		$datefmt = new \IntlDateFormatter(
				(!empty($locale))?$locale:locale_get_default(),
				($options & self::TYPE_DATE)?\IntlDateFormatter::MEDIUM:\IntlDateFormatter::NONE,
				($options & self::TYPE_TIME)?\IntlDateFormatter::MEDIUM:\IntlDateFormatter::NONE,
				($timezone !== null)?(version_compare(PHP_VERSION, "5.4.0", "<"))?$timezone->getName():$timezone:$timezone
		);
					
		return self::getDate($datefmt->parse($date));
	}
	
	/**
	 * 
	 * @param \DateTime $date
	 * @param int $options default TYPE_DATETIME
	 * @param \DateTimeZone $timezone default null
	 * @param string $locale default null
	 * @return string
	 */
	public static function bdToPhp(\DateTime $date, $options = self::TYPE_DATETIME, \DateTimeZone $timezone = null, $locale = null) {		
		$datefmt = new \IntlDateFormatter(
				(!empty($locale))?$locale:locale_get_default(),
				($options & self::TYPE_DATE)?\IntlDateFormatter::MEDIUM:\IntlDateFormatter::NONE,
				($options & self::TYPE_TIME)?\IntlDateFormatter::MEDIUM:\IntlDateFormatter::NONE,
				($timezone !== null)?(version_compare(PHP_VERSION, "5.4.0", "<"))?$timezone->getName():$timezone:$timezone
		);
		
		return $datefmt->format($date->getTimestamp());
	}
	
	/**
	 * 
	 * @param int $options
	 * @param string $locale
	 * @return string
	 */
	public static function getDateFormatterPattern($options = self::TYPE_DATETIME, $locale = null) {
		$datefmt = new \IntlDateFormatter(
				(!empty($locale))?$locale:locale_get_default(),
				($options & self::TYPE_DATE)?\IntlDateFormatter::MEDIUM:\IntlDateFormatter::NONE,
				($options & self::TYPE_TIME)?\IntlDateFormatter::MEDIUM:\IntlDateFormatter::NONE
		);
		
		return $datefmt->getPattern();
	}
}
