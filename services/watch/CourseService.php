<?php
namespace app\services\watch;

use app\core\CCRequest;

class CourseService {

	/**
	 * 根据素材ID查询并返回是否可播放、素材时长
	 *
	 * @param int $course_id
	 * @param int $textbook_id
	 * @param int $segment_id
	 * @param int $element_id
	 * @return boolean|mixed
	 */
	public static function getElement(int $course_id, int $textbook_id, int $segment_id, int $element_id) {
		$element = \Yii::$app->cache->getOrSet([
			__METHOD__,
			$course_id,
			$textbook_id,
			$segment_id,
			$element_id
		], function () use ($course_id, $textbook_id, $segment_id, $element_id) {
			return json_decode((new CCRequest(\Yii::$app->params['apiCourseService'] . "segment-materials/{$element_id}?scene=user-service&course_id={$course_id}&textbook_id={$textbook_id}&segment_id={$segment_id}", 'GET'))->send(), true);
		}, 300);
		if(!$element || !empty($element['code']) || empty($element['data'])) {
			return false;
		} else {
			return $element['data'];
		}
	}

	/**
	 * 根据环节ID查询并返回每个素材的素材ID 、是否可播放、素材时长
	 * 
	 * @param int $segment_id
	 * @return boolean|mixed
	 */
	public static function getSegment(int $segment_id) {
		$element = \Yii::$app->cache->getOrSet([
			__METHOD__,
			$segment_id
		], function () use ($segment_id) {
			return json_decode((new CCRequest(\Yii::$app->params['apiCourseService'] . "segments/{$segment_id}?scene=user-service", 'GET'))->send(), true);
		}, 300);
		if(!$element || !empty($element['code']) || empty($element['data'])) {
			return false;
		} else {
			return $element['data'];
		}
	}

	/**
	 * 根据教材ID查询并返回每个环节的环节ID
	 * 
	 * @param int $segment_id
	 * @return boolean|mixed
	 */
	public static function getTextBook(int $textbook_id) {
		$element = \Yii::$app->cache->getOrSet([
			__METHOD__,
			$textbook_id
		], function () use ($textbook_id) {
			return json_decode((new CCRequest(\Yii::$app->params['apiCourseService'] . "textbooks/{$textbook_id}?scene=user-service", 'GET'))->send(), true);
		}, 300);
		if(!$element || !empty($element['code']) || empty($element['data'])) {
			return false;
		} else {
			return $element['data'];
		}
	}

	/**
	 * 根据课程ID查询并返回每个教材的教材ID
	 * 
	 * @param int $segment_id
	 * @return boolean|mixed
	 */
	public static function getCourse(int $course_id) {
		$element = \Yii::$app->cache->getOrSet([
			__METHOD__,
			$course_id
		], function () use ($course_id) {
			return json_decode((new CCRequest(\Yii::$app->params['apiCourseService'] . "courses/{$course_id}?scene=user-service", 'GET'))->send(), true);
		}, 300);
		if(!$element || !empty($element['code']) || empty($element['data'])) {
			return false;
		} else {
			return $element['data'];
		}
	}

}