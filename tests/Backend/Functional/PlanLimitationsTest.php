<?php

namespace App\Tests\Backend\Functional;

/**
 * Tests the availability of all of the app's routes.
 */
class PlanLimitationsTest extends AbstractAppTestCase
{
	/**
	 * @dataProvider upgradeRequiredUrlProvider
	 */
	public function testUpgradeRequiredRedirections($userKey, $url, $expectedMessage)
	{
		$this->loadUser($userKey);

		$this->client->followRedirects(false);
		$this->client->request('GET', $url);
		$this->assertResponseRedirects('/account/subscription', 302, "Attempting to use a feature that isn't included in the user's plan redirects to the subscription page.");

		$this->client->followRedirect();
		$this->assertSelectorTextContains('#flash-messages', $expectedMessage, 'When a user is redirected to the subscription page, a message suggesting that they upgrade to get the desired feature is shown.');
	}

	public function upgradeRequiredUrlProvider()
	{
		yield [static::USER_FREE_PLAN, '/project/0YpbRqXLl2/testing', 'Upgrade to a plan that offers automated testing tools to start testing your projects!'];
		yield [static::USER_FREE_PLAN, '/team/create', 'Upgrade to a plan that offers Team Creation to get started with your own team!'];
		yield [static::USER_SOLO_PLAN, '/team/create', 'Upgrade to a plan that offers Team Creation to get started with your own team!'];
	}
}
