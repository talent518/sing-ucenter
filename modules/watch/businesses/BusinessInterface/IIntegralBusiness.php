<?php
namespace app\modules\watch\businesses\BusinessInterface;

interface IIntegralBusiness {

	/**
	 * 根据用户ID、期数ID和课程ID获取星星记录列表
	 *
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param array $class_id 班级ID
	 * @param array $course_id 课程ID(如果产品工具时course_id为0)
	 * @param array $business_type 业务类型(1教材 2环节 3学习报告 4调查问卷 5生成证书 6分享证书 7礼品兑换 8成长记录 9家长须知)
	 * @param array $dest_type 目标类型(1产品2课程3主题4教材5环节)
	 * @return array
	 */
	public function course(int $user_id, int $periods_id, array $class_id = [], array $course_id = [], array $business_type = [], array $dest_type = []) : array;

	/**
	 * 查询用户星星数
	 *
	 * @param int $user_id 用户ID
	 * @return int
	 */
	public function view(int $user_id) : int;

	/**
	 * 查询用户星星数
	 *
	 * @param int $user_id 用户ID
	 * @return int
	 */
	public function viewMerge(int $user_id) : int;

	/**
	 * 记录星星明细并自动更新用户星星数
	 *
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $class_id 班级ID
	 * @param int $course_id 课程ID(如果产品工具时course_id为0)
	 * @param int $business_type 业务类型(1教材 2环节 3学习报告 4调查问卷 5生成证书 6分享证书 7礼品兑换 8成长记录 9家长须知)
	 * @param int $dest_type 目标类型(1产品2课程3主题4教材5环节)
	 * @param int $dest_id 目标ID
	 * @param int $stars 星星数
	 * @param string $remark 备注
	 * @param int $duplicates 允许重复次数
	 * @param string $platform 平台：iphone, ipad, android, h5, mini
	 * @return boolean
	 */
	public function create(int $user_id, int $periods_id, int $class_id, int $course_id, int $business_type, int $dest_type, int $dest_id, int $stars, string $remark = '', int $duplicates = 0, string $platform = ''): bool;

}