<?php

declare(strict_types=1);

namespace Gandung\Tokopedia\Service;

use InvalidArgumentException;
use Gandung\Tokopedia\AbstractService;

class Interaction extends AbstractService
{
	/**
	 * @var string
	 */
	const INTERACTION_MESSAGE_ORDER_ASC = "asc";

	/**
	 * @var string
	 */
	const INTERACTION_MESSAGE_ORDER_DESC = "desc";

	/**
	 * @var string
	 */
	const INTERACTION_MESSAGE_FILTER_ALL = "all";

	/**
	 * @var string
	 */
	const INTERACTION_MESSAGE_FILTER_READ = "read";

	/**
	 * @var string
	 */
	const INTERACTION_MESSAGE_FILTER_UNREAD = "unread";

	/**
	 * Get list of chat message.
	 *
	 * @param int $shopID Shop ID.
	 * @param int $page Current page number.
	 * @param int $perPage How much message showed per page.
	 * @param string $order
	 * @param string $filter
	 * @return string
	 * @throws InvalidArgumentException when 'page' parameter less or equal to zero.
	 * @throws InvalidArgumentException when 'perPage' parameter less or equal to zero.
	 * @throws InvalidArgumentException when 'order' parameter is invalid.
	 * @throws InvalidArgumentException when 'filter' parameter is invalid.
	 */ 
	public function getListMessage(
		int $shopID,
		int $page = 1,
		int $perPage = 10,
		string $order = self::INTERACTION_MESSAGE_ORDER_ASC,
		string $filter = self::INTERACTION_MESSAGE_FILTER_ALL
	) {
		if ($page <= 0) {
			throw new InvalidArgumentException("'page' parameter must be larger than zero.");
		}

		if ($perPage <= 0) {
			throw new InvalidArgumentException("'perPage' parameter must be larger than zero.");
		}

		$this->validateMessageOrder($order);
		$this->validateMessageFilter($filter);

		$this->setEndpoint(
			sprintf(
				'/v1/chat/fs/%s/messages',
				$this->getFulfillmentServiceID()
			)
		);

		$queryParams             = [];
		$queryParams['shop_id']  = $shopID;
		$queryParams['page']     = $page;
		$queryParams['per_page'] = $perPage;
		$queryParams['order']    = $order;
		$queryParams['filter']   = $filter;

		return $this->getHttpClient()->request(
			'GET',
			sprintf(
				'%s?%s',
				$this->getEndpoint(),
				http_build_query($queryParams)
			)
		);
	}

	/**
	 * Get list of chat reply.
	 *
	 * @param int $messageID Message ID.
	 * @param int $shopID Shop ID.
	 * @param int $page Current page number.
	 * @param int $perPage How much message showed per page.
	 * @param string $order
	 * @return string
	 * @throws InvalidArgumentException When 'page' parameter less than or equal to zero.
	 * @throws InvalidArgumentException When 'perPage' parameter less than or equal to zero.
	 * @throws InvalidArgumentException When 'order' parameter is invalid.
	 */
	public function getListReply(
		int $messageID,
		int $shopID,
		int $page = 1,
		int $perPage = 10,
		string $order = self::INTERACTION_MESSAGE_ORDER_ASC
	) {
		if ($page <= 0) {
			throw new InvalidArgumentException("'page' parameter must be larger than zero.");
		}

		if ($perPage <= 0) {
			throw new InvalidArgumentException("'perPage' parameter must be larger than zero.");
		}

		$this->validateMessageOrder($order);

		$this->setEndpoint(
			sprintf(
				'/v1/chat/fs/%s/messages/%d/replies',
				$this->getFulfillmentServiceID(),
				$messageID
			)
		);

		$queryParams             = [];
		$queryParams['shop_id']  = $shopID;
		$queryParams['page']     = $page;
		$queryParams['per_page'] = $perPage;
		$queryParams['order']    = $order;

		return $this->getHttpClient()->request(
			'GET',
			sprintf(
				'%s?%s',
				$this->getEndpoint(),
				http_build_query($queryParams)
			)
		);
	}

	/**
	 * Initiate chat with given order ID.
	 *
	 * @param int $orderID Order ID.
	 * @return string
	 */
	public function initiateChat(int $orderID)
	{
		$this->setEndpoint(
			sprintf(
				'/v1/chat/fs/%s/initiate',
				$this->getFulfillmentServiceID()
			)
		);

		$queryParams             = [];
		$queryParams['order_id'] = $orderID;

		return $this->getHttpClient()->request(
			'GET',
			sprintf(
				'%s?%s',
				$this->getEndpoint(),
				http_build_query($queryParams)
			)
		);
	}

	/**
	 * Validate given message order.
	 *
	 * @param string $order
	 * @return void
	 * @throws InvalidArgumentException When 'order' parameter is invalid.
	 */
	private function validateMessageOrder(string $order)
	{
		switch ($order) {
			case self::INTERACTION_MESSAGE_ORDER_ASC:
			case self::INTERACTION_MESSAGE_ORDER_DESC:
			default:
				throw new InvalidArgumentException("Invalid message order.");
		}
	}

	/**
	 * Validate given message filter.
	 *
	 * @param string $filter
	 * @return void
	 * @throws InvalidArgumentException When 'filter' parameter is invalid.
	 */
	private function validateMessageFilter(string $filter)
	{
		switch ($filter) {
			case self::INTERACTION_MESSAGE_FILTER_ALL:
			case self::INTERACTION_MESSAGE_FILTER_READ:
			case self::INTERACTION_MESSAGE_FILTER_UNREAD:
			default:
				throw new InvalidArgumentException("Invalid message filter.");
		}
	}
}