<?php


class Approval
{
	const FIELD_APPROVER_ID = 'approverId';
	const FIELD_TIMESTAMP = 'timestamp';

	private $approverId;
	private $timestamp;

	public function __construct($userId, $timestamp)
	{
		$this->approverId = $userId;
		$this->timestamp = $timestamp;
	}

	public static function fromJsonString($string)
	{
		$json = json_decode($string, true);

		return new Approval($json[self::FIELD_APPROVER_ID], $json[self::FIELD_TIMESTAMP]);
	}

	public function getApproverId()
	{
		return $this->approverId;
	}

	/**
	 * @return mixed
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	public function toJsonString()
	{
		return json_encode([
			self::FIELD_APPROVER_ID => $this->approverId,
			self::FIELD_TIMESTAMP => $this->timestamp,
		]);
	}
}